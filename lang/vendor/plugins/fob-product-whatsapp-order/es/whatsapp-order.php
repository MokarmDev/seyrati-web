<?php

return [
    'name' => 'Pedido por WhatsApp de Producto',
    'button_text' => 'Chatear en WhatsApp',
    'settings' => [
        'title' => 'Pedido por WhatsApp',
        'description' => 'Configurar la funcionalidad de pedido por WhatsApp para productos',
    ],

    'setting_title' => 'Configuración de Pedido por WhatsApp',
    'general_settings' => 'Configuración General',
    'enable_whatsapp_order' => 'Habilitar Pedido por WhatsApp',
    'enable_whatsapp_order_helper' => 'Cuando esté habilitado, el botón de pedido por WhatsApp se mostrará en las páginas de productos y los clientes podrán iniciar un chat.',

    'whatsapp_phone_number' => 'Número de WhatsApp',
    'whatsapp_phone_number_placeholder' => '+521234567890 o 521234567890',
    'whatsapp_phone_number_helper' => 'Ingrese su número de WhatsApp Business con código de país (ej: +521234567890 o 521234567890 para México). Este es el número con el que los clientes chatearán.',

    'message_template' => 'Plantilla de Mensaje',
    'message_template_helper' => 'Personalice el mensaje prellenado que aparece cuando los clientes hacen clic en el botón de WhatsApp. Variables disponibles: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. Use *texto* para negrita en WhatsApp.',

    'button_settings' => 'Configuración del Botón',
    'button_icon' => 'Ícono del Botón',
    'button_icon_helper' => 'Seleccione un ícono para mostrar en el botón de WhatsApp. Esto ayuda a que el botón sea más reconocible.',

    'button_color' => 'Color del Botón',
    'button_color_helper' => 'Establezca el color de fondo para el botón de WhatsApp. Por defecto es verde WhatsApp (#25D366).',

    'button_hover_color' => 'Color del Botón al Pasar el Ratón',
    'button_hover_color_helper' => 'Establezca el color de fondo al pasar el ratón sobre el botón de WhatsApp. Por defecto es verde WhatsApp más oscuro (#128C7E).',

    'button_radius' => 'Radio del Borde del Botón (px)',
    'button_radius_helper' => 'Establezca el radio del borde en píxeles para controlar qué tan redondeadas aparecen las esquinas del botón. Por defecto es 4px.',

    'display_settings' => 'Configuración de Visualización',
    'show_for_out_of_stock' => 'Mostrar para Productos Agotados',
    'show_for_out_of_stock_helper' => 'Mostrar el botón de WhatsApp incluso cuando los productos están agotados, permitiendo a los clientes preguntar sobre disponibilidad.',

    'show_always' => 'Siempre Mostrar Botón de WhatsApp',
    'show_always_helper' => 'Mostrar el botón de WhatsApp en todos los productos. Cuando está deshabilitado, puede controlar la visibilidad por producto.',

    'error_no_phone_number' => 'Por favor configure un número de teléfono de WhatsApp en la configuración.',

    // For future features
    'track_clicks' => 'Rastrear Clics del Botón',
    'track_clicks_helper' => 'Rastrear cuando los clientes hacen clic en el botón de WhatsApp con fines analíticos.',
    'total_clicks' => 'Total de Clics en WhatsApp',
    'clicks_today' => 'Clics Hoy',
    'most_clicked_products' => 'Productos Más Clicados',
];