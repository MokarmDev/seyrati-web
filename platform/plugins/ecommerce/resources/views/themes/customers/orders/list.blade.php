@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Orders'))

@section('content')
    @if($orders->isNotEmpty())
        <div class="customer-list-order">
            <div class="bb-customer-card-list order-cards">
                @foreach ($orders as $order)
                    <div class="bb-customer-card order-card">
                        <div class="bb-customer-card-header">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <h3 class="bb-customer-card-title mb-2">
                                        {{ __('Order :code', ['code' => $order->code]) }}
                                    </h3>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <div class="bb-customer-card-status">
                                            {!! BaseHelper::clean($order->status->toHtml()) !!}
                                        </div>
                                        <span class="text-muted" style="font-size: 0.75rem;">•</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            {{ $order->created_at->translatedFormat('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bb-customer-card-body">
                            <div class="bb-customer-card-info">
                                <div class="row g-3">
                                    <div class="col-6 col-sm-4">
                                        <div class="info-item">
                                            <span class="label">{{ __('Total Amount') }}</span>
                                            <span class="value">{{ $order->amount_format }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="info-item">
                                            <span class="label">{{ __('Items') }}</span>
                                            <span class="value">{{ $order->products_count }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="info-item">
                                            <span class="label">{{ __('Payment') }}</span>
                                            <span class="value">
                                                @if(is_plugin_active('payment') && $order->payment->id && $order->payment->payment_channel->label())
                                                    {{ $order->payment->payment_channel->label() }}
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bb-customer-card-footer">
                            <a
                                class="btn btn-primary btn-sm"
                                href="{{ route('customer.orders.view', $order->id) }}"
                            >
                                <x-core::icon name="ti ti-eye" />
                                <span>{{ __('View Details') }}</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {!! $orders->links() !!}
                </div>
            @endif
        </div>
    @else
        @include(EcommerceHelper::viewPath('customers.partials.empty-state'), [
            'title' => __('No orders yet!'),
            'subtitle' => __('You have not placed any orders yet.'),
            'actionUrl' => route('public.products'),
            'actionLabel' => __('Start shopping now'),
        ])
    @endif
@stop
