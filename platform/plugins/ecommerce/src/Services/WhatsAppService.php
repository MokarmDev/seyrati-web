<?php

namespace Botble\Ecommerce\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $token;
    protected string $senderPhone;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api2.4whatsapp.com/api/Agent_Client_');
        $this->token = config('services.whatsapp.token', '');
        $this->senderPhone = config('services.whatsapp.sender_phone', '');
    }

    /**
     * إرسال OTP عبر WhatsApp
     */
    public function sendOtp(string $recipientPhone, string $otp, ?string $customerName = null): bool
    {
        try {
            // تنسيق رقم الهاتف
            $recipientPhone = $this->formatPhoneNumber($recipientPhone);
            
            // إنشاء الرسالة
            $message = $this->buildOtpMessage($otp, $customerName);
            
            // إرسال الطلب
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'phones' => $this->senderPhone,
                    'doctype' => 'text',
                    'recipient' => $recipientPhone,
                    'message' => $message,
                    'token' => $this->token,
                ]);

            // تسجيل الاستجابة
       
            // التحقق من نجاح الإرسال
            if ($response->successful()) {
                return true;
            }

            // تسجيل الخطأ
         
            return false;

        } catch (\Exception $e) {
          

            return false;
        }
    }

    /**
     * بناء رسالة OTP
     */
    protected function buildOtpMessage(string $otp, ?string $customerName = null): string
    {
        $greeting = $customerName ? "مرحباً *{$customerName}*،\n\n" : "مرحباً،\n\n";
        
        return $greeting . 
               "🔐 *رمز التحقق الخاص بك*\n\n" .
               "*{$otp}*\n\n" .
               "⏱️ الرمز صالح لمدة 10 دقائق\n" .
               "⚠️ لا تشارك هذا الرمز مع أي شخص\n\n" .
               "إذا لم تطلب هذا الرمز، يرجى تجاهل هذه الرسالة.\n\n" .
               "شكراً لك! 🌟";
    }

    /**
     * تنسيق رقم الهاتف
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // إزالة المسافات والشرطات والأقواس
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // إضافة + إذا لم تكن موجودة
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * إرسال رسالة عامة عبر WhatsApp
     */
    public function sendMessage(string $recipientPhone, string $message): bool
    {
        try {
            $recipientPhone = $this->formatPhoneNumber($recipientPhone);
            
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'phones' => $this->senderPhone,
                    'doctype' => 'text',
                    'recipient' => $recipientPhone,
                    'message' => $message,
                    'token' => $this->token,
                ]);

      
            return $response->successful();

        } catch (\Exception $e) {
        
            return false;
        }
    }

    /**
     * إرسال رسالة ترحيب بعد التسجيل الناجح
     */
    public function sendWelcomeMessage(string $recipientPhone, string $customerName): bool
    {
        $message = "🎉 *أهلاً بك في متجرنا!*\n\n" .
                   "عزيزي/عزيزتي *{$customerName}*،\n\n" .
                   "تم تفعيل حسابك بنجاح! ✅\n\n" .
                   "يمكنك الآن:\n" .
                   "🛍️ تصفح المنتجات\n" .
                   "❤️ إضافة المنتجات للمفضلة\n" .
                   "🛒 إتمام عمليات الشراء\n" .
                   "📦 تتبع طلباتك\n\n" .
                   "نتمنى لك تجربة تسوق رائعة! 🌟";

        return $this->sendMessage($recipientPhone, $message);
    }

    /**
     * إرسال رمز إعادة تعيين كلمة المرور عبر WhatsApp
     */
    public function sendPasswordResetOtp(string $recipientPhone, string $otp, ?string $customerName = null): bool
    {
        try {
            $recipientPhone = $this->formatPhoneNumber($recipientPhone);
            
            $greeting = $customerName ? "مرحباً *{$customerName}*،\n\n" : "مرحباً،\n\n";
            
            $message = $greeting .
                       "🔐 *رمز إعادة تعيين كلمة المرور*\n\n" .
                       "*{$otp}*\n\n" .
                       "⏱️ الرمز صالح لمدة 10 دقائق\n" .
                       "⚠️ لا تشارك هذا الرمز مع أي شخص\n\n" .
                       "إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذه الرسالة.\n\n" .
                       "شكراً لك! 🌟";
            
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'phones' => $this->senderPhone,
                    'doctype' => 'text',
                    'recipient' => $recipientPhone,
                    'message' => $message,
                    'token' => $this->token,
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            \Log::error('Failed to send password reset OTP: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * التحقق من تكوين الخدمة
     */
    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->senderPhone);
    }

    /**
     * اختبار الاتصال بالـ API
     */
    public function testConnection(): array
    {
        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post($this->apiUrl, [
                    'phones' => $this->senderPhone,
                    'doctype' => 'text',
                    'recipient' => $this->senderPhone,
                    'message' => 'Test connection from ' . config('app.name'),
                    'token' => $this->token,
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() 
                    ? 'Connection successful' 
                    : 'Connection failed',
                'response' => $response->json(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 0,
                'message' => $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
