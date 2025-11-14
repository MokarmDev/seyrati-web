<?php

namespace Botble\BotbleSitemap\Forms\Settings;

use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\BotbleSitemap\Http\Requests\SitemapSettingsRequest;
use Botble\Setting\Forms\SettingForm;

class SitemapSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/botble-sitemap::sitemap.settings.title'))
            ->setSectionDescription(trans('plugins/botble-sitemap::sitemap.settings.description'))
            ->setValidatorClass(SitemapSettingsRequest::class)
            ->addHtml(sprintf(
                '<div class="mb-3">
                    <button type="button" class="btn btn-warning" id="clear-sitemap-cache-btn" data-url="%s">
                        <i class="ti ti-trash"></i> %s
                    </button>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const clearBtn = document.getElementById("clear-sitemap-cache-btn");
                        if (clearBtn) {
                            clearBtn.addEventListener("click", function() {
                                const url = this.getAttribute("data-url");
                                const btn = this;
                                const originalHtml = btn.innerHTML;

                                btn.disabled = true;
                                btn.innerHTML = \'<i class="ti ti-loader fa-spin"></i> %s\';

                                fetch(url, {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                                        "Accept": "application/json",
                                        "X-Requested-With": "XMLHttpRequest"
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    btn.disabled = false;
                                    btn.innerHTML = originalHtml;

                                    if (data.error) {
                                        Botble.showError(data.message);
                                    } else {
                                        Botble.showSuccess(data.message);
                                    }
                                })
                                .catch(error => {
                                    btn.disabled = false;
                                    btn.innerHTML = originalHtml;
                                    Botble.showError(error.message || "%s");
                                });
                            });
                        }
                    });
                </script>',
                route('botble-sitemap.settings.clear-cache'),
                trans('plugins/botble-sitemap::sitemap.clear_cache'),
                trans('plugins/botble-sitemap::sitemap.clearing_cache'),
                trans('plugins/botble-sitemap::sitemap.error_clearing_cache')
            ))
            ->add(
                'botble_sitemap_enable',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.enable'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.enable_description'))
                    ->defaultValue((bool) setting('botble_sitemap_enable', true))
            )
            ->add(
                'botble_sitemap_cache_duration',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.cache_duration'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.cache_duration_description'))
                    ->value(setting('botble_sitemap_cache_duration', 60))
                    ->toArray()
            )
            ->add(
                'botble_sitemap_include_images',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.include_images'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.include_images_description'))
                    ->defaultValue((bool) setting('botble_sitemap_include_images', false))
            )
            ->add(
                'botble_sitemap_max_urls_per_sitemap',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.max_urls_per_sitemap'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.max_urls_per_sitemap_description'))
                    ->value(setting('botble_sitemap_max_urls_per_sitemap', 10000))
                    ->toArray()
            )
            ->add(
                'botble_sitemap_include_pages',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.include_pages'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.include_pages_description'))
                    ->defaultValue((bool) setting('botble_sitemap_include_pages', true))
            )
            ->add(
                'botble_sitemap_include_products',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.include_products'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.include_products_description'))
                    ->defaultValue((bool) setting('botble_sitemap_include_products', true))
            )
            ->add(
                'botble_sitemap_include_categories',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.include_categories'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.include_categories_description'))
                    ->defaultValue((bool) setting('botble_sitemap_include_categories', true))
            )
            ->add(
                'botble_sitemap_include_posts',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.include_posts'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.include_posts_description'))
                    ->defaultValue((bool) setting('botble_sitemap_include_posts', true))
            )
            ->add(
                'botble_sitemap_multi_language',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/botble-sitemap::sitemap.settings.multi_language'))
                    ->helperText(trans('plugins/botble-sitemap::sitemap.settings.multi_language_description'))
                    ->defaultValue((bool) setting('botble_sitemap_multi_language', false))
            );
    }
}
