<?php

use Botble\Base\Facades\AdminHelper;
use Botble\BotbleSitemap\Http\Controllers\SettingsController;
use Botble\BotbleSitemap\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['namespace' => 'Botble\BotbleSitemap\Http\Controllers', 'middleware' => ['web', 'core']], function () {
        Route::group(['prefix' => 'settings/botble-sitemap', 'as' => 'botble-sitemap.'], function () {
            Route::get('/', [SettingsController::class, 'index'])->name('settings');
            Route::put('/', [SettingsController::class, 'update'])->name('settings.update');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        });
    });
});

// Public sitemap routes
Route::group(['namespace' => 'Botble\BotbleSitemap\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
    Route::get('sitemap-{type}-{page}.xml', [SitemapController::class, 'show'])->name('sitemap.show');
});
