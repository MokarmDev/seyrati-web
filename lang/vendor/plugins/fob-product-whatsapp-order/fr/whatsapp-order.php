<?php

return [
    'name' => 'Commande Produit WhatsApp',
    'button_text' => 'Discuter sur WhatsApp',
    'settings' => [
        'title' => 'Commande WhatsApp',
        'description' => 'Configurer la fonctionnalité de commande WhatsApp pour les produits',
    ],

    'setting_title' => 'Configuration de Commande WhatsApp',
    'general_settings' => 'Paramètres Généraux',
    'enable_whatsapp_order' => 'Activer la Commande WhatsApp',
    'enable_whatsapp_order_helper' => 'Lorsqu\'activé, le bouton de commande WhatsApp sera affiché sur les pages produits et les clients pourront démarrer une conversation.',

    'whatsapp_phone_number' => 'Numéro de Téléphone WhatsApp',
    'whatsapp_phone_number_placeholder' => '+33123456789 ou 33123456789',
    'whatsapp_phone_number_helper' => 'Entrez votre numéro WhatsApp Business avec le code pays (ex: +33123456789 ou 33123456789 pour la France). C\'est le numéro avec lequel les clients discuteront.',

    'message_template' => 'Modèle de Message',
    'message_template_helper' => 'Personnalisez le message pré-rempli qui apparaît lorsque les clients cliquent sur le bouton WhatsApp. Variables disponibles: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}. Utilisez *texte* pour le gras dans WhatsApp.',

    'button_settings' => 'Paramètres du Bouton',
    'button_icon' => 'Icône du Bouton',
    'button_icon_helper' => 'Sélectionnez une icône à afficher sur le bouton WhatsApp. Cela aide à rendre le bouton plus reconnaissable.',

    'button_color' => 'Couleur du Bouton',
    'button_color_helper' => 'Définissez la couleur de fond pour le bouton WhatsApp. Par défaut vert WhatsApp (#25D366).',

    'button_hover_color' => 'Couleur du Bouton au Survol',
    'button_hover_color_helper' => 'Définissez la couleur de fond lors du survol du bouton WhatsApp. Par défaut vert WhatsApp plus foncé (#128C7E).',

    'button_radius' => 'Rayon de Bordure du Bouton (px)',
    'button_radius_helper' => 'Définissez le rayon de bordure en pixels pour contrôler l\'arrondi des coins du bouton. Par défaut 4px.',

    'display_settings' => 'Paramètres d\'Affichage',
    'show_for_out_of_stock' => 'Afficher pour les Produits en Rupture de Stock',
    'show_for_out_of_stock_helper' => 'Afficher le bouton WhatsApp même lorsque les produits sont en rupture de stock, permettant aux clients de se renseigner sur la disponibilité.',

    'show_always' => 'Toujours Afficher le Bouton WhatsApp',
    'show_always_helper' => 'Afficher le bouton WhatsApp sur tous les produits. Lorsque désactivé, vous pouvez contrôler la visibilité par produit.',

    'error_no_phone_number' => 'Veuillez configurer un numéro de téléphone WhatsApp dans les paramètres.',

    // For future features
    'track_clicks' => 'Suivre les Clics du Bouton',
    'track_clicks_helper' => 'Suivre lorsque les clients cliquent sur le bouton WhatsApp à des fins analytiques.',
    'total_clicks' => 'Total des Clics WhatsApp',
    'clicks_today' => 'Clics Aujourd\'hui',
    'most_clicked_products' => 'Produits les Plus Cliqués',
];