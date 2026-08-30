@php
    $spById = [];
    foreach ($storefrontProducts ?? collect() as $sp) {
        $spById[(string) $sp['id']] = $sp;
    }
@endphp
@foreach($products as $product)
    @php
        $sp        = $spById[(string) $product->id] ?? null;
        $cardSizes = $sp['sizes'] ?? [];
        $cardColors= $sp['colors'] ?? [];
        $unitPrice = (float) ($sp['price'] ?? $product->new_price);
        $oldPrice  = (float) ($sp['old_price'] ?? $product->old_price);
        $imgSrc    = $sp['image'] ?? asset('public/uploads/default.webp');
        $offer     = ($oldPrice > $unitPrice && $oldPrice > 0) ? (int) round((($oldPrice - $unitPrice) / $oldPrice) * 100) : 0;
    @endphp
    <article class="ms-radio-card cpb-live-product"
           data-product-card="{{ $product->id }}"
           data-cpb-card
           data-select-product="{{ $product->id }}"
           data-unit-price="{{ $unitPrice }}"
           data-max-qty="{{ (int) ($sp['stock'] ?? 99) }}">
        <input type="radio" name="lp_product" value="{{ $product->id }}" class="ms-radio-input" tabindex="-1">
        <span class="ms-radio-mark" aria-hidden="true"></span>
        <div class="cpb-live-product-image">
            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" loading="lazy">
            @if($offer > 0)<span class="ms-offer">{{ $offer }}% OFF</span>@endif
        </div>
        <div class="cpb-live-product-body">
            <h3>{{ $product->name }}</h3>
            <div class="cpb-live-price">
                <strong data-cpb-price>{{ number_format($unitPrice, 0) }} TK</strong>
                @if($oldPrice > $unitPrice)<del>{{ number_format($oldPrice, 0) }} TK</del>@endif
            </div>

            @if(!empty($cardSizes))
                <div class="cpb-option-row" data-cpb-size-row>
                    <span class="cpb-option-label">Select Size</span>
                    <div class="cpb-pills" data-cpb-size-box>
                        @foreach($cardSizes as $size)
                            <button type="button" class="cpb-pill {{ (!empty($size['has_stock']) && (int) $size['stock'] <= 0) ? 'is-out' : '' }}"
                                    data-cpb-size="{{ $size['id'] }}">{{ $size['name'] }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($cardColors))
                <div class="cpb-option-row" data-cpb-color-row>
                    <span class="cpb-option-label">Select Color</span>
                    <div class="cpb-pills" data-cpb-color-box>
                        @foreach($cardColors as $color)
                            <button type="button" class="cpb-pill" data-cpb-color="{{ $color['id'] }}">
                                @if(!empty($color['hex']))<span class="cpb-dot" style="background:{{ $color['hex'] }}"></span>@endif
                                {{ $color['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>
@endforeach
