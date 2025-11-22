<?php

namespace Botble\Ecommerce\Services;

use Botble\Ecommerce\Models\Currency;

class StoreCurrenciesService
{
    public function execute(array $currencies, array $deletedCurrencies): array
    {
        if ($deletedCurrencies) {
            Currency::query()->whereIn('id', $deletedCurrencies)->delete();
        }

        foreach ($currencies as $item) {
            if (! $item['title'] || ! $item['symbol']) {
                continue;
            }

            $item['title'] = mb_substr(strtoupper($item['title']), 0, 3);
            $item['symbol'] = mb_substr($item['symbol'], 0, 10);
            $item['decimals'] = $item['decimals'] < 10 && $item['decimals'] >= 0 ? $item['decimals'] : 2;


            // Handle commission settings
            if (isset($item['commission_percentage'])) {
                $item['commission_percentage'] = max(0, min(100, floatval($item['commission_percentage'])));
            }
            
            if (isset($item['apply_commission_globally'])) {
                $item['apply_commission_globally'] = (bool) $item['apply_commission_globally'];
            }



            if (count($currencies) == 1) {
                $item['is_default'] = 1;
            }

            $currency = Currency::query()->find($item['id']);

            if (! $currency) {
                Currency::query()->create($item);
            } else {
                $currency->fill($item);
                $currency->save();
            }
        }

        return [
            'error' => false,
        ];
    }
}
