<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth;

use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\PhoneNumberField;
use Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions\PhoneNumberFieldOption;
use Botble\Ecommerce\Http\Requests\Fronts\Auth\ForgotPasswordRequest;

class ForgotPasswordForm extends AuthForm
{
    public static function formTitle(): string
    {
        return __('Customer forgot password form');
    }

    public function setup(): void
    {
        parent::setup();

        $this
            ->setUrl(route('customer.password.request'))
            ->setValidatorClass(ForgotPasswordRequest::class)
            ->icon('ti ti-phone')
            ->heading(__('هل نسيت كلمة المرور؟'))
            ->description(__('هل نسيت كلمة المرور الخاصة بك؟ يرجى إدخال رقم هاتفك. ستتلقى رمز التحقق عبر واتساب لإعادة تعيين كلمة المرور.'))
            ->add(
                'phone',
                PhoneNumberField::class,
                PhoneNumberFieldOption::make()
                    ->label(__('رقم الهاتف'))
                    ->placeholder(__('رقم الهاتف'))
                    ->required()
                    ->addAttribute('autocomplete', 'tel')
                    ->withCountryCodeSelection()
            )
            ->submitButton(__('إرسال رمز التحقق عبر واتساب'))
            ->add('back_to_login', HtmlField::class, [
                'html' => sprintf(
                    '<div class="mt-3 text-center"><a href="%s" class="text-decoration-underline">%s</a></div>',
                    route('customer.login'),
                    __('العودة لصفحة تسجيل الدخول')
                ),
            ]);
    }
}
