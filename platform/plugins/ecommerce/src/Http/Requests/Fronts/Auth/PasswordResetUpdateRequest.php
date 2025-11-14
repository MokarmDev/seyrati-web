<?php

namespace Botble\Ecommerce\Http\Requests\Fronts\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => __('كلمة المرور مطلوبة'),
            'password.min' => __('كلمة المرور يجب أن تكون 8 أحرف على الأقل'),
            'password.regex' => __('كلمة المرور يجب أن تحتوي على حروف صغيرة وكبيرة وأرقام'),
            'password.confirmed' => __('تأكيد كلمة المرور غير متطابق'),
        ];
    }
}
