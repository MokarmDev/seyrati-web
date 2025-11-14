<?php

namespace Botble\BotbleSitemap\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\BotbleSitemap\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends BaseController
{
    protected SitemapService $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    public function index(): Response
    {
        $xml = $this->sitemapService->generateSitemapIndex();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    public function show(string $type, int $page = 1): Response
    {
        $xml = $this->sitemapService->generateSitemap($type, $page);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
