<?php

namespace FriendsOfBotble\SeoMetaKeywords\Providers;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\Forms\SeoForm;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Illuminate\Support\Facades\View;

class SeoMetaKeywordsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/fob-seo-meta-keywords')
            ->loadAndPublishTranslations();

        $this->app->booted(function (): void {
            SeoForm::beforeRendering(function (SeoForm $form): void {
                $meta = $form->getModel();

                $form->remove('meta_keywords');

                $form->addAfter(
                    'seo_meta[seo_description]',
                    'seo_meta[seo_keywords]',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords'))
                        ->placeholder(trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords_placeholder'))
                        ->helperText(trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords_helper'))
                        ->maxLength(255)
                        ->value(old('seo_meta.seo_keywords', $meta['seo_keywords'] ?? ''))
                );
            });

            add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $object): void {
                if (! $object) {
                    return;
                }

                $object->loadMissing('metadata');
                $meta = $object->getMetaData('seo_meta', true);

                if (! empty($meta['seo_keywords'])) {
                    SeoHelper::meta()->addMeta('keywords', $meta['seo_keywords']);
                } else {
                    $globalKeywords = theme_option('seo_keywords');
                    if ($globalKeywords) {
                        SeoHelper::meta()->addMeta('keywords', $globalKeywords);
                    }
                }
            }, 57, 2);

            $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
                theme_option()
                    ->setField([
                        'id' => 'seo_keywords',
                        'section_id' => 'opt-text-subsection-general',
                        'type' => 'text',
                        'label' => trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords'),
                        'attributes' => [
                            'name' => 'seo_keywords',
                            'value' => theme_option('seo_keywords'),
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords_placeholder'),
                                'data-counter' => 255,
                            ],
                        ],
                        'helper' => trans('plugins/fob-seo-meta-keywords::seo-meta-keywords.seo_keywords_helper'),
                    ]);
            });

            View::composer(['packages/theme::partials.header', '*::partials.header'], function (): void {
                $existingMeta = SeoHelper::meta()->render();

                if (! str_contains($existingMeta, 'name="keywords"')) {
                    $globalKeywords = theme_option('seo_keywords');
                    if ($globalKeywords) {
                        SeoHelper::meta()->addMeta('keywords', $globalKeywords);
                    }
                }
            });
        });
    }
}
