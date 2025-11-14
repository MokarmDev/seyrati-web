@php
    $buttonIcon = setting('product_whatsapp_order_button_icon', 'ti ti-brand-whatsapp');
    $whatsappNumber = setting('product_whatsapp_order_phone_number', '');
    $messageTemplate = setting('product_whatsapp_order_message_template',
        "Hi! I'm interested in *{product_name}*\n\nProduct: {product_name}\nSKU: {product_sku}\nPrice: {product_price}\nURL: {product_url}\n\nPlease provide more information about this product."
    );

    if (!empty($whatsappNumber)) {
        // Clean phone number (remove spaces, dashes, etc.)
        $whatsappNumber = preg_replace('/[^0-9+]/', '', $whatsappNumber);

        // Prepare message with product details
        $message = str_replace(
            [
                '{product_name}',
                '{product_sku}',
                '{product_price}',
                '{product_url}',
                '{site_name}'
            ],
            [
                $product->name,
                $product->sku ?? 'N/A',
                format_price($product->price),
                $product->url,
                theme_option('site_title', config('app.name'))
            ],
            $messageTemplate
        );

        // Create WhatsApp URL
        $whatsappUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);
    }
@endphp

@if(!empty($whatsappNumber))
<div class="product-whatsapp-order-wrapper mt-3 mb-3">
    <a href="{{ $whatsappUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="btn btn-success product-whatsapp-order-btn"
       data-product-id="{{ $product->id }}"
       data-product-name="{{ $product->name }}"
       data-product-sku="{{ $product->sku }}">
        @if($buttonIcon)
            {!! BaseHelper::renderIcon($buttonIcon) !!}
        @endif
        <span>{{ trans('plugins/fob-product-whatsapp-order::whatsapp-order.button_text') }}</span>
    </a>
</div>

<style>
.product-whatsapp-order-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background-color: {{ setting('product_whatsapp_order_button_color', '#25D366') }} !important;
    border-color: {{ setting('product_whatsapp_order_button_color', '#25D366') }} !important;
    color: white !important;
    border-radius: {{ setting('product_whatsapp_order_button_radius', 4) }}px !important;
    text-decoration: none;
}

.product-whatsapp-order-btn:hover {
    background-color: {{ setting('product_whatsapp_order_button_hover_color', '#128C7E') }} !important;
    border-color: {{ setting('product_whatsapp_order_button_hover_color', '#128C7E') }} !important;
    color: white !important;
}

.product-whatsapp-order-btn svg {
    width: 20px;
    height: 20px;
    margin: 0;
}
</style>
@endif