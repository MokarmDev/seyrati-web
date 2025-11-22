<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ec_currencies', function (Blueprint $table): void {
            if (!Schema::hasColumn('ec_currencies', 'commission_percentage')) {
                $table->decimal('commission_percentage', 8, 4)->default(0)->after('exchange_rate')
                    ->comment('Commission percentage to be added to all product prices');
            }
            if (!Schema::hasColumn('ec_currencies', 'apply_commission_globally')) {
                $table->boolean('apply_commission_globally')->default(false)->after('commission_percentage')
                    ->comment('Apply commission to all products automatically');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ec_currencies', function (Blueprint $table): void {
            $table->dropColumn(['commission_percentage', 'apply_commission_globally']);
        });
    }
};
