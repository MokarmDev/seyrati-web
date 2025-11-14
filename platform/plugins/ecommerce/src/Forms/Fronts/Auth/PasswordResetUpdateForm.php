<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth;

use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\PasswordField;
use Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\Ecommerce\Http\Requests\Fronts\Auth\PasswordResetUpdateRequest;

class PasswordResetUpdateForm extends AuthForm
{
    public static function formTitle(): string
    {
        return __('Password reset update form');
    }

    public function setup(): void
    {
        parent::setup();

        $this
            ->setUrl(route('customer.password.reset.update'))
            ->icon('ti ti-lock')
            ->setValidatorClass(PasswordResetUpdateRequest::class)
            ->heading(__('تعيين كلمة مرور جديدة'))
            ->add(
                'password',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('كلمة المرور الجديدة'))
                    ->placeholder(__('أدخل كلمة المرور الجديدة'))
                    ->icon('ti ti-lock')
            )
            ->add(
                'password_confirmation',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('تأكيد كلمة المرور'))
                    ->placeholder(__('أدخل كلمة المرور مرة أخرى'))
                    ->icon('ti ti-lock')
            )
            ->submitButton(__('تحديث كلمة المرور'))
            ->add('password_help', HtmlField::class, [
                'html' => sprintf(
                    '<small class="form-text text-muted d-block mt-2">%s</small>',
                    __('يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل وتتضمن حروف صغيرة وكبيرة وأرقام')
                ),
            ])
            ->add('back_to_login', HtmlField::class, [
                'html' => sprintf(
                    '<div class="mt-3 text-center"><a href="%s" class="text-decoration-underline">%s</a></div>',
                    route('customer.login'),
                    __('العودة لصفحة تسجيل الدخول')
                ),
            ]);
    }
}
