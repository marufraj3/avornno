@extends('frontEnd.layouts.master')

@section('title', 'All Products')

@section('content')
<div class="sf-page">
    <div class="sf-container">

        <div class="sf-page-head" style="border-radius:var(--r-lg);margin-top:18px">
            <div class="sf-container">
                <h1>All Products</h1>
                <p style="color:#c3cdea;font-size:14px;margin-top:6px">Browse the full catalog.</p>
            </div>
        </div>

        @if($products->count())
            <div class="sf-pgrid sf-pgrid--5" style="margin-top:22px">
                @foreach($products as $product)
                    @include('frontEnd.layouts.partials.product-card', ['product' => $product])
                @endforeach
            </div>
            {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
        @else
            <div class="sf-empty sf-card-surface">
                <i class="fa-solid fa-bag-shopping"></i>
                <h4>No products found</h4>
                <p>Check back soon for new arrivals.</p>
                <a class="sf-btn sf-btn--dark" style="margin-top:16px" href="{{ route('home') }}">Back home</a>
            </div>
        @endif
    </div>
</div>
@endsection
