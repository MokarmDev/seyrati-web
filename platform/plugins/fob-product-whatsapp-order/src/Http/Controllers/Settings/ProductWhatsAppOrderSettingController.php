<?php

namespace FriendsOfBotble\ProductWhatsAppOrder\Http\Controllers\Settings;

use Botble\Setting\Http\Controllers\SettingController;
use FriendsOfBotble\ProductWhatsAppOrder\Forms\Settings\ProductWhatsAppOrderSettingForm;
use FriendsOfBotble\ProductWhatsAppOrder\Http\Requests\Settings\ProductWhatsAppOrderSettingRequest;

class ProductWhatsAppOrderSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-product-whatsapp-order::whatsapp-order.settings.title'));

        return ProductWhatsAppOrderSettingForm::create()->renderForm();
    }

    public function update(ProductWhatsAppOrderSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
