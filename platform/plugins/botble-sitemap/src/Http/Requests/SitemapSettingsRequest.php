<?php

namespace Botble\BotbleSitemap\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SitemapSettingsRequest extends Request
{
    public function rules(): array
    {
        return [
            'botble_sitemap_enable' => 'nullable|boolean',
            'botble_sitemap_cache_duration' => 'nullable|integer|min:0',
            'botble_sitemap_include_images' => 'nullable|boolean',
            'botble_sitemap_max_urls_per_sitemap' => 'nullable|integer|min:1|max:50000',
            'botble_sitemap_include_pages' => 'nullable|boolean',
            'botble_sitemap_include_products' => 'nullable|boolean',
            'botble_sitemap_include_categories' => 'nullable|boolean',
            'botble_sitemap_include_posts' => 'nullable|boolean',
            'botble_sitemap_multi_language' => 'nullable|boolean',
        ];
    }
}
