<?php

namespace Botble\BotbleSitemap\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\BotbleSitemap\Services\SitemapService;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class SitemapServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        // Register SitemapService as singleton
        $this->app->singleton(SitemapService::class, function ($app) {
            return new SitemapService();
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/botble-sitemap')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes()
            ->loadAndPublishTranslations();

        // Register settings panel
        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('botble-sitemap')
                    ->setTitle(trans('plugins/botble-sitemap::sitemap.settings.title'))
                    ->withIcon('ti ti-sitemap')
                    ->withPriority(160)
                    ->withDescription(trans('plugins/botble-sitemap::sitemap.settings.description'))
                    ->withRoute('botble-sitemap.settings')
            );
        });
    }
}
