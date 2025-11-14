<?php

return [
    'name' => 'สั่งสินค้าผ่าน WhatsApp',
    'button_text' => 'แชทบน WhatsApp',
    'settings' => [
        'title' => 'สั่งสินค้า WhatsApp',
        'description' => 'ตั้งค่าฟังก์ชันการสั่งสินค้าผ่าน WhatsApp สำหรับสินค้า',
    ],

    'setting_title' => 'การตั้งค่าการสั่งสินค้า WhatsApp',
    'general_settings' => 'การตั้งค่าทั่วไป',
    'enable_whatsapp_order' => 'เปิดใช้งานการสั่งสินค้า WhatsApp',
    'enable_whatsapp_order_helper' => 'เมื่อเปิดใช้งาน ปุ่มสั่งสินค้า WhatsApp จะแสดงบนหน้าสินค้าและลูกค้าสามารถเริ่มการสนทนาได้',

    'whatsapp_phone_number' => 'หมายเลขโทรศัพท์ WhatsApp',
    'whatsapp_phone_number_placeholder' => '+66123456789 หรือ 66123456789',
    'whatsapp_phone_number_helper' => 'ป้อนหมายเลข WhatsApp Business ของคุณพร้อมรหัสประเทศ (เช่น +66123456789 หรือ 66123456789 สำหรับประเทศไทย) นี่คือหมายเลขที่ลูกค้าจะแชทด้วย',

    'message_template' => 'เทมเพลตข้อความ',
    'message_template_helper' => 'ปรับแต่งข้อความที่กรอกไว้ล่วงหน้าที่ปรากฏเมื่อลูกค้าคลิกปุ่ม WhatsApp ตัวแปรที่ใช้ได้: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name} ใช้ *ข้อความ* สำหรับตัวหนาใน WhatsApp',

    'button_settings' => 'การตั้งค่าปุ่ม',
    'button_icon' => 'ไอคอนปุ่ม',
    'button_icon_helper' => 'เลือกไอคอนเพื่อแสดงบนปุ่ม WhatsApp ซึ่งช่วยให้ปุ่มเป็นที่จดจำมากขึ้น',

    'button_color' => 'สีปุ่ม',
    'button_color_helper' => 'ตั้งค่าสีพื้นหลังสำหรับปุ่ม WhatsApp ค่าเริ่มต้นคือสีเขียว WhatsApp (#25D366)',

    'button_hover_color' => 'สีปุ่มเมื่อชี้เมาส์',
    'button_hover_color_helper' => 'ตั้งค่าสีพื้นหลังเมื่อชี้เมาส์ไปที่ปุ่ม WhatsApp ค่าเริ่มต้นคือสีเขียว WhatsApp เข้มขึ้น (#128C7E)',

    'button_radius' => 'รัศมีขอบปุ่ม (px)',
    'button_radius_helper' => 'ตั้งค่ารัศมีขอบเป็นพิกเซลเพื่อควบคุมความโค้งมนของมุมปุ่ม ค่าเริ่มต้นคือ 4px',

    'display_settings' => 'การตั้งค่าการแสดงผล',
    'show_for_out_of_stock' => 'แสดงสำหรับสินค้าหมดสต็อก',
    'show_for_out_of_stock_helper' => 'แสดงปุ่ม WhatsApp แม้ว่าสินค้าจะหมดสต็อก ทำให้ลูกค้าสามารถสอบถามเกี่ยวกับความพร้อม',

    'show_always' => 'แสดงปุ่ม WhatsApp เสมอ',
    'show_always_helper' => 'แสดงปุ่ม WhatsApp บนสินค้าทั้งหมด เมื่อปิดการใช้งาน คุณสามารถควบคุมการมองเห็นต่อสินค้า',

    'error_no_phone_number' => 'กรุณาตั้งค่าหมายเลขโทรศัพท์ WhatsApp ในการตั้งค่า',

    // For future features
    'track_clicks' => 'ติดตามการคลิกปุ่ม',
    'track_clicks_helper' => 'ติดตามเมื่อลูกค้าคลิกปุ่ม WhatsApp เพื่อวัตถุประสงค์ในการวิเคราะห์',
    'total_clicks' => 'จำนวนคลิก WhatsApp ทั้งหมด',
    'clicks_today' => 'คลิกวันนี้',
    'most_clicked_products' => 'สินค้าที่คลิกมากที่สุด',
];