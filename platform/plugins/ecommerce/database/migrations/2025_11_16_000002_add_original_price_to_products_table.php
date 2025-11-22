<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ec_products', function (Blueprint $table): void {
            $table->decimal('original_base_price', 15, 2)->nullable()->after('price')
                ->comment('Original price before commission');
            $table->decimal('original_base_sale_price', 15, 2)->nullable()->after('sale_price')
                ->comment('Original sale price before commission');
        });
    }

    public function down(): void
    {
        Schema::table('ec_products', function (Blueprint $table): void {
            $table->dropColumn(['original_base_price', 'original_base_sale_price']);
        });
    }
};
