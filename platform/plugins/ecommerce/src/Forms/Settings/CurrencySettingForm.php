<?php

namespace Botble\Ecommerce\Forms\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Ecommerce\Http\Requests\Settings\CurrencySettingRequest;
use Botble\Ecommerce\Models\Currency;
use Botble\Setting\Forms\SettingForm;

class CurrencySettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScripts(['jquery-ui'])
            ->addScriptsDirectly('vendor/core/plugins/ecommerce/js/currencies.js')
            ->addStylesDirectly('vendor/core/plugins/ecommerce/css/currencies.css');

        $currencies = Currency::query()
            ->orderBy('order')
            ->get()
        ;

        // Get default currency for commission settings
        $defaultCurrency = Currency::query()->where('is_default', 1)->first();

        $this
            ->setSectionTitle(trans('plugins/ecommerce::setting.currency.name'))
            ->setSectionDescription(trans('plugins/ecommerce::setting.currency.currency_setting_description'))
            ->setFormOptions([
                'class' => 'currency-setting-form',
            ])
            ->contentOnly()
            ->setValidatorClass(CurrencySettingRequest::class)
            ->add('enable_auto_detect_visitor_currency', OnOffCheckboxField::class, [
                'label' => trans('plugins/ecommerce::setting.currency.form.enable_auto_detect_visitor_currency'),
                'value' => get_ecommerce_setting('enable_auto_detect_visitor_currency', false),
                'help_block' => [
                    'text' => trans(
                        'plugins/ecommerce::setting.currency.form.auto_detect_visitor_currency_description'
                    ),
                ],
            ])
            ->add('add_space_between_price_and_currency', OnOffCheckboxField::class, [
                'label' => trans('plugins/ecommerce::setting.currency.form.add_space_between_price_and_currency'),
                'value' => get_ecommerce_setting('add_space_between_price_and_currency', false),
            ])
            ->add('thousands_separator', SelectField::class, [
                'label' => trans('plugins/ecommerce::setting.currency.form.thousands_separator'),
                'selected' => get_ecommerce_setting('thousands_separator', ','),
                'choices' => [
                    ',' => trans('plugins/ecommerce::setting.currency.form.separator_comma'),
                    '.' => trans('plugins/ecommerce::setting.currency.form.separator_period'),
                    'space' => trans('plugins/ecommerce::setting.currency.form.separator_space'),
                ],
            ])
            ->add('decimal_separator', SelectField::class, [
                'label' => trans('plugins/ecommerce::setting.currency.form.decimal_separator'),
                'selected' => get_ecommerce_setting('decimal_separator', '.'),
                'choices' => [
                    ',' => trans('plugins/ecommerce::setting.currency.form.separator_comma'),
                    '.' => trans('plugins/ecommerce::setting.currency.form.separator_period'),
                    'space' => trans('plugins/ecommerce::setting.currency.form.separator_space'),
                ],
            ])
            ->add('api_provider_field', HtmlField::class, [
                'html' => view('plugins/ecommerce::settings.partials.currencies.api-provider-fields'),
            ])
            ->add('use_exchange_rate_from_api', 'onOffCheckbox', [
                'label' => trans('plugins/ecommerce::setting.currency.form.use_exchange_rate_from_api'),
                'value' => get_ecommerce_setting('use_exchange_rate_from_api', false),
            ])
            ->add('data_currencies', HtmlField::class, [
                'html' => view(
                    'plugins/ecommerce::settings.partials.currencies.data-currency-fields',
                    compact('currencies')
                ),
            ])
            ->add('currency_table', HtmlField::class, [
                'html' => view('plugins/ecommerce::settings.partials.currencies.currency-table'),
            ])
            ->add(
                'default_currency_warning',
                AlertField::class,
                AlertFieldOption::make()->type('warning')->content(
                    trans('plugins/ecommerce::setting.currency.form.default_currency_warning')
                )
            )
            ->add('commission_section', HtmlField::class, [
                'html' => '<hr><h4 class="mt-4 mb-3">' . trans('plugins/ecommerce::currency.commission_settings') . '</h4>',
            ])
            ->add('commission_percentage', NumberField::class, [
                'label' => trans('plugins/ecommerce::currency.commission_percentage'),
                'help_block' => [
                    'text' => trans('plugins/ecommerce::currency.commission_percentage_help'),
                ],
                'value' => $defaultCurrency ? (float)$defaultCurrency->commission_percentage : 0,
                'attr' => [
                    'placeholder' => '0',
                    'step' => '1',
                    'min' => '0',
                    'max' => '100',
                ],
            ])
            ->add('apply_commission_globally', OnOffCheckboxField::class, [
                'label' => trans('plugins/ecommerce::currency.apply_commission_globally'),
                'help_block' => [
                    'text' => trans('plugins/ecommerce::currency.apply_commission_globally_help'),
                ],
                'value' => $defaultCurrency ? $defaultCurrency->apply_commission_globally : false,
            ])
            ->add('commission_actions', HtmlField::class, [
                'html' => view('plugins/ecommerce::settings.partials.currencies.commission-actions', ['defaultCurrency' => $defaultCurrency]),
            ])
            ->add(
                'commission_info',
                AlertField::class,
                AlertFieldOption::make()->type('info')->content(
                    trans('plugins/ecommerce::currency.commission_info')
                )
            );
    }
}
