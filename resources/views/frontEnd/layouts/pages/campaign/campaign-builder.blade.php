<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pixels = $pixels ?? collect();
        $gtm_code = $gtm_code ?? collect();
        $tiktok_pixels = $tiktok_pixels ?? collect();
        $contact = $contact ?? null;
        $campName = strip_tags($campaign_data->name ?? '');
        $campId = (string) $campaign_data->id;
        $firstProduct = $products->first();
        $campValue = (float) ($firstProduct?->new_price ?? 0);
        $campaignImage = $campaign_data->image_one ?: ($campaign_data->banner ?: optional($firstProduct?->image)->image);
        $campaignDescription = strip_tags($campaign_data->short_description ?: ($campaign_data->description ?? ''));
        $discount = $firstProduct && (float) $firstProduct->old_price > (float) $firstProduct->new_price
            ? (float) $firstProduct->old_price - (float) $firstProduct->new_price
            : 0;
        $storefrontProducts = $products->map(function ($product) {
            // Size/Color names must come from variantPrices first (admin panel now
            // creates variants there); the old productsizes/productcolors pivots are
            // only a fallback. Otherwise the front end shows raw IDs like "Size 23".
            $sizeOptions = [];
            $colorOptions = [];
            foreach ($product->variantPrices ?? [] as $variant) {
                if ($variant->size_id && $variant->size) {
                    if (!isset($sizeOptions[$variant->size_id])) {
                        $sizeOptions[$variant->size_id] = [
                            'id' => (string) $variant->size_id,
                            'name' => $variant->size->sizeName ?? $variant->size->name ?? ('Size ' . $variant->size_id),
                            'stock' => 0,
                            'has_stock' => false,
                        ];
                    }
                    if ($variant->stock !== null) {
                        $sizeOptions[$variant->size_id]['stock'] += max(0, (int) $variant->stock);
                        $sizeOptions[$variant->size_id]['has_stock'] = true;
                    }
                }
                if ($variant->color_id && $variant->color) {
                    $colorOptions[$variant->color_id] = [
                        'id' => (string) $variant->color_id,
                        'name' => $variant->color->colorName ?? $variant->color->name ?? ('Color ' . $variant->color_id),
                        'hex' => $variant->color->color ?? '',
                    ];
                }
            }
            foreach ($product->sizes ?? [] as $size) {
                if (!isset($sizeOptions[$size->id])) {
                    $sizeOptions[$size->id] = ['id' => (string) $size->id, 'name' => $size->sizeName ?? $size->size_name ?? $size->name ?? '', 'stock' => 0, 'has_stock' => false];
                }
            }
            foreach ($product->colors ?? [] as $color) {
                if (!isset($colorOptions[$color->id])) {
                    $colorOptions[$color->id] = ['id' => (string) $color->id, 'name' => $color->colorName ?? $color->color_name ?? $color->name ?? '', 'hex' => $color->color ?? ''];
                }
            }

            $variantRows = collect($product->variantPrices ?? []);
            $hasVariantStock = $variantRows->contains(fn ($variant) => $variant->stock !== null);
            $totalStock = $hasVariantStock
                ? $variantRows->sum(fn ($variant) => max(0, (int) $variant->stock))
                : (int) ($product->stock ?? 0);

            return [
                'id' => (string) $product->id,
                'name' => strip_tags($product->name ?? ''),
                'price' => (float) $product->new_price,
                'old_price' => (float) $product->old_price,
                'image' => asset(optional($product->image)->image ?? 'public/uploads/default.webp'),
                'stock' => (int) $totalStock,
                'has_stock' => (bool) $hasVariantStock || ($product->stock ?? null) !== null,
                'sizes' => array_values($sizeOptions),
                'colors' => array_values($colorOptions),
                'variants' => optional($product->variantPrices)->map(fn ($variant) => [
                    'size_id' => $variant->size_id ? (string) $variant->size_id : null,
                    'color_id' => $variant->color_id ? (string) $variant->color_id : null,
                    'price' => (float) $variant->price,
                    'stock' => $variant->stock === null ? null : (int) $variant->stock,
                ])->values(),
                'tiers' => collect(),
                'size_chart' => array_values(array_filter(
                    (array) ($product->size_chart ?? []),
                    fn ($row) => is_array($row) && !empty($row['size'])
                )),
            ];
        })->values();
        $gtmItems = $storefrontProducts->map(fn ($product, $index) => [
            'item_id' => $product['id'], 'item_name' => $product['name'], 'price' => $product['price'], 'index' => $index, 'quantity' => 1,
        ])->values();

        // ⭐ Size Chart — প্রথম প্রোডাক্টের চার্টই দেখানো হয় (ক্যাম্পেইন সাধারণত এক প্রোডাক্টের ফানেল)
        $sizeChartSource = ($storefrontProducts ?? collect())->first(fn ($sp) => !empty($sp['size_chart']));
        $sizeChart = is_array($sizeChartSource['size_chart'] ?? null) ? $sizeChartSource['size_chart'] : [];

        // ⭐ ফটো গ্যালারি — প্রোডাক্টের সব ছবি + ক্যাম্পেইনের রিভিউ/গ্যালারি ছবি, ডুপ্লিকেট বাদে
        $galleryImages = collect();
        foreach ($storefrontProducts as $sp) {
            if (!empty($sp['image'])) $galleryImages->push(['src' => $sp['image'], 'alt' => $sp['name']]);
        }
        foreach ($products as $product) {
            foreach ($product->images ?? [] as $image) {
                if (!empty($image->image)) {
                    $galleryImages->push(['src' => asset($image->image), 'alt' => strip_tags($product->name ?? '')]);
                }
            }
        }
        foreach ($campaign_data->images ?? [] as $image) {
            if (!empty($image->image)) {
                $galleryImages->push(['src' => asset($image->image), 'alt' => $campName]);
            }
        }
        $galleryImages = $galleryImages->unique('src')->values();

        // ⭐ WhatsApp — অ্যাডমিন Contact settings থেকে (কলাম না থাকলে চুপচাপ বাদ যাবে)
        $whatsappNumber = preg_replace('/[^0-9]/', '', (string) (optional($contact)->whatsapp ?? ''));
        if ($whatsappNumber !== '' && strlen($whatsappNumber) === 11 && str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '88' . $whatsappNumber;
        }
        $whatsappText = rawurlencode(($campName ?: 'অর্ডার') . ' সম্পর্কে জানতে চাচ্ছি');
        $tokenValues = [
            '{{campaign.name}}' => e($campName),
            '{{campaign.title}}' => e($campName),
            '{{campaign.slug}}' => e($campaign_data->slug ?? ''),
            '{{campaign.deadline}}' => e($campaign_data->deadline ?? ''),
            '{{campaign.description}}' => e($campaignDescription),
            '{{campaign.image}}' => e($campaignImage ? asset($campaignImage) : ''),
            '{{product.name}}' => e($firstProduct?->name ?? ''),
            '{{product.title}}' => e($firstProduct?->name ?? ''),
            '{{product.slug}}' => e($firstProduct?->slug ?? ''),
            '{{product.price}}' => e(number_format((float) ($firstProduct?->new_price ?? 0), 0)),
            '{{product.old_price}}' => e(number_format((float) ($firstProduct?->old_price ?? 0), 0)),
            '{{product.discount}}' => e(number_format($discount, 0)),
            '{{product.image}}' => e($firstProduct ? asset(optional($firstProduct->image)->image ?? 'public/uploads/default.webp') : ''),
            '{{contact.phone}}' => e(optional($contact)->phone ?? ''),
            '{{contact.whatsapp}}' => e(optional($contact)->whatsapp ?? ''),
        ];
        $hasRenderedSource = isset($renderPageHtml);
        $sourceHtml = $hasRenderedSource ? $renderPageHtml : ($campaign_data->page_html ?? '');
        $publishedHtml = strtr((string) $sourceHtml, $tokenValues);
        $publishedCss = $hasRenderedSource
            ? ($renderPageCss ?? '')
            : ($campaign_data->page_css ?? '');
        $publishedJs = $hasRenderedSource ? ($renderPageJs ?? null) : null;
        $pageType = $pageType ?? 'visual';
    @endphp

    <title>{{ $campName }} — {{ optional($generalsetting)->name }}</title>
    <meta name="description" content="{{ $campaignDescription }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $campName }}">
    <meta property="og:description" content="{{ $campaignDescription }}">
    <meta property="og:url" content="{{ route('campaign', $campaign_data->slug) }}">
    <link rel="canonical" href="{{ route('campaign', $campaign_data->slug) }}">
    @if($campaignImage)<meta property="og:image" content="{{ asset($campaignImage) }}">@endif
    <link rel="shortcut icon" href="{{ asset(optional($generalsetting)->favicon) }}" type="image/x-icon">

    {{-- ⭐ স্ট্রাকচার্ড ডেটা (JSON-LD) — সার্চ রেজাল্টে দাম, স্টক ও রেটিং দেখানোর জন্য --}}
    {!! app(\App\Services\ProductSchemaService::class)->campaignScript(
            $campaign_data,
            $products,
            route('campaign', $campaign_data->slug)
       ) !!}
    {{-- bootstrap.min.css (216K), FontAwesome all.css (140K) ও jQuery (88K) সরানো হয়েছে —
         এই পেজের সব স্টাইল campaign-page-renderer.css + builder-এর page_css থেকে আসে
         এবং renderer JS সম্পূর্ণ vanilla। FB ads ট্রাফিকের জন্য ~৩৫০KB সাশ্রয়। --}}
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/campaign-page-renderer.css') }}">
    @if($pageType === 'premium')
        <link rel="stylesheet" href="{{ asset('public/frontEnd/css/campaign-premium.css') }}">
    @endif
    @if(!empty($lpEditor))
        <link rel="stylesheet" href="{{ asset('public/frontEnd/css/landing-editor.css') }}">
    @endif
    <style id="campaign-builder-page-css">{!! $publishedCss !!}</style>

    {{-- Existing Google Analytics/custom tracking configured in General Settings. --}}
    {!! optional($generalsetting)->header_code !!}

    <script>
        window.dataLayer = window.dataLayer || [];
        window._campaignData = {
            id: @json($campId), name: @json($campName), slug: @json($campaign_data->slug),
            page_type: @json($pageType), currency: 'BDT', fb_event_id: @json($fb_view_content_event_id)
        };
        window._campaignProducts = @json($storefrontProducts);
        window.dataLayer.push({
            event: 'campaign_page_loaded', page_type: @json('campaign_landing_' . $pageType),
            campaign_id: @json($campId), campaign_name: @json($campName), currency: 'BDT', value: {{ $campValue }},
            ecommerce: { currency: 'BDT', items: @json($gtmItems) }
        });
    </script>

    @foreach($gtm_code as $gtm)
        @php $gtmContainerId = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-' . trim($gtm->code); @endphp
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f)})(window,document,'script','dataLayer',@json($gtmContainerId));</script>
    @endforeach

    @if($pixels->count() > 0)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            @foreach($pixels as $pixel) fbq('init', @json($pixel->code)); @endforeach
            fbq('track', 'PageView', {}, {eventID: @json('pv_camp' . $campaign_data->id . '_' . time())});
            fbq('track', 'ViewContent', {content_name:@json($campName),content_ids:@json($products->pluck('id')->map(fn($id)=>(string)$id)->values()),content_type:'product',value:{{ $campValue }},currency:'BDT',num_items:{{ $products->count() }}}, {eventID:@json($fb_view_content_event_id)});
        </script>
    @endif

    @if($tiktok_pixels->count() > 0)
        <script>
            !function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript';o.async=!0;o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)}}(window,document,'ttq');
            @foreach($tiktok_pixels as $tiktok) ttq.load(@json($tiktok->code)); @endforeach
            ttq.page();
            ttq.track('ViewContent',{content_name:@json($campName),content_id:@json($campId),content_type:'product',value:{{ $campValue }},currency:'BDT',quantity:1});
        </script>
    @endif
