<?php

namespace Botble\Ecommerce\Http\Requests\Fronts\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('البريد الإلكتروني مطلوب'),
            'email.email' => __('البريد الإلكتروني غير صحيح'),
            'password.required' => __('كلمة المرور مطلوبة'),
            'password.confirmed' => __('تأكيد كلمة المرور غير متطابق'),
            'password.min' => __('كلمة المرور يجب أن تكون 6 أحرف على الأقل'),
        ];
    }
}
