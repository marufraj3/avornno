@extends('frontEnd.layouts.master')

@section('title', 'Order Successful — #' . $order->invoice_id)

@section('content')
@php
    $payment = \App\Models\Payment::where('order_id', $order->id)->orderBy('id', 'desc')->first();
    $paid = $payment ? (float) $payment->amount : 0;
@endphp

<div class="sf-page">
    <div class="sf-container">
        <div class="sf-success">
            <div class="sf-success__ico"><i class="fa-solid fa-check"></i></div>
            <h2>Thank You! Order Placed 🎉</h2>
            <p>Your order has been received successfully. We'll start processing it right away.</p>

            <div class="sf-success__meta">
                <div><small>ORDER ID</small><b>#{{ $order->invoice_id }}</b></div>
                <div><small>DATE</small><b>{{ optional($order->created_at)->format('d M Y, h:i A') }}</b></div>
                <div><small>TOTAL</small><b>৳{{ number_format((float) $order->amount) }}</b></div>
                @if($paid > 0)<div><small>PAID NOW</small><b>৳{{ number_format($paid) }}</b></div>@endif
            </div>

            @if(($upsells ?? collect())->isNotEmpty())
                <div class="ms-upsell" style="margin:24px auto;max-width:520px;text-align:left">
                    <h3 style="font-size:18px">এক ক্লিকে আরও সাশ্রয়</h3>
                    @foreach($upsells as $bump)
                        <form method="post" action="{{ route('customer.order_upsell', $order->id) }}" style="display:flex;gap:12px;align-items:center;border:1px solid #e5e7eb;padding:12px;margin:10px 0;border-radius:8px">
                            @csrf
                            <input type="hidden" name="bump_id" value="{{ $bump->id }}">
                            <img src="{{ asset(optional($bump->product->image)->image ?? 'public/uploads/default.webp') }}" width="64" height="64" alt="">
                            <div style="flex:1">
                                <strong>{{ $bump->product->name }}</strong>
                                <div>{{ number_format($bump->offerPrice(), 0) }} TK <small>(৳{{ number_format($bump->savings(), 0) }} কম)</small></div>
                            </div>
                            <button type="submit" class="sf-btn sf-btn--primary">যোগ করুন</button>
                        </form>
                    @endforeach
                </div>
            @endif

            <div class="sf-success__btns">
                <a class="sf-btn sf-btn--primary" href="{{ route('customer.order_track') }}?invoice_id={{ $order->invoice_id }}"><i class="fa-solid fa-truck-fast"></i> Track Order</a>
                <a class="sf-btn sf-btn--dark" href="{{ route('customer.order_note', ['id' => $order->id]) }}"><i class="fa-regular fa-eye"></i> Order Details</a>
                <a class="sf-btn sf-btn--outline" href="{{ route('shop') }}"><i class="fa-solid fa-bag-shopping"></i> Continue Shopping</a>
            </div>
        </div>
    </div>
</div>
@endsection
