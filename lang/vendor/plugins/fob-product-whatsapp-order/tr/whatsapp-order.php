<?php

return [
    'name' => 'WhatsApp Ürün Sipariş',
    'button_text' => 'WhatsApp\'ta Sohbet',
    'settings' => [
        'title' => 'WhatsApp Sipariş',
        'description' => 'Ürünler için WhatsApp sipariş işlevini yapılandır',
    ],

    'setting_title' => 'WhatsApp Sipariş Yapılandırması',
    'general_settings' => 'Genel Ayarlar',
    'enable_whatsapp_order' => 'WhatsApp Siparişi Etkinleştir',
    'enable_whatsapp_order_helper' => 'Etkinleştirildiğinde, WhatsApp sipariş düğmesi ürün sayfalarında görüntülenecek ve müşteriler sohbet başlatabilecek.',

    'whatsapp_phone_number' => 'WhatsApp Telefon Numarası',
    'whatsapp_phone_number_placeholder' => '+901234567890 veya 901234567890',
    'whatsapp_phone_number_helper' => 'Ülke koduyla WhatsApp Business telefon numaranızı girin (örn: +901234567890 veya 901234567890 Türkiye için). Bu, müşterilerin sohbet edeceği numaradır.',

    'message_template' => 'Mesaj Şablonu',
    'message_template_helper' => 'Müşteriler WhatsApp düğmesine tıkladığında görünen önceden doldurulmuş mesajı özelleştirin. Kullanılabilir değişkenler: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. WhatsApp\'ta kalın için *metin* kullanın.',

    'button_settings' => 'Düğme Ayarları',
    'button_icon' => 'Düğme İkonu',
    'button_icon_helper' => 'WhatsApp düğmesinde görüntülenecek bir ikon seçin. Bu, düğmeyi daha tanınabilir hale getirir.',

    'button_color' => 'Düğme Rengi',
    'button_color_helper' => 'WhatsApp düğmesi için arka plan rengini ayarlayın. Varsayılan WhatsApp yeşili (#25D366).',

    'button_hover_color' => 'Düğme Üzerine Gelme Rengi',
    'button_hover_color_helper' => 'WhatsApp düğmesinin üzerine gelindiğinde arka plan rengini ayarlayın. Varsayılan daha koyu WhatsApp yeşili (#128C7E).',

    'button_radius' => 'Düğme Kenarlık Yarıçapı (px)',
    'button_radius_helper' => 'Düğme köşelerinin ne kadar yuvarlak görüneceğini kontrol etmek için kenarlık yarıçapını piksel olarak ayarlayın. Varsayılan 4px.',

    'display_settings' => 'Görüntüleme Ayarları',
    'show_for_out_of_stock' => 'Stokta Olmayan Ürünler İçin Göster',
    'show_for_out_of_stock_helper' => 'Ürünler stokta olmadığında bile WhatsApp düğmesini görüntüle, müşterilerin kullanılabilirlik hakkında bilgi almasına izin ver.',

    'show_always' => 'WhatsApp Düğmesini Her Zaman Göster',
    'show_always_helper' => 'WhatsApp düğmesini tüm ürünlerde göster. Devre dışı bırakıldığında, ürün başına görünürlüğü kontrol edebilirsiniz.',

    'error_no_phone_number' => 'Lütfen ayarlarda bir WhatsApp telefon numarası yapılandırın.',

    // For future features
    'track_clicks' => 'Düğme Tıklamalarını İzle',
    'track_clicks_helper' => 'Müşteriler WhatsApp düğmesine tıkladığında analitik amaçlar için izleyin.',
    'total_clicks' => 'Toplam WhatsApp Tıklaması',
    'clicks_today' => 'Bugünkü Tıklamalar',
    'most_clicked_products' => 'En Çok Tıklanan Ürünler',
];