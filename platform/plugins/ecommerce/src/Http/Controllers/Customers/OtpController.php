<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Forms\Fronts\Auth\OtpVerificationForm;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerOtp;
use Botble\Ecommerce\Services\WhatsAppService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OtpController extends BaseController
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('customer.guest');
        $this->whatsappService = $whatsappService;
    }

    /**
     * عرض صفحة إدخال OTP
     */
    public function showVerifyForm()
    {
        // التحقق من وجود بيانات التسجيل المعلقة أو customer_id
        if (!session()->has('pending_registration_data') && !session()->has('otp_customer_id')) {
            return redirect()->route('customer.register')
                ->with('error', __('الجلسة انتهت. يرجى التسجيل مرة أخرى'));
        }

        // التحقق من انتهاء صلاحية الجلسة (30 دقيقة)
        $otpCreatedAt = session('otp_created_at');
        if ($otpCreatedAt && now()->timestamp - $otpCreatedAt > 1800) {
            session()->forget(['pending_registration_data', 'otp_customer_id', 'otp_phone', 'otp_created_at']);
            
            return redirect()->route('customer.register')
                ->with('error', __('انتهت صلاحية الجلسة. يرجى التسجيل مرة أخرى'));
        }

        SeoHelper::setTitle(__('التحقق من رقم الهاتف'));

        Theme::breadcrumb()->add(__('التحقق عبر واتساب'), route('customer.otp.verify'));

        return Theme::scope(
            'ecommerce.customers.otp-verify',
            ['form' => OtpVerificationForm::create()],
            'plugins/ecommerce::themes.customers.otp-verify'
        )->render();
    }

    /**
     * التحقق من OTP
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

        // البحث عن OTP بناءً على الهاتف (للتسجيلات المعلقة)
        $phone = session('otp_phone');
        
        $otpRecord = CustomerOtp::where('phone', $phone)
            ->where('otp', $request->otp)
            ->where('type', 'registration')
            ->where('is_verified', false)
            ->first();

        if (!$otpRecord) {
            // حساب عدد المحاولات الفاشلة
            $attempts = session('otp_failed_attempts', 0) + 1;
            session(['otp_failed_attempts' => $attempts]);

            // حظر بعد 5 محاولات فاشلة
            if ($attempts >= 5) {
                session()->forget(['pending_registration_data', 'otp_customer_id', 'otp_phone', 'otp_created_at', 'otp_failed_attempts']);
                
                return $this->httpResponse()
                    ->setError(__('تم تجاوز الحد الأقصى للمحاولات. يرجى التسجيل مرة أخرى'));
            }

            return $this->httpResponse()
                ->setError(__('رمز التحقق غير صحيح. المحاولات المتبقية: :count', ['count' => 5 - $attempts]));
        }

        // التحقق من انتهاء الصلاحية
        if ($otpRecord->isExpired()) {
            return $this->httpResponse()
                ->setError(__('رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد'));
        }

        // تفعيل OTP
        $otpRecord->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // الآن إنشاء الحساب من البيانات المخزنة في الجلسة
        $registrationData = session('pending_registration_data');
        
        if (!$registrationData) {
            session()->forget(['pending_registration_data', 'otp_customer_id', 'otp_phone', 'otp_created_at', 'otp_failed_attempts']);
            
            return redirect()->route('customer.register')
                ->with('error', __('بيانات التسجيل غير موجودة'));
        }

        // إنشاء الحساب
        $customer = Customer::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'phone' => $registrationData['phone'],
            'password' => Hash::make($registrationData['password']),
            'confirmed_at' => now(), // تفعيل مباشر
        ]);

        // ربط OTP بالعميل الجديد
        $otpRecord->update(['customer_id' => $customer->id]);

        // إطلاق حدث التسجيل
        event(new \Illuminate\Auth\Events\Registered($customer));

        // إرسال رسالة ترحيب
        $this->whatsappService->sendWelcomeMessage(
            $customer->phone,
            $customer->name
        );

        // تسجيل الدخول تلقائيًا
        auth('customer')->login($customer);

        // حذف بيانات الجلسة
        session()->forget(['pending_registration_data', 'otp_customer_id', 'otp_phone', 'otp_created_at', 'otp_failed_attempts']);


        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.overview'))
            ->setMessage(__('تم تفعيل حسابك بنجاح! مرحباً بك 🎉'));
    }

    /**
     * إعادة إرسال OTP
     */
    public function resend()
    {
        $phone = session('otp_phone');
        
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => __('رقم الهاتف غير موجود')
            ], 400);
        }

        // التحقق من عدد المحاولات (منع الإساءة)
        $lastOtp = CustomerOtp::where('phone', $phone)
            ->where('type', 'registration')
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
        $resendCount = session('otp_resend_count', 0);
        if ($resendCount >= 3) {
            session()->forget(['pending_registration_data', 'otp_customer_id', 'otp_phone', 'otp_created_at', 'otp_resend_count']);
        
            return response()->json([
                'success' => false,
                'message' => __('تم تجاوز الحد الأقصى لإعادة الإرسال. يرجى التسجيل مرة أخرى')
            ], 429);
        }

        // إنشاء OTP جديد (بدون customer_id)
        $otp = CustomerOtp::generateWithoutCustomer($phone, 'registration');
        
        // الحصول على الاسم من البيانات المخزنة
        $registrationData = session('pending_registration_data');
        $customerName = $registrationData['name'] ?? 'العميل';
        
        // إرسال OTP عبر WhatsApp
        $sent = $this->whatsappService->sendOtp(
            $phone,
            $otp->otp,
            $customerName
        );

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => __('فشل إرسال رمز التحقق. يرجى المحاولة مرة أخرى')
            ], 400);
        }

        // تحديث عداد إعادة الإرسال
        session(['otp_resend_count' => $resendCount + 1]);

        return response()->json([
            'success' => true,
            'message' => __('تم إرسال رمز التحقق الجديد إلى واتساب')
        ]);
    }
}
