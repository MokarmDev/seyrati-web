<?php

namespace FriendsOfBotble\ProductWhatsAppOrder\Providers;

use Botble\Ecommerce\Models\Product;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, function ($html, $product) {
            if ($product instanceof Product && setting('product_whatsapp_order_enabled', true)) {
                $showForOutOfStock = setting('product_whatsapp_order_show_for_out_of_stock', false);
                $showAlways = setting('product_whatsapp_order_show_always', true);

                if ($showAlways || ($showForOutOfStock && $product->isOutOfStock())) {
                    return $html . view('plugins/fob-product-whatsapp-order::button', compact('product'));
                }
            }

            return $html;
        }, 201, 2); // Priority 201 to appear after request quote button (which has priority 200)
    }
}
