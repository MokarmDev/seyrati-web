<?php

namespace FriendsOfBotble\ProductWhatsAppOrder\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Support\ServiceProvider;

class ProductWhatsAppOrderServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->setNamespace('plugins/fob-product-whatsapp-order')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes();

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('product-whatsapp-order')
                    ->setTitle(trans('plugins/fob-product-whatsapp-order::whatsapp-order.settings.title'))
                    ->withIcon('ti ti-brand-whatsapp')
                    ->withDescription(trans('plugins/fob-product-whatsapp-order::whatsapp-order.settings.description'))
                    ->withPriority(186)
                    ->withRoute('product-whatsapp-order.settings')
            );
        });

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
