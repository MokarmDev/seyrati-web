<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ec_currencies', function (Blueprint $table): void {
            if (!Schema::hasColumn('ec_currencies', 'applied_commission_percentage')) {
                $table->decimal('applied_commission_percentage', 8, 4)->nullable()->after('apply_commission_globally')
                    ->comment('The commission percentage that was last applied to products');
            }
            if (!Schema::hasColumn('ec_currencies', 'commission_applied_at')) {
                $table->timestamp('commission_applied_at')->nullable()->after('applied_commission_percentage')
                    ->comment('When the commission was last applied');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ec_currencies', function (Blueprint $table): void {
            $table->dropColumn(['applied_commission_percentage', 'commission_applied_at']);
        });
    }
};
