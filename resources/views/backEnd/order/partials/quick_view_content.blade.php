{{-- অর্ডার কুইক ভিউ — মডালের ভিতরের কনটেন্ট (AJAX দিয়ে লোড হয়) --}}
@php
    $shipping   = $order->shipping;
    $customer   = $order->customer;
    $payment    = $order->payment;

    $mobile     = optional($shipping)->phone ?: optional($customer)->phone;
    $orderIp    = $order->ip_address ?? $order->ip ?? null;
    $isIpBlocked = $orderIp && in_array($orderIp, $blockedIps ?? [], true);

    $subtotal   = 0;
    foreach ($order->orderdetails as $od) {
        $subtotal += ((float) $od->sale_price) * (int) $od->qty;
    }
    $discount   = (float) ($order->discount ?? 0);
    $shipCharge = (float) ($order->shipping_charge ?? 0);
    $grandTotal = (float) $order->amount;
    $paid       = (float) ($order->paid_amount ?? optional($payment)->amount ?? 0);
    $due        = max($grandTotal - $paid, 0);

    $fraudRate  = $order->fraud_rate;
    $orderNote  = $order->order_note ?? '';
    $adminNote  = $order->admin_note ?? '';
@endphp

{{-- গ্রাহক তথ্য --}}
<div class="oqv-section">
    <h6 class="oqv-section-title"><i class="fas fa-user"></i> গ্রাহক তথ্য</h6>
    <div class="oqv-customer-card">
        <div class="oqv-avatar d-flex align-items-center justify-content-center bg-light">
            <i class="fas fa-user text-secondary"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="oqv-customer-name">{{ optional($shipping)->name ?: (optional($customer)->name ?: 'গেস্ট') }}</div>
            <div class="oqv-customer-phone"><i class="fas fa-phone-alt me-1"></i> {{ $mobile ?: '—' }}</div>
            @if(optional($customer)->email)
                <div class="oqv-customer-email"><i class="fas fa-envelope me-1"></i> {{ $customer->email }}</div>
            @endif
            <div class="oqv-customer-phone mt-1"><i class="fas fa-map-marker-alt me-1"></i> {{ optional($shipping)->address ?: '—' }}</div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-2 order-ip-wrap">
                <span class="oqv-label">IP</span>
                <code class="oqv-ip-code">{{ $orderIp ?: 'N/A' }}</code>
                @if($orderIp)
                    @if($isIpBlocked)
                        <span class="badge bg-secondary" title="এই IP আগেই ব্লক করা"><i class="fe-shield"></i> Blocked</span>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-danger block-ip-btn"
                                data-ip="{{ $orderIp }}" data-reason="ফেইক অর্ডার">
                            <i class="fe-shield"></i> IP ব্লক
                        </button>
                    @endif
                @endif
            </div>
        </div>
        <div class="text-end flex-shrink-0">
            <span class="badge oqv-status-badge">{{ optional($order->status)->name ?: '—' }}</span>
            <div class="oqv-courier-info mt-2">
                {{ optional($order->created_at)->format('d-m-Y h:i a') }}
            </div>
            @if($order->courier_type || $order->courier_tracking_id || $order->consignment_id)
                <div class="oqv-courier-info mt-1">
                    <i class="fas fa-truck me-1"></i>
                    {{ ucfirst($order->courier_type ?: 'কুরিয়ার') }}
                    <br><small>{{ $order->courier_tracking_id ?: $order->consignment_id }}</small>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- নোট --}}
<div class="row oqv-notes-row">
    <div class="col-md-6">
        <div class="oqv-section oqv-note-section h-100">
            <h6 class="oqv-section-title"><i class="fas fa-sticky-note"></i> অর্ডার নোট (গ্রাহক)</h6>
            <div class="oqv-note-text">{{ $orderNote !== '' ? $orderNote : 'কোনো নোট নেই' }}</div>
            <button type="button" class="btn btn-sm btn-outline-primary note-modal-btn"
                    data-id="{{ $order->id }}" data-type="order" data-note="{{ $orderNote }}">
                {{ $orderNote !== '' ? 'View' : 'Add' }}
            </button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="oqv-section oqv-note-section h-100">
            <h6 class="oqv-section-title"><i class="fas fa-user-shield"></i> অ্যাডমিন নোট</h6>
            <div class="oqv-note-text">{{ $adminNote !== '' ? $adminNote : 'কোনো নোট নেই' }}</div>
            <button type="button" class="btn btn-sm btn-outline-primary note-modal-btn"
                    data-id="{{ $order->id }}" data-type="admin" data-note="{{ $adminNote }}">
                {{ $adminNote !== '' ? 'View' : 'Add' }}
            </button>
        </div>
    </div>
</div>

