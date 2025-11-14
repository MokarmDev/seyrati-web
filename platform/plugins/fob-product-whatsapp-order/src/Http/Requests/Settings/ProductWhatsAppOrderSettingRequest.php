<?php

namespace FriendsOfBotble\ProductWhatsAppOrder\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;

class ProductWhatsAppOrderSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'product_whatsapp_order_phone_number' => ['nullable', 'string', 'regex:/^[+]?[0-9\s\-()]+$/'],
            'product_whatsapp_order_message_template' => ['nullable', 'string', 'max:1000'],
            'product_whatsapp_order_button_radius' => ['nullable', 'numeric', 'min:0', 'max:50'],
        ];
    }
}
