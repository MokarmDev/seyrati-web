<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Forms\Fronts\Auth\ForgotPasswordForm;
use Botble\Ecommerce\Services\PasswordResetService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends BaseController
{
    protected PasswordResetService $resetService;

    public function __construct(PasswordResetService $resetService)
    {
        $this->middleware('customer.guest');
        $this->resetService = $resetService;
    }

    /**
     * عرض نموذج طلب إعادة تعيين كلمة المرور
     */
    public function showLinkRequestForm()
    {
        SeoHelper::setTitle(__('Forgot Password'));

        Theme::breadcrumb()
            ->add(__('Login'), route('customer.password.reset'));

        return Theme::scope(
            'ecommerce.customers.passwords.email',
            ['form' => ForgotPasswordForm::create()],
            'plugins/ecommerce::themes.customers.passwords.email'
        )->render();
    }

    /**
     * معالجة طلب إرسال رمز إعادة التعيين
     */
    public function sendResetCode(Request $request)
    {
        // التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ], [
            'phone.required' => __('رقم الهاتف مطلوب'),
            'phone.min' => __('رقم الهاتف غير صحيح'),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // إرسال رمز الـ OTP
        $result = $this->resetService->sendResetCode($request->phone);

        if (!$result['success']) {
            return back()
                ->with('error', $result['message'])
                ->withInput();
        }

        // حفظ بيانات الجلسة
        session([
            'password_reset_phone' => $result['phone'],
            'password_reset_otp_created_at' => now()->timestamp,
        ]);

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.password.reset.otp.verify'))
            ->setMessage($result['message']);
    }
}
