<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerOtp extends BaseModel
{
    protected $table = 'ec_customer_otps';

    protected $fillable = [
        'customer_id',
        'phone',
        'otp',
        'type',
        'is_verified',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * علاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * التحقق من انتهاء الصلاحية
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * التحقق من صلاحية الـ OTP
     */
    public function isValid(): bool
    {
        return !$this->is_verified && !$this->isExpired();
    }

    /**
     * توليد OTP جديد
     */
    public static function generate(Customer $customer, string $type = 'registration'): self
    {
        // حذف OTPs القديمة غير المستخدمة لنفس العميل ونفس النوع
        static::where('customer_id', $customer->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->delete();

        // إنشاء OTP جديد
        return static::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'otp' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10), // صالح لمدة 10 دقائق
        ]);
    }

    /**
     * توليد OTP بدون حساب عميل (للتسجيل المعلق)
     */
    public static function generateWithoutCustomer(string $phone, string $type = 'registration'): self
    {
        // حذف OTPs القديمة غير المستخدمة لنفس الهاتف ونفس النوع
        static::where('phone', $phone)
            ->where('type', $type)
            ->where('is_verified', false)
            ->whereNull('customer_id')
            ->delete();

        // إنشاء OTP جديد بدون customer_id
        return static::create([
            'customer_id' => null,
            'phone' => $phone,
            'otp' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10), // صالح لمدة 10 دقائق
        ]);
    }

    /**
     * التحقق من OTP وتفعيل الحساب
     */
    public function verify(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // تفعيل حساب العميل عند التسجيل
        if ($this->type === 'registration') {
            $this->customer->update([
                'confirmed_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Scope للبحث عن OTP صالح
     */
    public function scopeValid($query)
    {
        return $query->where('is_verified', false)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope للبحث حسب النوع
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
