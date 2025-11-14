<?php

namespace Botble\BotbleSitemap\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Blog\Models\Post;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Language\Facades\Language;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    protected string $cachePrefix = 'botble_sitemap_';

    protected int $maxUrlsPerSitemap = 10000;

    public function __construct()
    {
        $this->maxUrlsPerSitemap = (int) setting('botble_sitemap_max_urls_per_sitemap', 10000);
    }

    public function generateSitemapIndex(): string
    {
        if (! setting('botble_sitemap_enable', true)) {
            return $this->buildEmptySitemap();
        }

        $cacheDuration = (int) setting('botble_sitemap_cache_duration', 60);

        if ($cacheDuration > 0) {
            return Cache::remember($this->cachePrefix . 'index', $cacheDuration * 60, function () {
                return $this->buildSitemapIndex();
            });
        }

        return $this->buildSitemapIndex();
    }

    public function generateSitemap(string $type, int $page = 1): string
    {
        if (! setting('botble_sitemap_enable', true)) {
            return $this->buildEmptySitemap();
        }

        $cacheDuration = (int) setting('botble_sitemap_cache_duration', 60);
        $cacheKey = $this->cachePrefix . $type . '_page_' . $page;

        if ($cacheDuration > 0) {
            return Cache::remember($cacheKey, $cacheDuration * 60, function () use ($type, $page) {
                return $this->buildSitemap($type, $page);
            });
        }

        return $this->buildSitemap($type, $page);
    }

    protected function buildSitemapIndex(): string
    {
        $sitemaps = $this->getSitemapTypes();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemaps as $sitemap) {
            $totalUrls = $sitemap['count'];
            $totalPages = (int) ceil($totalUrls / $this->maxUrlsPerSitemap);

            if ($totalPages === 0) {
                $totalPages = 1;
            }

            for ($page = 1; $page <= $totalPages; $page++) {
                $xml .= '  <sitemap>' . PHP_EOL;
                $xml .= '    <loc>' . route('sitemap.show', ['type' => $sitemap['type'], 'page' => $page]) . '</loc>' . PHP_EOL;
                $xml .= '    <lastmod>' . now()->toIso8601String() . '</lastmod>' . PHP_EOL;
                $xml .= '  </sitemap>' . PHP_EOL;
            }
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    protected function buildSitemap(string $type, int $page): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        if (setting('botble_sitemap_include_images', false)) {
            $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }

        if (setting('botble_sitemap_multi_language', false)) {
            $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }

        $xml .= '>' . PHP_EOL;

        // Get URLs for this sitemap type and page
        $urls = $this->getUrlsForType($type, $page);

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;

            // Multi-language support with hreflang
            if (isset($url['alternates']) && setting('botble_sitemap_multi_language', false)) {
                foreach ($url['alternates'] as $alternate) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="' . $alternate['lang'] . '" href="' . htmlspecialchars($alternate['url']) . '" />' . PHP_EOL;
                }
            }

            // Image support
            if (isset($url['images']) && setting('botble_sitemap_include_images', false)) {
                foreach ($url['images'] as $image) {
                    $xml .= '    <image:image>' . PHP_EOL;
                    $xml .= '      <image:loc>' . htmlspecialchars($image) . '</image:loc>' . PHP_EOL;
                    $xml .= '    </image:image>' . PHP_EOL;
                }
            }

            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    protected function buildEmptySitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $xml .= '</urlset>';

        return $xml;
    }

    protected function getSitemapTypes(): array
    {
        $types = [];

        if (setting('botble_sitemap_include_pages', true)) {
            $types[] = [
                'type' => 'pages',
                'name' => 'Static Pages',
                'count' => $this->countPages(),
            ];
        }

        if (setting('botble_sitemap_include_products', true) && class_exists(Product::class)) {
            $types[] = [
                'type' => 'products',
                'name' => 'Products',
                'count' => $this->countProducts(),
            ];
        }

        if (setting('botble_sitemap_include_categories', true) && class_exists(ProductCategory::class)) {
            $types[] = [
                'type' => 'categories',
                'name' => 'Categories',
                'count' => $this->countCategories(),
            ];
        }

        if (setting('botble_sitemap_include_posts', true) && class_exists(Post::class)) {
            $types[] = [
                'type' => 'posts',
                'name' => 'Blog Posts',
                'count' => $this->countPosts(),
            ];
        }

        return $types;
    }

    protected function getUrlsForType(string $type, int $page): array
    {
        $offset = ($page - 1) * $this->maxUrlsPerSitemap;

        return match ($type) {
            'pages' => $this->getUrlsForPages($offset),
            'products' => $this->getUrlsForProducts($offset),
            'categories' => $this->getUrlsForCategories($offset),
            'posts' => $this->getUrlsForPosts($offset),
            default => [],
        };
    }

    protected function countPages(): int
    {
        if (! class_exists(Page::class)) {
            return 0;
        }

        return Page::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->count();
    }

    protected function countProducts(): int
    {
        return Product::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('is_variation', false)
            ->count();
    }

    protected function countCategories(): int
    {
        return ProductCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->count();
    }

    protected function countPosts(): int
    {
        return Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->count();
    }

    protected function getUrlsForPages(int $offset): array
    {
        if (! class_exists(Page::class)) {
            return [];
        }

        $pages = Page::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($this->maxUrlsPerSitemap)
            ->get();

        $urls = [];

        foreach ($pages as $page) {
            $url = [
                'loc' => $page->url,
                'lastmod' => $page->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];

            // Multi-language support
            if (setting('botble_sitemap_multi_language', false)) {
                $url['alternates'] = $this->getLanguageAlternates($page);
            }

            $urls[] = $url;
        }

        return $urls;
    }

    protected function getUrlsForProducts(int $offset): array
    {
        $products = Product::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('is_variation', false)
            ->with('slugable')
            ->orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($this->maxUrlsPerSitemap)
            ->get();

        $urls = [];

        foreach ($products as $product) {
            // Build product URL
            if (! $product->slug && $product->slugable) {
                $product->slug = $product->slugable->key;
            }

            if (! $product->slug) {
                continue;
            }

            $prefix = SlugHelper::getPrefix(Product::class);
            $productUrl = url(ltrim($prefix . '/' . $product->slug, '/')) . SlugHelper::getPublicSingleEndingURL();

            $url = [
                'loc' => $productUrl,
                'lastmod' => $product->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            // Add product images
            if (setting('botble_sitemap_include_images', false)) {
                $images = [];

                if ($product->image) {
                    $images[] = \Botble\Media\Facades\RvMedia::getImageUrl($product->image);
                }

                if ($product->images && is_array($product->images)) {
                    foreach ($product->images as $image) {
                        $images[] = \Botble\Media\Facades\RvMedia::getImageUrl($image);
                    }
                }

                if (! empty($images)) {
                    $url['images'] = array_slice($images, 0, 5); // Max 5 images per product
                }
            }

            // Multi-language support
            if (setting('botble_sitemap_multi_language', false)) {
                $url['alternates'] = $this->getLanguageAlternates($product);
            }

            $urls[] = $url;
        }

        return $urls;
    }

    protected function getUrlsForCategories(int $offset): array
    {
        $categories = ProductCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with('slugable')
            ->orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($this->maxUrlsPerSitemap)
            ->get();

        $urls = [];

        foreach ($categories as $category) {
            // Build category URL
            if (! $category->slug && $category->slugable) {
                $category->slug = $category->slugable->key;
            }

            if (! $category->slug) {
                continue;
            }

            $prefix = SlugHelper::getPrefix(ProductCategory::class);
            $categoryUrl = url(ltrim($prefix . '/' . $category->slug, '/')) . SlugHelper::getPublicSingleEndingURL();

            $url = [
                'loc' => $categoryUrl,
                'lastmod' => $category->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];

            // Add category image
            if (setting('botble_sitemap_include_images', false) && $category->image) {
                $url['images'] = [
                    \Botble\Media\Facades\RvMedia::getImageUrl($category->image),
                ];
            }

            // Multi-language support
            if (setting('botble_sitemap_multi_language', false)) {
                $url['alternates'] = $this->getLanguageAlternates($category);
            }

            $urls[] = $url;
        }

        return $urls;
    }

    protected function getUrlsForPosts(int $offset): array
    {
        $posts = Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with('slugable')
            ->orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($this->maxUrlsPerSitemap)
            ->get();

        $urls = [];

        foreach ($posts as $post) {
            // Build post URL
            if (! $post->slug && $post->slugable) {
                $post->slug = $post->slugable->key;
            }

            if (! $post->slug) {
                continue;
            }

            $prefix = SlugHelper::getPrefix(Post::class);
            $postUrl = url(ltrim($prefix . '/' . $post->slug, '/')) . SlugHelper::getPublicSingleEndingURL();

            $url = [
                'loc' => $postUrl,
                'lastmod' => $post->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            // Add post image
            if (setting('botble_sitemap_include_images', false) && $post->image) {
                $url['images'] = [
                    \Botble\Media\Facades\RvMedia::getImageUrl($post->image),
                ];
            }

            // Multi-language support
            if (setting('botble_sitemap_multi_language', false)) {
                $url['alternates'] = $this->getLanguageAlternates($post);
            }

            $urls[] = $url;
        }

        return $urls;
    }

    protected function getLanguageAlternates($model): array
    {
        $alternates = [];

        if (! class_exists(Language::class)) {
            return $alternates;
        }

        $languages = Language::getActiveLanguage(['lang_code', 'lang_locale']);

        foreach ($languages as $language) {
            // Build URL for each language
            $langUrl = BaseHelper::getHomepageUrl() . '/' . $language->lang_code;

            if (method_exists($model, 'url')) {
                $langUrl = str_replace(BaseHelper::getHomepageUrl(), $langUrl, $model->url);
            }

            $alternates[] = [
                'lang' => $language->lang_locale ?? $language->lang_code,
                'url' => $langUrl,
            ];
        }

        return $alternates;
    }

    public function clearCache(): void
    {
        $types = ['pages', 'products', 'categories', 'posts'];

        Cache::forget($this->cachePrefix . 'index');

        foreach ($types as $type) {
            // Clear cache for all pages of each type
            for ($page = 1; $page <= 100; $page++) {
                $cacheKey = $this->cachePrefix . $type . '_page_' . $page;
                Cache::forget($cacheKey);
            }
        }
    }
}
