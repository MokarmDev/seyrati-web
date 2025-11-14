<?php

namespace Botble\Ecommerce\Http\Requests\Fronts\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => __('رمز التحقق مطلوب'),
            'otp.size' => __('رمز التحقق يجب أن يكون 6 أرقام'),
            'otp.regex' => __('رمز التحقق يجب أن يحتوي على أرقام فقط'),
        ];
    }
}
