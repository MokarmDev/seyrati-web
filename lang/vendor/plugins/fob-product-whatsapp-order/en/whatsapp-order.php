<?php

return [
    'name' => 'Product WhatsApp Order',
    'button_text' => 'Chat on WhatsApp',
    'settings' => [
        'title' => 'WhatsApp Order',
        'description' => 'Configure WhatsApp order functionality for products',
    ],

    'setting_title' => 'WhatsApp Order Configuration',
    'general_settings' => 'General Settings',
    'enable_whatsapp_order' => 'Enable WhatsApp Order',
    'enable_whatsapp_order_helper' => 'When enabled, the WhatsApp order button will be displayed on product pages and customers can initiate a chat.',

    'whatsapp_phone_number' => 'WhatsApp Phone Number',
    'whatsapp_phone_number_placeholder' => '+1234567890 or 1234567890',
    'whatsapp_phone_number_helper' => 'Enter your WhatsApp Business phone number with country code (e.g., +1234567890 or 1234567890 for US). This is the number customers will chat with.',

    'message_template' => 'Message Template',
    'message_template_helper' => 'Customize the pre-filled message that appears when customers click the WhatsApp button. Available variables: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. Use *text* for bold in WhatsApp.',

    'button_settings' => 'Button Settings',
    'button_icon' => 'Button Icon',
    'button_icon_helper' => 'Select an icon to display on the WhatsApp button. This helps make the button more recognizable.',

    'button_color' => 'Button Color',
    'button_color_helper' => 'Set the background color for the WhatsApp button. Default is WhatsApp green (#25D366).',

    'button_hover_color' => 'Button Hover Color',
    'button_hover_color_helper' => 'Set the background color when hovering over the WhatsApp button. Default is darker WhatsApp green (#128C7E).',

    'button_radius' => 'Button Border Radius (px)',
    'button_radius_helper' => 'Set the border radius in pixels to control how rounded the button corners appear. Default is 4px.',

    'display_settings' => 'Display Settings',
    'show_for_out_of_stock' => 'Show for Out of Stock Products',
    'show_for_out_of_stock_helper' => 'Display the WhatsApp button even when products are out of stock, allowing customers to inquire about availability.',

    'show_always' => 'Always Show WhatsApp Button',
    'show_always_helper' => 'Show the WhatsApp button on all products. When disabled, you can control visibility per product.',

    'error_no_phone_number' => 'Please configure a WhatsApp phone number in the settings.',

    // For future features
    'track_clicks' => 'Track Button Clicks',
    'track_clicks_helper' => 'Track when customers click the WhatsApp button for analytics purposes.',
    'total_clicks' => 'Total WhatsApp Clicks',
    'clicks_today' => 'Clicks Today',
    'most_clicked_products' => 'Most Clicked Products',
];