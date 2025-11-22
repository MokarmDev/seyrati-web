<div class="commission-actions mt-3 mb-3">
    <div class="row">
        <div class="col-md-12">
            <div class="btn-list">
                <x-core::button
                    type="button"
                    id="btn-apply-commission"
                    color="primary"
                    data-url="{{ route('ecommerce.settings.currencies.apply-commission') }}"
                    icon="ti ti-percentage"
                >
                    {{ trans('plugins/ecommerce::currency.apply_commission_now') }}
                </x-core::button>

                <x-core::button
                    type="button"
                    id="btn-reset-prices"
                    color="warning"
                    data-url="{{ route('ecommerce.settings.currencies.reset-prices') }}"
                    icon="ti ti-refresh"
                >
                    {{ trans('plugins/ecommerce::currency.reset_prices') }}
                </x-core::button>
            </div>
        </div>
    </div>

    @if(isset($defaultCurrency) && $defaultCurrency && $defaultCurrency->applied_commission_percentage)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-success">
                <i class="ti ti-check-circle"></i>
                <strong>{{ trans('plugins/ecommerce::currency.commission_currently_applied') }}:</strong>
                {{ number_format($defaultCurrency->applied_commission_percentage, 0) }}%
                @if($defaultCurrency->commission_applied_at)
                    <span class="text-muted ms-2">
                        ({{ trans('plugins/ecommerce::currency.applied_at') }}: {{ $defaultCurrency->commission_applied_at->diffForHumans() }})
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="ti ti-alert-triangle"></i>
                {{ trans('plugins/ecommerce::currency.commission_warning') }}
            </div>
        </div>
    </div>
</div>



@push('footer')
<script>
    $(document).ready(function() {
        // Apply Commission
        $('#btn-apply-commission').on('click', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const url = $btn.data('url');
            
            // Get current commission percentage from input field
            const commissionPercentage = parseFloat($('input[name="commission_percentage"]').val()) || 0;
            
            if (commissionPercentage === 0) {
                Botble.showError('{{ trans("plugins/ecommerce::currency.please_enter_commission_percentage") }}');
                return;
            }

            $httpClient
                .make()
                .withButtonLoading($btn)
                .post(url, {
                    commission_percentage: commissionPercentage
                })
                .then(({ data }) => {
                    if (!data.error) {
                        Botble.showSuccess(data.message);
                        // Update the alert without reloading
                        updateCommissionAlert(commissionPercentage);
                    } else {
                        Botble.showError(data.message);
                    }
                })
                .catch(error => {
                    Botble.showError(error.message || '{{ trans("plugins/ecommerce::currency.commission_application_failed") }}');
                });
        });

        // Reset Prices
        $('#btn-reset-prices').on('click', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const url = $btn.data('url');

            $httpClient
                .make()
                .withButtonLoading($btn)
                .post(url)
                .then(({ data }) => {
                    if (!data.error) {
                        Botble.showSuccess(data.message);
                        // Reset the input and alert without reloading
                        $('input[name="commission_percentage"]').val(0);
                        removeCommissionAlert();
                    } else {
                        Botble.showError(data.message);
                    }
                })
                .catch(error => {
                    Botble.showError(error.message || '{{ trans("plugins/ecommerce::currency.price_reset_failed") }}');
                });
        });

        // Update commission percentage in real-time
        $('input[name="commission_percentage"]').on('input', function() {
            const value = parseFloat($(this).val()) || 0;
            if (value > 100) {
                $(this).val(100);
            } else if (value < 0) {
                $(this).val(0);
            }
        });

        // Helper function to update commission alert
        function updateCommissionAlert(percentage) {
            const $container = $('.commission-actions');
            const $existingAlert = $container.find('.alert-success');
            
            const alertHtml = `
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <i class="ti ti-check-circle"></i>
                            <strong>{{ trans('plugins/ecommerce::currency.commission_currently_applied') }}:</strong>
                            ${percentage}%
                            <span class="text-muted ms-2">
                                ({{ trans('plugins/ecommerce::currency.applied_at') }}: {{ trans('core/base::tables.just_now') }})
                            </span>
                        </div>
                    </div>
                </div>
            `;
            
            if ($existingAlert.length > 0) {
                $existingAlert.closest('.row').replaceWith(alertHtml);
            } else {
                $container.find('.btn-list').closest('.row').after(alertHtml);
            }
        }

        // Helper function to remove commission alert
        function removeCommissionAlert() {
            $('.commission-actions .alert-success').closest('.row').remove();
        }
    });
</script>
@endpush
