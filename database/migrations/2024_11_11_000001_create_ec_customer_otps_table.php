<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ec_customer_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('ec_customers')->cascadeOnDelete();
            $table->string('phone', 20);
            $table->string('otp', 6);
            $table->enum('type', ['registration', 'login', 'password_reset'])->default('registration');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['phone', 'otp', 'type']);
            $table->index(['customer_id', 'type', 'is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_customer_otps');
    }
};
