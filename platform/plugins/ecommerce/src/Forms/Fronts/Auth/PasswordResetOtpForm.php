<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth;

use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\Ecommerce\Http\Requests\Fronts\Auth\PasswordResetOtpRequest;

class PasswordResetOtpForm extends AuthForm
{
    public static function formTitle(): string
    {
        return __('Password reset OTP verification form');
    }

    public function setup(): void
    {
        parent::setup();

        $this
            ->setUrl(route('customer.password.reset.otp.verify.post'))
            ->setValidatorClass(PasswordResetOtpRequest::class)
            ->icon('ti ti-lock')
            ->heading(__('التحقق من الرمز'))
            ->description(__('أدخل رمز التحقق المكون من 6 أرقام المرسل إلى واتساب الخاص بك.'))
            ->add(
                'otp',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('رمز التحقق'))
                    ->placeholder(__('أدخل 6 أرقام'))
                    ->icon('ti ti-shield-check')
                    ->addAttribute('maxlength', '6')
                    ->addAttribute('inputmode', 'numeric')
                    ->addAttribute('pattern', '[0-9]{6}')
                    ->addAttribute('dir', 'ltr')
                    ->addAttribute('autocomplete', 'off')
                    ->required()
            )
            ->submitButton(__('تأكيد الرمز'))
            ->add('resend_otp', HtmlField::class, [
                'html' => sprintf(
                    '<div class="mt-3 text-center">
                        <button type="button" onclick="resendOtp(\'%s\')" class="btn btn-link text-decoration-underline p-0" style="border:none; background:none; cursor:pointer; font-size:inherit; color:#0066cc;" id="resendBtn">%s</button>
                        <div id="resendMessage" style="display:none; margin-top:10px; padding:10px; border-radius:4px;" role="alert"></div>
                    </div>
                    <script>
                    let resendCountdown = 0;
                    
                    function resendOtp(url) {
                        const btn = document.getElementById("resendBtn");
                        const msgDiv = document.getElementById("resendMessage");
                        
                        // Disable button during request
                        btn.disabled = true;
                        btn.style.opacity = "0.5";
                        
                        fetch(url, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector("meta[name=\'csrf-token\']")?.content || "",
                                "Content-Type": "application/json"
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            msgDiv.style.display = "block";
                            if (data.success) {
                                msgDiv.style.backgroundColor = "#d4edda";
                                msgDiv.style.color = "#000000";
                                msgDiv.style.borderLeft = "4px solid #28a745";
                            } else {
                                msgDiv.style.backgroundColor = "#f8d7da";
                                msgDiv.style.color = "#000000";
                                msgDiv.style.borderLeft = "4px solid #dc3545";
                            }
                            msgDiv.textContent = data.message || "تم الإرسال";
                            
                            // Start countdown
                            resendCountdown = 60;
                            startCountdown(btn);
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            msgDiv.style.display = "block";
                            msgDiv.style.backgroundColor = "#f8d7da";
                            msgDiv.style.color = "#000000";
                            msgDiv.style.borderLeft = "4px solid #dc3545";
                            msgDiv.textContent = "حدث خطأ في الإرسال";
                            btn.disabled = false;
                            btn.style.opacity = "1";
                        });
                    }
                    
                    function startCountdown(btn) {
                        const originalText = btn.textContent;
                        
                        const interval = setInterval(() => {
                            resendCountdown--;
                            btn.textContent = originalText + " (" + resendCountdown + "s)";
                            
                            if (resendCountdown <= 0) {
                                clearInterval(interval);
                                btn.disabled = false;
                                btn.style.opacity = "1";
                                btn.textContent = originalText;
                            }
                        }, 1000);
                    }
                    </script>',
                    route('customer.password.reset.otp.resend'),
                    __('إعادة إرسال الرمز')
                ),
            ])
            ->add('back_to_login', HtmlField::class, [
                'html' => sprintf(
                    '<div class="mt-2 text-center"><a href="%s" class="text-decoration-underline">%s</a></div>',
                    route('customer.login'),
                    __('العودة لصفحة تسجيل الدخول')
                ),
            ]);
    }
}
