# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-10-02

### Added
- Initial release of FOB Product WhatsApp Order plugin
- One-click WhatsApp chat button on product pages
- Pre-filled message templates with product information
- Dynamic variable support in message templates
- WhatsApp Business number configuration
- Button appearance customization (colors, icons, radius)
- Smart display rules for product visibility
- Out-of-stock product support
- International phone number validation
- Configurable settings panel with multiple options
- Multi-language support with translation files
- Theme-independent styling with inline CSS
- Mobile and desktop compatibility

### Features
- **Message Variables**: {product_name}, {product_sku}, {product_price}, {product_url}, {site_name}
- **Validation**: Phone number validation with international format support
- **Button Customization**: Icon selection, color picker, hover effects
- **Display Rules**: Show always or conditional based on stock status
- **Integration**: Seamless integration with Botble e-commerce products
- **Settings**: WhatsApp number, message template, appearance options

### Technical
- Built on Botble CMS 7.5+
- **Self-contained plugin** - no external dependencies!
- Uses Botble's built-in form builder and validation
- Theme-independent styling with inline CSS
- Hook-based integration for product pages
- Supports WhatsApp Web and mobile app
- CSRF protection and input sanitization

### Requirements
- PHP 8.1+
- Botble CMS 7.5+
- E-commerce plugin activated

### Installation
1. Extract plugin to `platform/plugins/fob-product-whatsapp-order`
2. Activate plugin in admin panel
3. Configure WhatsApp number in Settings > Others > WhatsApp Order

**No composer commands required!** The plugin is completely self-contained.