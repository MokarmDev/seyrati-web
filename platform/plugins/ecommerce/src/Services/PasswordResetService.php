<?php

namespace Botble\Ecommerce\Services;

use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\PasswordResetOtp;
use Illuminate\Support\Str;

class PasswordResetService
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * إنشاء وإرسال رمز إعادة تعيين كلمة المرور
     */
    public function sendResetCode(string $phone): array
    {
        try {
            // البحث عن العميل برقم الهاتف
            $customer = Customer::where('phone', $phone)->first();

            if (!$customer) {
                return [
                    'success' => false,
                    'message' => __('رقم الهاتف غير مسجل في النظام'),
                ];
            }

            // التحقق من عدم الإرسال المتكرر (منع الإساءة)
            $lastOtp = PasswordResetOtp::where('phone', $phone)
                ->where('is_verified', false)
                ->latest()
                ->first();

            if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
                $remainingSeconds = ceil(60 - $lastOtp->created_at->diffInSeconds(now()));

                return [
                    'success' => false,
                    'message' => __('يرجى الانتظار :seconds ثانية قبل طلب رمز جديد', ['seconds' => $remainingSeconds]),
                ];
            }

            // إنشاء OTP جديد
            $otp = PasswordResetOtp::generateOtp($phone, $customer->id);

            // إرسال OTP عبر WhatsApp
            $sent = $this->whatsappService->sendPasswordResetOtp(
                $phone,
                $otp->otp,
                $customer->name
            );

            if (!$sent) {
                // حذف الـ OTP في حالة الفشل
                $otp->delete();

                return [
                    'success' => false,
                    'message' => __('فشل إرسال الرمز. يرجى المحاولة مرة أخرى'),
                ];
            }

            return [
                'success' => true,
                'message' => __('تم إرسال رمز التحقق إلى WhatsApp'),
                'phone' => $phone,
                'otp_id' => $otp->id,
            ];
        } catch (\Exception $e) {
            \Log::error('Password reset OTP error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => __('حدث خطأ. يرجى المحاولة لاحقاً'),
            ];
        }
    }

    /**
     * التحقق من رمز إعادة التعيين
     */
    public function verifyResetCode(string $phone, string $otp): array
    {
        try {
            $otpRecord = PasswordResetOtp::findValidOtp($phone, $otp);

            if (!$otpRecord) {
                return [
                    'success' => false,
                    'message' => __('رمز التحقق غير صحيح أو منتهي الصلاحية'),
                ];
            }

            // تفعيل الرمز
            $otpRecord->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // إنشاء token مؤقت للمتابعة
            $resetToken = Str::random(60);

            return [
                'success' => true,
                'message' => __('تم التحقق من الرمز بنجاح'),
                'customer_id' => $otpRecord->customer_id,
                'phone' => $phone,
                'reset_token' => $resetToken,
            ];
        } catch (\Exception $e) {
            \Log::error('Password reset verification error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => __('حدث خطأ. يرجى المحاولة لاحقاً'),
            ];
        }
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(int $customerId, string $phone, string $newPassword, string $token): array
    {
        try {
            $customer = Customer::find($customerId);

            if (!$customer) {
                return [
                    'success' => false,
                    'message' => __('العميل غير موجود'),
                ];
            }

            // التحقق من أن الهاتف يطابق
            if ($customer->phone !== $phone) {
                return [
                    'success' => false,
                    'message' => __('بيانات غير متطابقة'),
                ];
            }

            // تحديث كلمة المرور
            $customer->update([
                'password' => bcrypt($newPassword),
            ]);

            // حذف جميع الرموز القديمة
            PasswordResetOtp::where('customer_id', $customerId)->delete();

            return [
                'success' => true,
                'message' => __('تم تحديث كلمة المرور بنجاح'),
            ];
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => __('حدث خطأ. يرجى المحاولة لاحقاً'),
            ];
        }
    }
}
