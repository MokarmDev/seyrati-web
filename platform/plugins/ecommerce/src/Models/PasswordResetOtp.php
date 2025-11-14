<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtp extends BaseModel
{
    protected $table = 'password_reset_otps';

    protected $fillable = [
        'customer_id',
        'phone',
        'otp',
        'is_verified',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * العلاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * التحقق من انتهاء الصلاحية
     */
    public function isExpired(): bool
    {
        return $this->expires_at && Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * إنشاء OTP جديد
     */
    public static function generateOtp(string $phone, ?int $customerId = null, int $expiryMinutes = 10): self
    {
        // حذف الرموز القديمة والمنتهية
        static::where('phone', $phone)
            ->where('is_verified', false)
            ->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return static::create([
            'phone' => $phone,
            'customer_id' => $customerId,
            'otp' => $otp,
            'is_verified' => false,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }

    /**
     * البحث عن OTP بناءً على الهاتف والرمز
     */
    public static function findValidOtp(string $phone, string $otp): ?self
    {
        $record = static::where('phone', $phone)
            ->where('otp', $otp)
            ->where('is_verified', false)
            ->first();

        if ($record && !$record->isExpired()) {
            return $record;
        }

        return null;
    }
}
