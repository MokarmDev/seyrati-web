<?php

namespace Botble\Ecommerce\Http\Controllers\Settings;

use Botble\Ecommerce\Forms\Settings\CurrencySettingForm;
use Botble\Ecommerce\Http\Requests\Settings\CurrencySettingRequest;
use Botble\Ecommerce\Services\StoreCurrenciesService;
use Botble\Ecommerce\Services\ApplyCommissionService;

class CurrencySettingController extends SettingController
{
    public function index()
    {
        $this->pageTitle(trans('plugins/ecommerce::currency.currencies'));

        $form = CurrencySettingForm::create();
        
        $defaultCurrency = \Botble\Ecommerce\Models\Currency::query()->where('is_default', 1)->first();

        return view('plugins/ecommerce::settings.currency', compact('form', 'defaultCurrency'));
    }

    public function applyCommission(ApplyCommissionService $commissionService, \Illuminate\Http\Request $request)
    {
        $defaultCurrency = \Botble\Ecommerce\Models\Currency::query()->where('is_default', 1)->first();

        if (!$defaultCurrency) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/ecommerce::currency.no_default_currency'));
        }

        // Get commission percentage from request
        $commissionPercentage = $request->input('commission_percentage', $defaultCurrency->commission_percentage);

        $result = $commissionService->applyCommissionToAllProducts(
            $defaultCurrency,
            $commissionPercentage,
            $defaultCurrency->apply_commission_globally
        );

        if ($result['error']) {
            return $this->httpResponse()
                ->setError()
                ->setMessage($result['message']);
        }

        return $this->httpResponse()
            ->setMessage($result['message']);
    }

    public function resetPrices(ApplyCommissionService $commissionService)
    {
        $result = $commissionService->resetProductPrices();

        if ($result['error']) {
            return $this->httpResponse()
                ->setError()
                ->setMessage($result['message']);
        }

        return $this->httpResponse()
            ->setMessage($result['message']);
    }

    public function update(CurrencySettingRequest $request, StoreCurrenciesService $service)
    {
        $this->saveSettings($request->except([
            'currencies',
            'currencies_data',
            'deleted_currencies',
            'commission_percentage',
            'apply_commission_globally',
        ]));

        $currencies = $request->validated('currencies') ?: [];
        if ($currencies) {
            $currencies = json_decode($currencies, true);
        }

        $response = $this->httpResponse()
            ->setNextUrl(route('ecommerce.settings.currencies'));

        if (! $currencies) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::currency.require_at_least_one_currency'));
        }

        $deletedCurrencies = $request->validated('deleted_currencies') ?: [];
        if ($deletedCurrencies) {
            $deletedCurrencies = json_decode($deletedCurrencies, true);
        }

        $storedCurrencies = $service->execute($currencies, $deletedCurrencies);

        if ($storedCurrencies['error']) {
            return $response
                ->setError()
                ->setMessage($storedCurrencies['message']);
        }

        // Save commission settings for default currency
        $defaultCurrency = \Botble\Ecommerce\Models\Currency::query()->where('is_default', 1)->first();
        if ($defaultCurrency) {
            $defaultCurrency->commission_percentage = max(0, min(100, floatval($request->input('commission_percentage', 0))));
            $defaultCurrency->apply_commission_globally = (bool) $request->input('apply_commission_globally', false);
            $defaultCurrency->save();
        }

        return $response
            ->withUpdatedSuccessMessage();
    }
}
