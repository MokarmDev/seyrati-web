<?php

namespace Botble\Ecommerce\Services;

use Botble\Ecommerce\Models\Currency;
use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyCommissionService
{
	/**
	 * Apply commission to all products based on currency settings
	 *
	 * @param Currency $currency
	 * @param float $commissionPercentage
	 * @param bool $applyGlobally
	 * @return array
	 */
	public function applyCommissionToAllProducts(
		Currency $currency,
		float $commissionPercentage,
		bool $applyGlobally = true
	): array {
		try {
			Log::info('[Commission] applyCommissionToAllProducts called', [
				'currency_id' => $currency->id ?? null,
				'commissionPercentage' => $commissionPercentage,
				'applyGlobally' => $applyGlobally,
			]);

			DB::beginTransaction();

			if (!$applyGlobally || $commissionPercentage <= 0) {
				DB::commit();
				Log::info('[Commission] Not applied - either disabled or percentage <= 0', [
					'commissionPercentage' => $commissionPercentage,
					'applyGlobally' => $applyGlobally,
				]);

				return [
					'error' => false,
					'message' => trans('plugins/ecommerce::currency.commission_not_applied'),
					'updated_count' => 0,
				];
			}

			// Get all published products (excluding variations)
			$products = Product::query()
				->where('is_variation', false)
				->whereNotNull('price')
				->where('price', '>', 0)
				->get();

			$updatedCount = 0;
			$commissionRate = 1 + ($commissionPercentage / 100);

			Log::info('[Commission] Products fetched for commission application', ['count' => $products->count()]);

			foreach ($products as $product) {
				// Store original price if not already stored
				if (!$product->original_base_price) {
					$product->original_base_price = $product->price;
				}

				// Calculate new price with commission
				$newPrice = $product->original_base_price * $commissionRate;

				// Update product price
				$product->price = round($newPrice, 2);

				// Update sale price if exists
				if ($product->sale_price && $product->sale_price > 0) {
					if (!$product->original_base_sale_price) {
						$product->original_base_sale_price = $product->sale_price;
					}
					$product->sale_price = round($product->original_base_sale_price * $commissionRate, 2);
				}

				$product->save();

				// Update product variations (these are actual Product models with is_variation=true)
				if ($product->variations()->count() > 0) {
					foreach ($product->variations as $productVariation) {
						// Get the actual Product model from the ProductVariation relation
						$variationProduct = $productVariation->product;
                        
						if (!$variationProduct || !$variationProduct->id) {
							continue;
						}

						if (!$variationProduct->original_base_price) {
							$variationProduct->original_base_price = $variationProduct->price;
						}

						$variationProduct->price = round($variationProduct->original_base_price * $commissionRate, 2);

						if ($variationProduct->sale_price && $variationProduct->sale_price > 0) {
							if (!$variationProduct->original_base_sale_price) {
								$variationProduct->original_base_sale_price = $variationProduct->sale_price;
							}
							$variationProduct->sale_price = round($variationProduct->original_base_sale_price * $commissionRate, 2);
						}

						$variationProduct->save();
					}
				}

				$updatedCount++;
			}

			DB::commit();

			Log::info('[Commission] Commission applied successfully', ['updated_count' => $updatedCount, 'commissionPercentage' => $commissionPercentage]);

			// Update currency with applied commission info and save the percentage for future use
			$currency->commission_percentage = $commissionPercentage;
			$currency->applied_commission_percentage = $commissionPercentage;
			$currency->commission_applied_at = now();
			$currency->save();

			// Clear cache
			$this->clearProductPriceCache();

			return [
				'error' => false,
				'message' => trans('plugins/ecommerce::currency.commission_applied_successfully', [
					'count' => $updatedCount,
					'percentage' => $commissionPercentage,
				]),
				'updated_count' => $updatedCount,
			];
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('[Commission] Commission Application Error', [
				'message' => $e->getMessage(),
				'exception' => $e,
			]);

			return [
				'error' => true,
				'message' => trans('plugins/ecommerce::currency.commission_application_failed') . ': ' . $e->getMessage(),
				'updated_count' => 0,
			];
		}
	}

	/**
	 * Reset all products to original prices (remove commission)
	 *
	 * @return array
	 */
	public function resetProductPrices(): array
	{
		try {
			DB::beginTransaction();

			$products = Product::query()
				->where('is_variation', false)
				->whereNotNull('original_base_price')
				->get();

			$updatedCount = 0;

			foreach ($products as $product) {
				// Reset to original price
				$product->price = $product->original_base_price;

				if ($product->original_base_sale_price) {
					$product->sale_price = $product->original_base_sale_price;
				}

				$product->save();

				// Reset variations (these are actual Product models with is_variation=true)
				if ($product->variations()->count() > 0) {
					foreach ($product->variations as $productVariation) {
						// Get the actual Product model from the ProductVariation relation
						$variationProduct = $productVariation->product;
                        
						if (!$variationProduct || !$variationProduct->id) {
							continue;
						}

						if ($variationProduct->original_base_price) {
							$variationProduct->price = $variationProduct->original_base_price;
						}

						if ($variationProduct->original_base_sale_price) {
							$variationProduct->sale_price = $variationProduct->original_base_sale_price;
						}

						$variationProduct->save();
					}
				}

				$updatedCount++;
			}

			DB::commit();

			// Reset applied commission info in all currencies
			Currency::query()->update([
				'commission_percentage' => 0,
				'applied_commission_percentage' => null,
				'commission_applied_at' => null,
			]);

			$this->clearProductPriceCache();

			return [
				'error' => false,
				'message' => trans('plugins/ecommerce::currency.prices_reset_successfully', ['count' => $updatedCount]),
				'updated_count' => $updatedCount,
			];
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('Price Reset Error: ' . $e->getMessage());

			return [
				'error' => true,
				'message' => trans('plugins/ecommerce::currency.price_reset_failed') . ': ' . $e->getMessage(),
				'updated_count' => 0,
			];
		}
	}

	/**
	 * Calculate price with commission for a single product
	 *
	 * @param float $basePrice
	 * @param float $commissionPercentage
	 * @return float
	 */
	public function calculatePriceWithCommission(float $basePrice, float $commissionPercentage): float
	{
		if ($commissionPercentage <= 0) {
			return $basePrice;
		}

		$commissionRate = 1 + ($commissionPercentage / 100);
		return round($basePrice * $commissionRate, 2);
	}

	/**
	 * Clear product price cache
	 *
	 * @return void
	 */
	protected function clearProductPriceCache(): void
	{
		cache()->forget('product_prices');
		cache()->forget('products_collection_with_prices');
        
		if (function_exists('ecommerce_clear_product_cache')) {
			ecommerce_clear_product_cache();
		}
	}
}

