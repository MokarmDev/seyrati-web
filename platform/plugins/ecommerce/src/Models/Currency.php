<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class Currency extends BaseModel
{
    protected $table = 'ec_currencies';

    protected $fillable = [
        'title',
        'symbol',
        'is_prefix_symbol',
        'order',
        'decimals',
        'is_default',
        'exchange_rate',
        // Commission fields
        'commission_percentage',
        'apply_commission_globally',
        'applied_commission_percentage',
        'commission_applied_at',
    ];

    protected $casts = [
        'is_prefix_symbol' => 'boolean',
        'is_default' => 'boolean',
        'exchange_rate' => 'double',
        'commission_percentage' => 'decimal:4',
        'apply_commission_globally' => 'boolean',
        'applied_commission_percentage' => 'decimal:4',
        'commission_applied_at' => 'datetime',
    ];
}