</head>
<body class="{{ !empty($lpEditor) ? 'lp-is-editor' : '' }}">
@if(!empty($lpEditor))
<header class="lp-editor-bar">
    <a href="{{ route('campaign.index') }}">← পেজগুলো</a>
    <strong>{{ $campName }}</strong>
    <span id="lp-save-state">সেভ আছে</span>
    <a href="{{ route('campaign', $campaign_data->slug) }}" target="_blank">লাইভ প্রিভিউ</a>
    <button type="button" id="lp-products-btn">প্রোডাক্ট</button>
    <input type="file" id="lp-file" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
</header>
<aside class="lp-product-drawer" id="lp-product-drawer" hidden>
    <h3>প্রোডাক্ট যোগ/বাদ</h3>
    <form id="lp-product-form">
        @foreach(($allProducts ?? collect()) as $p)
            <label><input type="checkbox" name="product_id[]" value="{{ $p->id }}" @checked($products->contains('id', $p->id))> {{ $p->name }} — ৳{{ number_format($p->new_price) }}</label>
        @endforeach
        <button type="submit">আপডেট</button>
    </form>
</aside>
@endif
    @foreach($gtm_code as $gtm)
        @php $gtmNoscriptId = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-' . trim($gtm->code); @endphp
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmNoscriptId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endforeach
    @foreach($pixels as $pixel)
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixel->code }}&ev=PageView&noscript=1" alt=""></noscript>
    @endforeach

    <main
        id="campaign-builder-storefront"
        class="cpb-published-page cpb-page-{{ $pageType }}"
        data-page-type="{{ $pageType }}"
        data-change-product-url="{{ route('cart.changeProduct') }}"
        data-cart-update-url="{{ route('cart.update') }}"
        data-cart-increment-url="{{ route('cart.increment') }}"
        data-cart-decrement-url="{{ route('cart.decrement') }}"
        data-cart-remove-url="{{ route('cart.remove') }}"
        data-shipping-url="{{ route('shipping.charge') }}"
        data-incomplete-order-url="{{ route('incomplete.order.store') }}"
    >
        {!! $publishedHtml !!}

        {{-- =========================================================
             স্টিকি প্যাকেজ আপসেল বার — "আরেকটি নিলেই বিশেষ মূল্য!"
             ========================================================= --}}
        @php
            $firstProductData = ($storefrontProducts ?? collect())->first();
            $firstTier = collect($firstProductData['tiers'] ?? [])->first(fn ($row) => (int) $row['min'] > 1);
        @endphp
        @if($firstTier && $firstProductData)
            <div class="cpb-upsell-bar is-hidden" data-cpb-upsell role="complementary" aria-label="প্যাকেজ অফার">
                <span class="cpb-upsell-badge" aria-hidden="true">%</span>
                <p>
                    <strong>আরেকটি নিলেই বিশেষ মূল্য!</strong>
                    {{ $firstProductData['name'] ?? '' }}-এর অন্য সাইজ/ভ্যারিয়েশনসহ মোট
                    {{ $firstTier['min'] }} পিস নিলে প্রতি পিস মাত্র <b data-cpb-upsell-price>৳{{ number_format($firstTier['unit'], 0) }}</b>।
                    আর প্রয়োজন <b data-cpb-upsell-need>{{ max(0, (int) $firstTier['min'] - 1) }}</b> পিস।
                </p>
                <button type="button" class="cpb-upsell-close" data-cpb-upsell-close aria-label="বন্ধ করুন">×</button>
            </div>
        @endif
    </main>

    {{-- =========================================================
         ফটো গ্যালারি ও Size Chart — visual builder-এর "Gallery" ও
         "Size Chart" ব্লক দিয়ে যেকোনো জায়গায় বসানো যায়। ব্লক না
         থাকলে renderer JS পেজের শেষে আপনাআপনি বসিয়ে দেয়, তাই পুরনো
         ক্যাম্পেইনগুলোও ভাঙে না।
         ========================================================= --}}
    <template id="cpb-live-gallery-template">
        @if($galleryImages->count() > 1)
            <section class="cpb-gallery-section" id="cpb-gallery" aria-labelledby="cpb-gallery-title">
                <h2 id="cpb-gallery-title">ছবি দেখুন</h2>
                <div class="cpb-gallery">
                    @foreach($galleryImages as $galleryImage)
                        <figure class="cpb-gallery-item">
                            <img src="{{ $galleryImage['src'] }}" alt="{{ $galleryImage['alt'] }}" loading="lazy">
                            <button type="button" class="cpb-zoom" data-cpb-zoom
                                    data-zoom-src="{{ $galleryImage['src'] }}" data-zoom-alt="{{ $galleryImage['alt'] }}">
                                ⌕ বড় করে দেখুন
                            </button>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

    </template>

    <template id="cpb-live-sizechart-template">
        @if(!empty($sizeChart))
            <section class="cpb-sizechart-section" aria-labelledby="cpb-sizechart-title">
                <h2 id="cpb-sizechart-title">Size Chart</h2>
                <button type="button" class="cpb-sizechart-toggle" data-cpb-sizechart-toggle aria-expanded="false" aria-controls="cpb-sizechart-panel">
                    <span>সাইজ মেসারমেন্ট দেখুন</span><i aria-hidden="true">▾</i>
                </button>
                <div id="cpb-sizechart-panel" data-cpb-sizechart-panel hidden>
                    <p class="cpb-sizechart-unit">Unit: inch</p>
                    <table class="cpb-sizechart-table">
                        <thead>
                            <tr><th>Size</th><th>Chest</th><th>Length</th></tr>
                        </thead>
                        <tbody>
                            @foreach($sizeChart as $row)
                                <tr>
                                    <th scope="row">{{ $row['size'] ?? '' }}</th>
                                    <td>{{ $row['chest'] ?? '—' }}</td>
                                    <td>{{ $row['length'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </template>

    {{-- WhatsApp ফ্লোটিং বাটন — অ্যাডমিন Contact settings থেকে নম্বর আসে --}}
    @if(!empty($whatsappNumber))
        <a class="cpb-whatsapp" href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappText }}"
           target="_blank" rel="noopener nofollow" aria-label="WhatsApp-এ মেসেজ করুন">
            <span aria-hidden="true">
                <svg viewBox="0 0 32 32" width="26" height="26" fill="currentColor" aria-hidden="true">
                    <path d="M16 3C9.4 3 4 8.3 4 14.9c0 2.6.8 5 2.3 7L4 29l7.3-2.2c1.5.8 3.1 1.2 4.7 1.2 6.6 0 12-5.3 12-11.9S22.6 3 16 3zm0 21.6c-1.5 0-2.9-.4-4.2-1.1l-.3-.2-4.3 1.3 1.3-4.2-.2-.3c-.8-1.3-1.2-2.8-1.2-4.3 0-5.4 4.4-9.8 9.9-9.8s9.9 4.4 9.9 9.8-4.5 8.8-9.9 8.8zm5.4-6.6c-.3-.2-1.8-.9-2-1-.3-.1-.5-.2-.7.1-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.2-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.6-.1-.2-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.5 5.6 4.9.8.3 1.4.5 1.9.7.8.2 1.5.2 2 .1.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.1-.3-.2-.5-.3z"/>
                </svg>
            </span>
            <b>WhatsApp</b>
        </a>
    @endif

    <template id="cpb-live-products-template">
        @include('frontEnd.layouts.pages.campaign.partials.builder-products')
    </template>
    <template id="cpb-live-reviews-template">
        @forelse($campaign_data->images as $review)
            <article class="cpb-live-review"><img src="{{ asset($review->image) }}" alt="Customer review" loading="lazy"></article>
        @empty
            <p class="cpb-empty-dynamic">এখনো কোনো customer review image যোগ করা হয়নি।</p>
        @endforelse
    </template>
    <template id="cpb-live-checkout-template">
        @include('frontEnd.layouts.pages.campaign.partials.builder-checkout')
    </template>

    {{-- ===== Size/Color popup (storefront style — legacy campaign page-এর মতো) ===== --}}
    <div class="cpb-modal" id="cpb-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="cpb-modal-title">
        <div class="cpb-modal-bg" data-cpb-modal-close></div>
        <div class="cpb-modal-box">
            <div class="cpb-modal-head">
                <h5 id="cpb-modal-title">🛒 সাইজ ও কালার বেছে নিন</h5>
                <button type="button" class="cpb-modal-x" data-cpb-modal-close aria-label="বন্ধ করুন">✕</button>
            </div>
            <div class="cpb-modal-body">
                <div class="cpb-modal-img"><img id="cpb-mo-img" src="" alt="Product"></div>
                <div>
                    <h4 class="cpb-modal-name" id="cpb-mo-name"></h4>
                    <div class="cpb-modal-price">
                        <b id="cpb-mo-price"></b>
                        <del id="cpb-mo-old"></del>
                        <span class="cpb-modal-save" id="cpb-mo-save"></span>
                    </div>
                    <div class="cpb-modal-stock" id="cpb-mo-stock"></div>
                    <div id="cpb-size-wrap" hidden>
                        <p class="cpb-lbl">সাইজ সিলেক্ট করুন <em>*</em></p>
                        <div class="cpb-chips" id="cpb-sizes"></div>
                    </div>
                    <div id="cpb-color-wrap" hidden>
                        <p class="cpb-lbl">কালার সিলেক্ট করুন <em>*</em></p>
                        <div class="cpb-chips" id="cpb-colors"></div>
                    </div>
                    <p class="cpb-lbl">পরিমাণ</p>
                    <div class="cpb-qty">
                        <button type="button" data-cpb-qty="-1" aria-label="কমান">−</button>
                        <input type="text" id="cpb-qty-box" value="1" readonly aria-label="পরিমাণ">
                        <button type="button" data-cpb-qty="1" aria-label="বাড়ান">+</button>
                    </div>
                    <div class="cpb-modal-total"><span>সর্বমোট</span><b id="cpb-mo-total">৳ 0</b></div>
                    <button type="button" class="cpb-modal-confirm" id="cpb-mo-confirm">✓ কনফার্ম করুন — চেকআউটে যোগ হবে</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== নির্বাচিত ভ্যারিয়েন্ট সামারি (checkout-এর উপরে দেখানো হয়) ===== --}}
    <template id="cpb-selected-variant-template">
        <div class="cpb-selected-variant" id="cpb-selected-variant" hidden>
            <span id="cpb-selected-variant-text"></span>
            <button type="button" id="cpb-change-variant">সাইজ/কালার বদলান</button>
        </div>
    </template>

    <div id="cpb-store-loading" class="cpb-store-loading" hidden><span></span><strong>আপনার অর্ডার আপডেট হচ্ছে...</strong></div>
    <button id="cpb-sticky-order" class="cpb-sticky-order" type="button" hidden><span aria-hidden="true">🛍️</span><span>এখনই অর্ডার করুন</span></button>
    <div id="cpb-store-toast" class="cpb-store-toast" role="status" aria-live="polite"></div>

    <script src="{{ asset('public/frontEnd/js/campaign-page-renderer.js') }}"></script>
    @if(filled($publishedJs))
        <script id="campaign-custom-page-js">{!! $publishedJs !!}</script>
    @endif
</body>
</html>
