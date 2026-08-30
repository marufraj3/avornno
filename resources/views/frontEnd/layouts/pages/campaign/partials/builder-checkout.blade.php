@php
    $builderSubtotal = (float) str_replace([',', '.00'], '', Cart::instance('shopping')->subtotal());
    $builderShipping = (float) Session::get('shipping', 0);
    $builderDiscount = (float) Session::get('discount', 0);
    $builderBumps    = $orderBumps ?? collect();
@endphp
<div class="cpb-live-checkout">
    <div id="cpb-variant-picker" class="cpb-variant-picker" hidden></div>

    <form action="{{ route('customer.ordersave') }}" method="POST" data-cpb-order-form>
        @csrf
        <input type="hidden" name="payment_method" value="cod">
        <div class="cpb-checkout-columns ms-checkout-cols">
        <section class="cpb-customer-form">
            <div class="cpb-checkout-card-head">
                <strong id="cpb-form-heading">বিলিং বিবরণ</strong>
            </div>
            @if($errors->any())
                <div class="cpb-form-errors" role="alert">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="cpb-form-field">
                <label for="cpb-name">আপনার নাম <span>*</span></label>
                <input id="cpb-name" type="text" name="name" value="{{ old('name') }}" placeholder="আপনার সম্পূর্ণ নাম" autocomplete="name" required>
            </div>
            <div class="cpb-form-field">
                <label for="cpb-phone">মোবাইল নম্বর <span>*</span></label>
                <input id="cpb-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" inputmode="numeric" maxlength="11" autocomplete="tel" required>
            </div>
            <div class="cpb-form-field">
                <label for="cpb-address">সম্পূর্ণ ঠিকানা <span>*</span></label>
                <textarea id="cpb-address" name="address" placeholder="জেলা, থানা, এলাকা" required>{{ old('address') }}</textarea>
            </div>
            <div class="cpb-form-field">
                <span class="cpb-fieldset-label">Delivery Area</span>
                <div class="cpb-zone-cards" role="radiogroup">
                    @foreach(($shippingcharge ?? collect()) as $charge)
                        @php $isZoneSelected = (string) old('area', $loop->first ? $charge->id : null) === (string) $charge->id; @endphp
                        <label class="cpb-zone-card {{ $isZoneSelected ? 'is-checked' : '' }}">
                            <input type="radio" name="area" value="{{ $charge->id }}" data-cpb-zone {{ $isZoneSelected ? 'checked' : '' }} required>
                            <span class="cpb-zone-radio"></span>
                            <span class="cpb-zone-text">
                                <strong>{{ $charge->name }}</strong>
                                <small>{{ number_format((float) $charge->amount, 0) }} tk</small>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <button class="cpb-place-order ms-btn" type="submit"><span>অর্ডার কনফার্ম করুন</span></button>
        </section>

        <section class="cpb-order-summary">
            <div class="cpb-checkout-card-head">
                <strong id="cpb-summary-heading">আপনার অর্ডার আইটেম</strong>
            </div>
            <div class="cartlist cpb-cartlist">
                <table class="cart_table">
                    <tbody>
                    @forelse(Cart::instance('shopping')->content() as $item)
                        <tr>
                            <td>
                                <img src="{{ asset($item->options->image ?? 'public/uploads/default.webp') }}" width="56" height="56" alt="">
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    @if(!empty($item->options->product_size))<small>Size: {{ $item->options->product_size }}</small>@endif
                                    @if(!empty($item->options->product_color))<small>Color: {{ $item->options->product_color }}</small>@endif
                                </div>
                            </td>
                            <td>
                                <div class="quantity">
                                    <button type="button" class="cart_decrement" data-id="{{ $item->rowId }}">−</button>
                                    <input type="text" value="{{ $item->qty }}" readonly>
                                    <button type="button" class="cart_increment" data-id="{{ $item->rowId }}">+</button>
                                </div>
                                <button type="button" class="cart_remove ms-remove" data-id="{{ $item->rowId }}">Remove</button>
                                <div>{{ $item->qty }} x {{ number_format($item->price, 0) }} = {{ number_format($item->price * $item->qty, 0) }} TK</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2">কোনো আইটেম নেই</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr><th>Subtotal</th><td id="net_total"><strong>{{ number_format($builderSubtotal, 0) }} TK</strong></td></tr>
                        <tr><th>Shipping Charge</th><td id="cart_shipping_cost"><strong>{{ number_format($builderShipping, 0) }} TK</strong></td></tr>
                        @if($builderDiscount > 0)
                            <tr><th>Discount</th><td><strong>−{{ number_format($builderDiscount, 0) }} TK</strong></td></tr>
                        @endif
                        <tr><th>Payable Amount</th><td id="grand_total"><strong>{{ number_format($builderSubtotal + $builderShipping - $builderDiscount, 0) }} TK</strong></td></tr>
                    </tfoot>
                </table>
            </div>
            @if($builderBumps->isNotEmpty())
                <div class="cpb-bumps ms-bump" data-cpb-bumps data-bump-url="{{ route('cart.addBump') }}">
                    @foreach($builderBumps as $bump)
                        <div class="cpb-bump" data-cpb-bump="{{ $bump->id }}"
                             data-bump-product-id="{{ $bump->product_id }}"
                             data-bump-product-name="{{ $bump->product->name }}"
                             data-bump-price="{{ $bump->offerPrice() }}">
                            <label class="cpb-bump-check">
                                <input type="checkbox" data-cpb-bump-toggle="{{ $bump->id }}">
                                <img src="{{ asset(optional($bump->product->image)->image ?? 'public/uploads/default.webp') }}" width="48" height="48" alt="">
                                <span>
                                    <strong>{{ $bump->title ?: 'আরও ১টি নিলে ৳'.number_format($bump->savings(), 0).' কম' }}</strong>
                                    <small>{{ $bump->product->name }} — {{ number_format($bump->offerPrice(), 0) }} TK</small>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
            <button class="cpb-place-order ms-btn" type="submit"><span>অর্ডার কনফার্ম করুন</span></button>
        </section>
        </div>
    </form>
</div>
