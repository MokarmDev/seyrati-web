<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Forms\Fronts\Auth\PasswordResetUpdateForm;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Services\PasswordResetService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class PasswordResetUpdateController extends BaseController
{
    protected PasswordResetService $resetService;

    public function __construct(PasswordResetService $resetService)
    {
        $this->middleware('customer.guest');
        $this->resetService = $resetService;
    }

    /**
     * عرض نموذج إدخال كلمة المرور الجديدة
     */
    public function showForm()
    {
        // التحقق من التحقق السابق من OTP
        if (!session()->has('password_reset_verified') || !session('password_reset_verified')) {
            return redirect()->route('customer.password.request')
                ->with('error', __('الجلسة انتهت. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
        }

        SeoHelper::setTitle(__('Set New Password'));
        Theme::breadcrumb()->add(__('Set New Password'), route('customer.password.reset.form'));

        $form = PasswordResetUpdateForm::create();

        return Theme::scope(
            'ecommerce.customers.passwords.reset',
            ['form' => $form],
            'plugins/ecommerce::themes.customers.passwords.reset'
        )->render();
    }

    /**
     * تحديث كلمة المرور
     */
    public function update(Request $request)
    {
        // التحقق من التحقق السابق من OTP
        if (!session()->has('password_reset_verified') || !session('password_reset_verified')) {
            return redirect()->route('customer.password.request')
                ->with('error', __('الجلسة انتهت. يرجى محاولة إعادة تعيين كلمة المرور مرة أخرى'));
        }

        $customerId = session('password_reset_customer_id');
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            session()->forget([
                'password_reset_token',
                'password_reset_customer_id',
                'password_reset_verified',
            ]);

            return redirect()->route('customer.login')
                ->with('error', __('حدث خطأ. يرجى المحاولة مرة أخرى'));
        }

        // تحديث كلمة المرور
        $result = $this->resetService->resetPassword(
            $customerId,
            $customer->phone,
            $request->password,
            session('password_reset_token')
        );

        if (!$result['success']) {
            return back()
                ->with('error', $result['message']);
        }

        // حذف بيانات الجلسة
        session()->forget([
            'password_reset_token',
            'password_reset_customer_id',
            'password_reset_verified',
        ]);

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.login'))
            ->setMessage(__('تم تحديث كلمة المرور بنجاح! يرجى تسجيل الدخول بكلمتك الجديدة'));
    }
}
