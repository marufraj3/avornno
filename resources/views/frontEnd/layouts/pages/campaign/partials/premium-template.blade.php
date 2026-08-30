@php
    $L = $landing ?? $campaign_data->landingContent();
    $lpEditor = !empty($lpEditor);
    $hero = $L['hero_image'] ?: ($campaign_data->banner ?: $campaign_data->image_one ?: optional($products->first()?->image)->image);
    $gallery = collect($L['gallery'] ?? [])->filter()->values();
    if ($gallery->isEmpty()) {
        $gallery = $products->map(fn ($p) => optional($p->image)->image)->filter()->unique()->values();
    }
    $phone = $L['phone'] ?: (optional($contact ?? null)->phone ?? '');
    $storeName = optional($generalsetting ?? null)->name;
    $logo = optional($generalsetting ?? null)->logo;
    $sections = $L['sections'] ?? ['announcement','hero','cta','gallery','video','features','reviews','products','checkout'];
@endphp

<div class="ms-lp" data-lp-root>
@foreach($sections as $section)
@if($section === 'announcement')
    <header class="ms-top" data-lp-section="announcement">
        <div class="ms-wrap">
            @if($logo)
                <a class="ms-logo" href="#product_list"><img src="{{ asset($logo) }}" alt="{{ $storeName }}"></a>
            @endif
            <h1 class="ms-title" data-edit="headline" data-type="text">{{ $L['headline'] }}</h1>
            <div class="ms-urgency">
                @if($campaign_data->deadline)
                    <span class="ms-timer" data-cpb-countdown="{{ $campaign_data->deadline }}">অফার শেষ: <b><span data-hours>00</span>:<span data-minutes>00</span>:<span data-seconds>00</span></b></span>
                @endif
                <span data-edit="urgency_text" data-type="text">{{ str_replace('{stock}', (string) ($L['stock_left'] ?? 12), $L['urgency_text'] ?? 'স্টকে মাত্র ১২টি বাকি') }}</span>
                <span>আজকে {{ (int) ($L['recent_orders'] ?? 34) }} জন অর্ডার করেছে</span>
            </div>
        </div>
    </header>
@elseif($section === 'hero')
    <section class="ms-hero" data-lp-section="hero">
        <div class="ms-wrap">
            <div class="ms-banner" data-edit="hero_image" data-type="image">
                @if($hero)
                    <img src="{{ asset($hero) }}" alt="{{ $L['headline'] }}">
                @else
                    <div class="ms-placeholder">ব্যানার ছবি যোগ করুন</div>
                @endif
            </div>
        </div>
    </section>
@elseif($section === 'cta')
    <div class="ms-cta-row" data-lp-section="cta">
        <div class="ms-wrap">
            <a href="#product_list" class="ms-btn" data-edit="cta" data-type="text">{{ $L['cta'] }}</a>
        </div>
    </div>
@elseif($section === 'gallery')
    <section class="ms-shots" data-lp-section="gallery">
        <div class="ms-wrap" data-edit="gallery" data-type="gallery">
            @foreach($gallery as $img)
                <img class="ms-shot" src="{{ asset($img) }}" alt="product" loading="lazy">
            @endforeach
        </div>
        <div class="ms-cta-row">
            <div class="ms-wrap"><a href="#product_list" class="ms-btn">{{ $L['cta'] }}</a></div>
        </div>
        <div class="ms-call">
            <p data-edit="phone_label" data-type="text">{{ $L['phone_label'] }}</p>
            <a href="tel:{{ preg_replace('/[^0-9+]/','',$phone) }}" data-edit="phone" data-type="text">{{ $phone ?: '01XXXXXXXXX' }}</a>
        </div>
    </section>
@elseif($section === 'video')
    <section class="ms-video" data-lp-section="video">
        <div class="ms-wrap">
            <div class="ms-video-box" data-edit="video" data-type="text" data-value="{{ $L['video'] }}">
                @if(!empty($L['video']))
                    <iframe src="https://www.youtube.com/embed/{{ $L['video'] }}" title="video" allowfullscreen loading="lazy"></iframe>
                @else
                    <div class="ms-placeholder">YouTube ভিডিও</div>
                @endif
            </div>
        </div>
    </section>
@elseif($section === 'features')
    <section class="ms-info" data-lp-section="features">
        <div class="ms-wrap">
            <h2 data-edit="features_title" data-type="text">{{ $L['features_title'] }}</h2>
            <div class="ms-info-body" data-edit="features_html" data-type="html">{!! $L['features_html'] !!}</div>
            <div class="ms-cta-row"><a href="#product_list" class="ms-btn">{{ $L['cta'] }}</a></div>
        </div>
    </section>
@elseif($section === 'reviews')
    <section class="ms-reviews" data-lp-section="reviews">
        <div class="ms-wrap">
            <h2>Customer Reviews</h2>
            <div class="ms-review-grid" data-cpb-dynamic="reviews"></div>
        </div>
    </section>
@elseif($section === 'products')
    <section class="ms-products" id="product_list" data-lp-section="products">
        <div class="ms-wrap">
            <h2 data-edit="product_title" data-type="text">{{ $L['product_title'] }}</h2>
            <div class="ms-product-list cpb-live-products" data-cpb-dynamic="products"></div>
        </div>
    </section>
@elseif($section === 'checkout')
    <section class="ms-checkout" id="order_form_section" data-lp-section="checkout">
        <div class="ms-wrap">
            <div data-cpb-dynamic="checkout"></div>
        </div>
    </section>
@endif
@endforeach
</div>
