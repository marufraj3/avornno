@php
    $subtotal = (float) str_replace([',', '.00'], '', Cart::instance('shopping')->subtotal());
    $shipping = (float) Session::get('shipping', 0);
    $discount = (float) Session::get('discount', 0);
@endphp
<table class="cart_table">
    <tbody>
        @forelse(Cart::instance('shopping')->content() as $item)
            <tr>
                <td>
                    <img src="{{ asset($item->options->image ?? 'public/uploads/default.webp') }}" width="52" height="52" alt="">
                    <div>
                        <strong>{{ $item->name }}</strong>
                        @if(!empty($item->options->product_size))<small>Size: {{ $item->options->product_size }}</small>@endif
                        @if(!empty($item->options->product_color))<small>Color: {{ $item->options->product_color }}</small>@endif
                    </div>
                </td>
                <td>
                    <div class="quantity">
                        <button type="button" class="cart_decrement" data-id="{{ $item->rowId }}" aria-label="কমান">−</button>
                        <input type="text" value="{{ $item->qty }}" readonly>
                        <button type="button" class="cart_increment" data-id="{{ $item->rowId }}" aria-label="বাড়ান">+</button>
                    </div>
                    <button type="button" class="cart_remove ms-remove" data-id="{{ $item->rowId }}">Remove</button>
                    <div class="ms-line">{{ $item->qty }} x {{ number_format($item->price, 0) }} = {{ number_format($item->price * $item->qty, 0) }} TK</div>
                </td>
            </tr>
        @empty
            <tr><td colspan="2">প্রোডাক্ট সিলেক্ট করুন</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr><th>Subtotal</th><td id="net_total"><strong>{{ number_format($subtotal, 0) }} TK</strong></td></tr>
        <tr><th>Shipping Charge</th><td id="cart_shipping_cost"><strong>{{ number_format($shipping, 0) }} TK</strong></td></tr>
        @if($discount > 0)
            <tr><th>Discount</th><td><strong>−{{ number_format($discount, 0) }} TK</strong></td></tr>
        @endif
        <tr><th>Payable Amount</th><td id="grand_total"><strong>{{ number_format($subtotal + $shipping - $discount, 0) }} TK</strong></td></tr>
    </tfoot>
</table>
