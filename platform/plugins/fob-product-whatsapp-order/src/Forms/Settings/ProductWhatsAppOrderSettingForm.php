<?php

namespace FriendsOfBotble\ProductWhatsAppOrder\Forms\Settings;

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\ProductWhatsAppOrder\Http\Requests\Settings\ProductWhatsAppOrderSettingRequest;

class ProductWhatsAppOrderSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-product-whatsapp-order::whatsapp-order.setting_title'))
            ->setSectionDescription(trans('plugins/fob-product-whatsapp-order::whatsapp-order.settings.description'))
            ->setValidatorClass(ProductWhatsAppOrderSettingRequest::class)
            ->add(
                'product_whatsapp_order_enabled',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.enable_whatsapp_order'))
                    ->value(setting('product_whatsapp_order_enabled', true))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.enable_whatsapp_order_helper'))
            )
            ->add(
                'product_whatsapp_order_phone_number',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.whatsapp_phone_number'))
                    ->value(setting('product_whatsapp_order_phone_number', ''))
                    ->placeholder(trans('plugins/fob-product-whatsapp-order::whatsapp-order.whatsapp_phone_number_placeholder'))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.whatsapp_phone_number_helper'))
                    ->required()
            )
            ->add(
                'product_whatsapp_order_message_template',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.message_template'))
                    ->value(setting(
                        'product_whatsapp_order_message_template',
                        "Hi! I'm interested in *{product_name}*\n\nProduct: {product_name}\nSKU: {product_sku}\nPrice: {product_price}\nURL: {product_url}\n\nPlease provide more information about this product."
                    ))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.message_template_helper'))
                    ->rows(8)
            )
            ->add(
                'product_whatsapp_order_button_icon',
                CoreIconField::class,
                CoreIconFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_icon'))
                    ->value(setting('product_whatsapp_order_button_icon', 'ti ti-brand-whatsapp'))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_icon_helper'))
            )
            ->add(
                'product_whatsapp_order_button_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_color'))
                    ->value(setting('product_whatsapp_order_button_color', '#25D366'))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_color_helper'))
            )
            ->add(
                'product_whatsapp_order_button_hover_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_hover_color'))
                    ->value(setting('product_whatsapp_order_button_hover_color', '#128C7E'))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_hover_color_helper'))
            )
            ->add(
                'product_whatsapp_order_button_radius',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_radius'))
                    ->value(setting('product_whatsapp_order_button_radius', 4))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_radius_helper'))
            )
            ->add(
                'product_whatsapp_order_show_for_out_of_stock',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.show_for_out_of_stock'))
                    ->value(setting('product_whatsapp_order_show_for_out_of_stock', false))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.show_for_out_of_stock_helper'))
            )
            ->add(
                'product_whatsapp_order_show_always',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-product-whatsapp-order::whatsapp-order.show_always'))
                    ->value(setting('product_whatsapp_order_show_always', true))
                    ->helperText(trans('plugins/fob-product-whatsapp-order::whatsapp-order.show_always_helper'))
            );
    }
}
