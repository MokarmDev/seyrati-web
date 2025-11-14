<?php

namespace Botble\Ecommerce\Http\Requests\Fronts\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('رقم الهاتف مطلوب'),
            'phone.min' => __('رقم الهاتف غير صحيح'),
        ];
    }
}
