<?php

return [
    'name' => 'Đặt hàng qua WhatsApp',
    'button_text' => 'Chat trên WhatsApp',
    'settings' => [
        'title' => 'Đặt hàng WhatsApp',
        'description' => 'Cấu hình chức năng đặt hàng qua WhatsApp cho sản phẩm',
    ],

    'setting_title' => 'Cấu hình đặt hàng WhatsApp',
    'general_settings' => 'Cài đặt chung',
    'enable_whatsapp_order' => 'Bật đặt hàng WhatsApp',
    'enable_whatsapp_order_helper' => 'Khi bật, nút đặt hàng WhatsApp sẽ được hiển thị trên trang sản phẩm và khách hàng có thể bắt đầu trò chuyện.',

    'whatsapp_phone_number' => 'Số điện thoại WhatsApp',
    'whatsapp_phone_number_placeholder' => '+84123456789 hoặc 84123456789',
    'whatsapp_phone_number_helper' => 'Nhập số điện thoại WhatsApp Business của bạn với mã quốc gia (ví dụ: +84123456789 hoặc 84123456789 cho Việt Nam). Đây là số khách hàng sẽ chat.',

    'message_template' => 'Mẫu tin nhắn',
    'message_template_helper' => 'Tùy chỉnh tin nhắn được điền sẵn khi khách hàng nhấp vào nút WhatsApp. Các biến có sẵn: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. Sử dụng *text* cho chữ in đậm trong WhatsApp.',

    'button_settings' => 'Cài đặt nút',
    'button_icon' => 'Biểu tượng nút',
    'button_icon_helper' => 'Chọn biểu tượng để hiển thị trên nút WhatsApp. Điều này giúp nút dễ nhận biết hơn.',

    'button_color' => 'Màu nút',
    'button_color_helper' => 'Đặt màu nền cho nút WhatsApp. Mặc định là màu xanh WhatsApp (#25D366).',

    'button_hover_color' => 'Màu nút khi di chuột',
    'button_hover_color_helper' => 'Đặt màu nền khi di chuột qua nút WhatsApp. Mặc định là màu xanh WhatsApp đậm hơn (#128C7E).',

    'button_radius' => 'Bo góc nút (px)',
    'button_radius_helper' => 'Đặt bán kính bo góc bằng pixel để kiểm soát độ bo tròn của góc nút. Mặc định là 4px.',

    'display_settings' => 'Cài đặt hiển thị',
    'show_for_out_of_stock' => 'Hiển thị cho sản phẩm hết hàng',
    'show_for_out_of_stock_helper' => 'Hiển thị nút WhatsApp ngay cả khi sản phẩm hết hàng, cho phép khách hàng hỏi về tình trạng có hàng.',

    'show_always' => 'Luôn hiển thị nút WhatsApp',
    'show_always_helper' => 'Hiển thị nút WhatsApp trên tất cả sản phẩm. Khi tắt, bạn có thể kiểm soát hiển thị cho từng sản phẩm.',

    'error_no_phone_number' => 'Vui lòng cấu hình số điện thoại WhatsApp trong cài đặt.',

    // For future features
    'track_clicks' => 'Theo dõi nhấp chuột nút',
    'track_clicks_helper' => 'Theo dõi khi khách hàng nhấp vào nút WhatsApp cho mục đích phân tích.',
    'total_clicks' => 'Tổng số nhấp WhatsApp',
    'clicks_today' => 'Nhấp chuột hôm nay',
    'most_clicked_products' => 'Sản phẩm được nhấp nhiều nhất',
];