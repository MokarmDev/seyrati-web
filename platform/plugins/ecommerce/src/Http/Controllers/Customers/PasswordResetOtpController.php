<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Forms\Fronts\Auth\PasswordResetOtpForm;
use Botble\Ecommerce\Models\PasswordResetOtp;
use Botble\Ecommerce\Services\PasswordResetService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordResetOtpController extends BaseController
{
    protected PasswordResetService $resetService;

    public function __construct(PasswordResetService $resetService)
    {
        $this->middleware('customer.guest');
        $this->resetService = $resetService;
    }

    /**
     * عرض صفحة التحقق من رمز إعادة التعيين
     */
    public function showVerifyForm()
    {
        // التحقق من وجود رقم الهاتف في الجلسة
        if (!session()->has('password_reset_phone')) {
            return redirect()->route('customer.password.request')
                ->with('error', __('الجلسة انتهت. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
        }

        // التحقق من انتهاء صلاحية الجلسة (30 دقيقة)
        $otpCreatedAt = session('password_reset_otp_created_at');
        if ($otpCreatedAt && now()->timestamp - $otpCreatedAt > 1800) {
            session()->forget(['password_reset_phone', 'password_reset_otp_created_at']);
            
            return redirect()->route('customer.password.request')
                ->with('error', __('انتهت صلاحية الجلسة. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
        }

        SeoHelper::setTitle(__('إعادة تعيين كلمة المرور'));
        Theme::breadcrumb()->add(__('إعادة تعيين كلمة المرور'), route('customer.password.reset.otp.verify'));

        // استخدام نفس تصميم الواجهة الموجودة
        return Theme::scope(
            'ecommerce.customers.otp-verify',
            ['phone' => session('password_reset_phone'), 'form' => PasswordResetOtpForm::create()],
            'plugins/ecommerce::themes.customers.otp-verify'
        )->render();
    }

    /**
     * التحقق من رمز إعادة التعيين
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => __('رمز التحقق مطلوب'),
            'otp.size' => __('رمز التحقق يجب أن يكون 6 أرقام'),
            'otp.regex' => __('رمز التحقق يجب أن يحتوي على أرقام فقط'),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $phone = session('password_reset_phone');

        if (!$phone) {
            session()->forget(['password_reset_phone', 'password_reset_otp_created_at']);

            return redirect()->route('customer.password.request')
                ->with('error', __('الجلسة انتهت. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
        }

        // التحقق من رمز الـ OTP
        $result = $this->resetService->verifyResetCode($phone, $request->otp);

        if (!$result['success']) {
            // حساب عدد المحاولات الفاشلة
            $attempts = session('password_reset_failed_attempts', 0) + 1;
            session(['password_reset_failed_attempts' => $attempts]);

            // حظر بعد 5 محاولات فاشلة
            if ($attempts >= 5) {
                session()->forget([
                    'password_reset_phone',
                    'password_reset_otp_created_at',
                    'password_reset_failed_attempts'
                ]);
                
                return $this->httpResponse()
                    ->setError(__('تم تجاوز الحد الأقصى للمحاولات. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
            }

            return $this->httpResponse()
                ->setError(__('رمز التحقق غير صحيح. المحاولات المتبقية: :count', ['count' => 5 - $attempts]));
        }

        // حفظ البيانات اللازمة للخطوة التالية
        session([
            'password_reset_token' => $result['reset_token'],
            'password_reset_customer_id' => $result['customer_id'],
            'password_reset_verified' => true,
        ]);

        // حذف بيانات الـ OTP
        session()->forget([
            'password_reset_failed_attempts',
            'password_reset_phone',
            'password_reset_otp_created_at',
        ]);

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.password.reset.form'))
            ->setMessage(__('تم التحقق من الرمز بنجاح! 🎉'));
    }

    /**
     * إعادة إرسال رمز التحقق
     */
    public function resend()
    {
        $phone = session('password_reset_phone');

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => __('الجلسة انتهت. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى')
            ], 400);
        }

        // التحقق من عدد المحاولات (منع الإساءة)
        $lastOtp = PasswordResetOtp::where('phone', $phone)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
            $remainingSeconds = ceil(60 - $lastOtp->created_at->diffInSeconds(now()));

            return response()->json([
                'success' => false,
                'message' => __('يرجى الانتظار :seconds ثانية قبل طلب رمز جديد', ['seconds' => $remainingSeconds])
            ], 429);
        }

        // حساب عدد محاولات إعادة الإرسال
        $resendCount = session('password_reset_resend_count', 0);
        if ($resendCount >= 3) {
            session()->forget([
                'password_reset_phone',
                'password_reset_otp_created_at',
                'password_reset_resend_count',
                'password_reset_failed_attempts'
            ]);

            return response()->json([
                'success' => false,
                'message' => __('تم تجاوز الحد الأقصى لإعادة الإرسال. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى')
            ], 429);
        }

        // إرسال رمز جديد
        $result = $this->resetService->sendResetCode($phone);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // تحديث عداد إعادة الإرسال
        session(['password_reset_resend_count' => $resendCount + 1]);

        return response()->json([
            'success' => true,
            'message' => __('تم إرسال رمز تحقق جديد إلى واتساب')
        ]);
    }
}
