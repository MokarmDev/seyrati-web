<?php

namespace Botble\BotbleSitemap\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\BotbleSitemap\Forms\Settings\SitemapSettingForm;
use Botble\BotbleSitemap\Http\Requests\SitemapSettingsRequest;
use Botble\BotbleSitemap\Services\SitemapService;
use Botble\Setting\Http\Controllers\Concerns\InteractsWithSettings;

class SettingsController extends BaseController
{
    use InteractsWithSettings;

    public function index()
    {
        $this->pageTitle(trans('plugins/botble-sitemap::sitemap.settings.title'));

        return SitemapSettingForm::create()->renderForm();
    }

    public function update(SitemapSettingsRequest $request)
    {
        return $this->performUpdate($request->validated());
    }

    public function clearCache(SitemapService $sitemapService): BaseHttpResponse
    {
        $sitemapService->clearCache();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/botble-sitemap::sitemap.cache_cleared'));
    }
}
