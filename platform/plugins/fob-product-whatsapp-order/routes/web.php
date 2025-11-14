<?php

use Botble\Base\Facades\AdminHelper;
use FriendsOfBotble\ProductWhatsAppOrder\Http\Controllers\Settings\ProductWhatsAppOrderSettingController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'product-whatsapp-order', 'as' => 'product-whatsapp-order.'], function (): void {
        Route::get('settings', [ProductWhatsAppOrderSettingController::class, 'edit'])->name('settings');
        Route::put('settings', [ProductWhatsAppOrderSettingController::class, 'update'])->name('settings.update');
    });
});