{{-- প্রোডাক্ট --}}
<div class="oqv-section">
    <h6 class="oqv-section-title"><i class="fas fa-box-open"></i> প্রোডাক্ট ({{ $order->orderdetails->count() }})</h6>
    <div class="table-responsive">
        <table class="table table-sm oqv-items-table mb-0">
            <thead>
                <tr>
                    <th>প্রোডাক্ট</th>
                    <th>ভ্যারিয়েন্ট</th>
                    <th class="text-center">পরিমাণ</th>
                    <th class="text-end">দাম</th>
                    <th class="text-end">মোট</th>
                </tr>
            </thead>
            <tbody>
            @forelse($order->orderdetails as $item)
                @php
                    $colorName = optional($item->color)->colorName ?? optional($item->color)->color_name ?? null;
                    $sizeName  = optional($item->size)->sizeName ?? optional($item->size)->size_name ?? null;
                    $lineTotal = ((float) $item->sale_price) * (int) $item->qty;
                    $thumb     = optional($item->image)->image;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($thumb)
                                <img src="{{ asset($thumb) }}" class="oqv-product-thumb" alt="">
                            @endif
                            <span>{{ $item->product_name }}</span>
                        </div>
                    </td>
                    <td>
                        @if($colorName)
                            <span class="oqv-variant"><span class="oqv-color-swatch" style="background: {{ $colorName }};"></span>{{ $colorName }}</span>
                        @endif
                        @if($sizeName)
                            <span class="oqv-variant"><span class="oqv-variant-size">{{ $sizeName }}</span></span>
                        @endif
                        @if(!$colorName && !$sizeName)—@endif
                    </td>
                    <td class="text-center">{{ (int) $item->qty }}</td>
                    <td class="text-end">৳{{ number_format((float) $item->sale_price, 2) }}</td>
                    <td class="text-end">৳{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">কোনো প্রোডাক্ট নেই</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">সাবটোটাল</td>
                    <td class="text-end">৳{{ number_format($subtotal, 2) }}</td>
                </tr>
                @if($discount > 0)
                <tr>
                    <td colspan="4" class="text-end">ডিসকাউন্ট</td>
                    <td class="text-end text-danger">- ৳{{ number_format($discount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-end">ডেলিভারি চার্জ</td>
                    <td class="text-end">৳{{ number_format($shipCharge, 2) }}</td>
                </tr>
                <tr class="oqv-total-row">
                    <td colspan="4" class="text-end">সর্বমোট</td>
                    <td class="text-end">৳{{ number_format($grandTotal, 2) }}</td>
                </tr>
                @if($paid > 0)
                <tr>
                    <td colspan="4" class="text-end">পরিশোধিত</td>
                    <td class="text-end text-success">৳{{ number_format($paid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end">বাকি</td>
                    <td class="text-end text-danger">৳{{ number_format($due, 2) }}</td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>

{{-- ফ্রড চেক --}}
<div class="oqv-section">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="oqv-section-title mb-0"><i class="fe-shield"></i> ফ্রড স্ট্যাটাস</h6>
        <div class="d-flex align-items-center gap-2">
            <span id="oqvFraudStatus">
                @if(is_null($fraudRate))
                    <span class="badge bg-warning text-dark oqv-fraud-badge">যাচাই করা হয়নি</span>
                @elseif($fraudRate >= 80)
                    <span class="badge bg-success oqv-fraud-badge">{{ $fraudRate }}% নিরাপদ</span>
                @else
                    <span class="badge bg-danger oqv-fraud-badge">{{ $fraudRate }}% ঝুঁকি</span>
                @endif
            </span>
            <button type="button" class="btn btn-sm btn-primary oqv-fraud-btn fraud-check-oqv" data-mobile="{{ $mobile }}">
                <i class="fas fa-sync-alt me-1"></i> ফ্রড চেক
            </button>
        </div>
    </div>
    <div id="oqvFraudReport" class="oqv-fraud-report"></div>
</div>

{{-- অ্যাকশন --}}
<div class="oqv-actions">
    <a href="{{ route('admin.order.invoice', ['invoice_id' => $order->invoice_id]) }}" class="btn btn-sm oqv-act-invoice" target="_blank">
        <i class="fas fa-file-invoice me-1"></i> ইনভয়েস
    </a>
    <a href="{{ route('admin.order.process', ['invoice_id' => $order->invoice_id]) }}" class="btn btn-sm oqv-act-process">
        <i class="fas fa-cogs me-1"></i> প্রসেস
    </a>
    <a href="{{ route('admin.order.edit', ['invoice_id' => $order->invoice_id]) }}" class="btn btn-sm oqv-act-edit">
        <i class="fas fa-edit me-1"></i> এডিট
    </a>
    @if(!empty($steadfast))
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-courier" data-courier="steadfast" data-order-id="{{ $order->id }}">
            <i class="fas fa-truck"></i> Steadfast
        </button>
    @endif
    @if(!empty($redx_info))
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-courier" data-courier="redx" data-order-id="{{ $order->id }}">
            <i class="fas fa-truck"></i> RedX
        </button>
    @endif
    @if(!empty($pathao_info))
        <button type="button" class="btn btn-sm oqv-act-courier oi-quick-pathao" data-order-id="{{ $order->id }}">
            <i class="fas fa-truck"></i> Pathao
        </button>
    @endif
</div>
