# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-10-24

### Added
- Initial release of FOB SEO Meta Keywords plugin
- Meta keywords field in SEO meta box for individual content
- Global meta keywords field in Theme Options
- Automatic meta keywords tag rendering in HTML head
- Dynamic keyword replacement in frontend
- Multi-language support with 42 translations
- Smart priority system (page-specific > global keywords)
- Comprehensive warning about meta keywords ineffectiveness

### Features
- **SEO Meta Box Integration**: Add keywords to posts, pages, and other content
- **Theme Options Integration**: Set global fallback keywords
- **Priority System**: Page-specific keywords override global keywords
- **Multi-language**: 42 language translations included
- **Warning System**: Clear warnings about SEO ineffectiveness
- **No Database Changes**: Uses existing meta_boxes and settings tables

### Technical
- Built on Botble CMS 7.2.6+
- **Self-contained plugin** - no external dependencies!
- Uses `FormAbstract::beforeRendering()` hook for SEO form integration
- Uses `RenderingThemeOptionSettings` event for theme options
- Hook-based integration with existing SEO Helper package
- PSR-12 code standards
- Clean, maintainable codebase

### Requirements
- PHP 8.2+
- Botble CMS 7.2.6+
- SEO Helper package activated

### Installation
1. Extract plugin to `platform/plugins/fob-seo-meta-keywords`
2. Activate plugin in admin panel
3. Configure global keywords in Appearance > Theme Options (optional)
4. Add page-specific keywords in content SEO meta boxes

**No composer commands required!** The plugin is completely self-contained.

### Important Notes
- ⚠️ Meta keywords are NOT used by Google, Bing, or other major search engines
- Only use for legacy systems, internal search, or specific non-SEO requirements
- For actual SEO, focus on quality content, proper headings, and meta descriptions
- Learn more: https://yoast.com/meta-keywords/
