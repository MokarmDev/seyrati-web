<?php

return [
    'name' => 'Pesan Produk WhatsApp',
    'button_text' => 'Chat di WhatsApp',
    'settings' => [
        'title' => 'Pesan WhatsApp',
        'description' => 'Konfigurasi fungsi pemesanan WhatsApp untuk produk',
    ],

    'setting_title' => 'Konfigurasi Pesan WhatsApp',
    'general_settings' => 'Pengaturan Umum',
    'enable_whatsapp_order' => 'Aktifkan Pesan WhatsApp',
    'enable_whatsapp_order_helper' => 'Saat diaktifkan, tombol pesan WhatsApp akan ditampilkan di halaman produk dan pelanggan dapat memulai obrolan.',

    'whatsapp_phone_number' => 'Nomor Telepon WhatsApp',
    'whatsapp_phone_number_placeholder' => '+62123456789 atau 62123456789',
    'whatsapp_phone_number_helper' => 'Masukkan nomor WhatsApp Business Anda dengan kode negara (contoh: +62123456789 atau 62123456789 untuk Indonesia). Ini adalah nomor yang akan dihubungi pelanggan.',

    'message_template' => 'Template Pesan',
    'message_template_helper' => 'Sesuaikan pesan yang telah diisi sebelumnya yang muncul saat pelanggan mengklik tombol WhatsApp. Variabel tersedia: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. Gunakan *teks* untuk tebal di WhatsApp.',

    'button_settings' => 'Pengaturan Tombol',
    'button_icon' => 'Ikon Tombol',
    'button_icon_helper' => 'Pilih ikon untuk ditampilkan pada tombol WhatsApp. Ini membantu membuat tombol lebih mudah dikenali.',

    'button_color' => 'Warna Tombol',
    'button_color_helper' => 'Atur warna latar belakang untuk tombol WhatsApp. Default adalah hijau WhatsApp (#25D366).',

    'button_hover_color' => 'Warna Tombol saat Hover',
    'button_hover_color_helper' => 'Atur warna latar belakang saat mengarahkan kursor ke tombol WhatsApp. Default adalah hijau WhatsApp lebih gelap (#128C7E).',

    'button_radius' => 'Radius Batas Tombol (px)',
    'button_radius_helper' => 'Atur radius batas dalam piksel untuk mengontrol seberapa bulat sudut tombol muncul. Default adalah 4px.',

    'display_settings' => 'Pengaturan Tampilan',
    'show_for_out_of_stock' => 'Tampilkan untuk Produk Habis Stok',
    'show_for_out_of_stock_helper' => 'Tampilkan tombol WhatsApp bahkan ketika produk habis stok, memungkinkan pelanggan untuk menanyakan ketersediaan.',

    'show_always' => 'Selalu Tampilkan Tombol WhatsApp',
    'show_always_helper' => 'Tampilkan tombol WhatsApp di semua produk. Saat dinonaktifkan, Anda dapat mengontrol visibilitas per produk.',

    'error_no_phone_number' => 'Silakan konfigurasi nomor telepon WhatsApp di pengaturan.',

    // For future features
    'track_clicks' => 'Lacak Klik Tombol',
    'track_clicks_helper' => 'Lacak saat pelanggan mengklik tombol WhatsApp untuk tujuan analitis.',
    'total_clicks' => 'Total Klik WhatsApp',
    'clicks_today' => 'Klik Hari Ini',
    'most_clicked_products' => 'Produk Paling Banyak Diklik',
];