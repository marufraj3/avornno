@extends('backEnd.layouts.master')
@section('title','Dashboard')

@section('css')
<style>
  /* =====================================================================
     Minimal Dashboard look (matches backEnd/minimal-dashboard design)
     — Tailwind-free: dashboard-scoped CSS only, no extra framework/JS.
     ===================================================================== */

  /* ---------- Section head ---------- */
  .msec-head { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; min-width: 0; flex-wrap: wrap; }
  .msec-title { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
  .msec-sub { font-size: 12px; color: #9ca3af; white-space: nowrap; }
  .msec-ico { width: 24px; height: 24px; flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; }
  .msec-ico svg { width: 14px; height: 14px; }
  .msec-ico--amber { background: #fef3c7; color: #d97706; }
  .msec-ico--brand { background: #d1fae5; color: #059669; }

  /* ---------- Welcome banner (green gradient) ---------- */
  .mhero {
    padding: 20px;
    border-radius: 16px;
    color: #fff;
    background: linear-gradient(to bottom right, #10b981, #047857);
    box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.25), 0 4px 6px -4px rgba(16, 185, 129, 0.25);
  }
  @media (min-width: 768px) { .mhero { padding: 24px 32px; } }
  .mhero-inner { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
  @media (min-width: 768px) { .mhero-inner { flex-direction: row; align-items: center; justify-content: space-between; } }
  .mhero-hello { min-width: 0; }
  .mhero-hello .hi { margin: 0; font-size: 13px; font-weight: 500; color: rgba(255, 255, 255, 0.8); }
  .mhero-hello h2 { margin: 2px 0 0; font-size: 20px; font-weight: 700; color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  @media (min-width: 768px) { .mhero-hello h2 { font-size: 24px; } }
  .mhero-hello .sub { margin: 4px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.7); }
  .mhero-stats { display: flex; flex-direction: column; gap: 12px; min-width: 0; }
  @media (min-width: 640px) { .mhero-stats { flex-direction: row; align-items: center; gap: 16px; } }
  @media (min-width: 768px) { .mhero-stats { gap: 24px; margin-left: auto; } }
  .mstat { min-width: 0; }
  @media (min-width: 640px) { .mstat { text-align: right; } }
  .mstat .lbl { margin: 0; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255, 255, 255, 0.8); }
  .mstat .val { margin: 2px 0 0; font-size: 20px; font-weight: 700; line-height: 1.25; color: #fff; overflow-wrap: anywhere; }
  @media (min-width: 640px) { .mstat .val { font-size: 24px; } }
  @media (min-width: 768px) { .mstat .val { font-size: 30px; } }
  .mstat-div { display: none; width: 1px; height: 48px; background: rgba(255, 255, 255, 0.2); }
  @media (min-width: 640px) { .mstat-div { display: block; } }

  /* ---------- Grids (same breakpoints as the Tailwind design) ---------- */
  .min-w-0 { min-width: 0; }
  .mgrid { display: grid; gap: 12px; min-width: 0; }
  @media (min-width: 768px) { .mgrid { gap: 16px; } }
  .mgrid-6 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  @media (min-width: 640px) { .mgrid-6 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  @media (min-width: 1024px) { .mgrid-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
  .mgrid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  @media (min-width: 1024px) { .mgrid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
  .mgrid-3 { grid-template-columns: 1fr; }
  @media (min-width: 640px) { .mgrid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (min-width: 1024px) { .mgrid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  .mgrid-at { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  @media (min-width: 1024px) { .mgrid-at { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

  /* ---------- Stat card ---------- */
  .mcard {
    display: block; min-width: 0;
    padding: 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: inherit !important;
    text-decoration: none !important;
    transition: box-shadow 0.15s ease, border-color 0.15s ease;
  }
  a.mcard:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border-color: #d1d5db; }
  .mcard-head { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; min-width: 0; }
  .mic { width: 36px; height: 36px; flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
  .mic svg { width: 20px; height: 20px; }
  .mcard-lbl { font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.025em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .mval { margin: 0; font-size: 20px; font-weight: 700; color: #111827; line-height: 1.25; overflow-wrap: anywhere; }
  @media (min-width: 768px) { .mval { font-size: 24px; } }
  .mval-lg { font-size: 24px; }
  @media (min-width: 768px) { .mval-lg { font-size: 30px; } }
  .msub { margin: 4px 0 0; font-size: 12px; color: #9ca3af; }
  .msub--amber   { color: #d97706; }
  .msub--emerald { color: #059669; }
  .msub--red     { color: #dc2626; }
  .msub--blue    { color: #2563eb; }

  /* icon chip colors */
  .mic--gray    { background: #f3f4f6; color: #4b5563; }
  .mic--brand   { background: #ecfdf5; color: #059669; }
  .mic--amber   { background: #fffbeb; color: #d97706; }
  .mic--emerald { background: #ecfdf5; color: #059669; }
  .mic--red     { background: #fef2f2; color: #dc2626; }
  .mic--blue    { background: #eff6ff; color: #2563eb; }
  .mic--purple  { background: #faf5ff; color: #9333ea; }

  /* small pill badge (sales cards) */
  .mpill { margin-left: auto; flex: 0 0 auto; font-size: 11px; font-weight: 500; padding: 4px 8px; border-radius: 9999px; white-space: nowrap; }
  .mpill--brand  { color: #059669; background: #ecfdf5; }
  .mpill--purple { color: #9333ea; background: #faf5ff; }

  /* Total Revenue highlight card */
  .mcard--revenue { border: 2px solid #10b981; background: #ecfdf5; }
  .mcard--revenue .mic { background: #d1fae5; color: #047857; }
  .mcard--revenue .mcard-lbl { color: #047857; }
  .mcard--revenue .mval { color: #047857; }
  .mcard--revenue .msub { color: #059669; }

  /* ---------- Need attention cards (colored left border) ---------- */
  .acard {
    display: block; min-width: 0;
    padding: 14px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-left-width: 4px;
    border-radius: 12px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: inherit !important;
    text-decoration: none !important;
    transition: box-shadow 0.15s ease;
  }
  @media (min-width: 768px) { .acard { padding: 16px; } }
  a.acard:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); }
  .acard-top { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .acard-top .mic { width: 40px; height: 40px; }
  .acard-name { margin: 0; font-size: 14px; font-weight: 600; color: #1f2937; }
  .acard-sub { margin: 0; font-size: 12px; color: #9ca3af; }
  .acard-count { margin: 12px 0 0; font-size: 18px; font-weight: 700; line-height: 1.25; }
  .acard-link { display: inline-block; margin-top: 4px; font-size: 12px; font-weight: 500; }
  .acard--amber { border-left-color: #fbbf24; }
  .acard--amber .acard-count { color: #b45309; }
  .acard--amber .acard-link  { color: #d97706; }
  .acard--gray { border-left-color: #9ca3af; }
  .acard--gray .acard-count { color: #374151; }
  .acard--gray .acard-link  { color: #6b7280; }
  .acard--red { border-left-color: #f87171; }
  .acard--red .acard-count { color: #b91c1c; }
  .acard--red .acard-link  { color: #dc2626; }
  a.acard--amber:hover .acard-link { color: #92400e; }
  a.acard--gray:hover  .acard-link { color: #374151; }
  a.acard--red:hover   .acard-link { color: #991b1b; }

  /* ---------- Quick actions ---------- */
  .qgrid { display: grid; gap: 12px; min-width: 0; grid-template-columns: repeat(2, minmax(0, 1fr)); }
  @media (min-width: 640px) { .qgrid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  @media (min-width: 1024px) { .qgrid { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
  .qa {
    display: flex; align-items: center; gap: 12px; min-width: 0;
    padding: 12px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: inherit !important;
    text-decoration: none !important;
    transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
  }
  @media (min-width: 768px) { .qa { padding: 16px; } }
  .qa:hover { background: #f9fafb; border-color: #d1d5db; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  .qa--brand:hover { background: #ecfdf5; border-color: #a7f3d0; }
  .qa .mic { background: #f3f4f6; color: #4b5563; transition: background-color 0.15s ease, color 0.15s ease; }
  .qa:hover .mic { background: #d1fae5; color: #059669; }
  .qa-name { margin: 0; font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .qa:hover .qa-name { color: #059669; }
  .qa-sub { margin: 0; font-size: 12px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  @media (max-width: 479.98px) {
    .mhero { padding: 18px 16px; }
    .mgrid { gap: 10px; }
  }
</style>
@endsection

@section('content')
@php
    $adminUser = Auth::guard('admin')->user();

    // permission থাকলেই কার্ডে link বসবে, না থাকলে কার্ড non-clickable থাকবে
    $can = function (string $ability): bool {
        try {
            return (bool) (Auth::guard('admin')->user()?->can($ability) ?? false);
        } catch (\Throwable $e) {
            return true;
        }
    };

    $orderList = fn (?string $slug) => route('admin.orders', ['slug' => $slug ?? 'all']);

    // ---- Feather-style inline SVG icons (same as the original design) ----
    $iconPaths = [
        'cart'      => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
        'alert'     => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'check'     => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'         => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'bag'       => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        'dollar'    => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'calendar'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'users'     => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'user-add'  => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        'archive'   => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        'grid'      => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
        'plus'      => 'M12 4v16m8-8H4',
        'folder'    => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        'tag'       => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'message'   => 'M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01',
    ];

    // ছোট helper — svg path থেকে icon string বানায় (আমাদের নিজের static data, তাই {!! !!} নিরাপদ)
    $svg = fn (string $name): string =>
        '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="' . ($iconPaths[$name] ?? $iconPaths['alert']) . '"/></svg>';

    $todayCards = [
        ['lbl' => 'Orders',        'val' => (int) ($today_order ?? 0),     'icon' => 'cart',  'mic' => 'gray',    'sub' => 'Created today'],
        ['lbl' => 'Pending',       'val' => (int) ($today_pending ?? 0),   'icon' => 'alert', 'mic' => 'amber',   'sub' => 'Needs action',        'sub_c' => 'amber'],
        ['lbl' => 'Confirmed',     'val' => (int) ($today_confirm ?? 0),   'icon' => 'check', 'mic' => 'emerald', 'sub' => 'Confirmed today',     'sub_c' => 'emerald'],
        ['lbl' => 'Cancelled',     'val' => (int) ($today_cancel ?? 0),    'icon' => 'x',     'mic' => 'red',     'sub' => 'Cancelled today',     'sub_c' => 'red'],
        ['lbl' => 'Shipped',       'val' => (int) ($today_shipped ?? 0),   'icon' => 'bag',   'mic' => 'blue',    'sub' => 'Dispatched today',    'sub_c' => 'blue'],
        ['lbl' => 'Incomplete',    'val' => (int) ($today_incomplete ?? 0),'icon' => 'alert', 'mic' => 'gray',    'sub' => 'Unfinished checkout',
         'url' => route('admin.incomplete-orders.index'), 'abilities' => ['order-list']],
    ];
    // প্রতিটি today কার্ডের ডিফল্ট link = সংশ্লিষ্ট status-এর order list
    $todayLinks = [
        $orderList('all'), $orderList($pendingSlug ?? 'pending'), $orderList($confirmSlug ?? 'confirm'),
        $orderList($cancelSlug ?? 'cancel'), $orderList($shippedSlug ?? 'shipped'), null,
    ];
    foreach ($todayCards as $i => $c) {
        if (empty($c['url'])) {
            $todayCards[$i]['url'] = $todayLinks[$i];
            $todayCards[$i]['abilities'] = ['order-list'];
        }
    }

    $lifetimeCards = [
        ['lbl' => 'Total Orders',     'val' => (int) ($total_order ?? 0),      'icon' => 'cart',  'mic' => 'gray',    'sub' => 'সর্বমোট অর্ডার'],
        ['lbl' => 'Total Pending',    'val' => (int) ($total_pending ?? 0),    'icon' => 'alert', 'mic' => 'amber',   'sub' => 'Pending হয়ে আছে',   'sub_c' => 'amber'],
        ['lbl' => 'Total Confirmed',  'val' => (int) ($total_confirm ?? 0),    'icon' => 'check', 'mic' => 'emerald', 'sub' => 'সম্পূর্ণ হয়েছে',     'sub_c' => 'emerald'],
        ['lbl' => 'Total Cancelled',  'val' => (int) ($total_cancel ?? 0),     'icon' => 'x',     'mic' => 'red',     'sub' => 'বাতিল হয়েছে',       'sub_c' => 'red'],
        ['lbl' => 'Total Shipped',    'val' => (int) ($total_shipped ?? 0),    'icon' => 'bag',   'mic' => 'blue',    'sub' => 'পাঠানো হয়েছে',      'sub_c' => 'blue'],
        ['lbl' => 'Total Incomplete', 'val' => (int) ($total_incomplete ?? 0), 'icon' => 'alert', 'mic' => 'gray',    'sub' => 'অসম্পূর্ণ অর্ডার',
         'url' => route('admin.incomplete-orders.index'), 'abilities' => ['order-list']],
    ];
    $lifetimeLinks = [
        $orderList('all'), $orderList($pendingSlug ?? 'pending'), $orderList($confirmSlug ?? 'confirm'),
        $orderList($cancelSlug ?? 'cancel'), $orderList($shippedSlug ?? 'shipped'), null,
    ];
    foreach ($lifetimeCards as $i => $c) {
        if (empty($c['url'])) {
            $lifetimeCards[$i]['url'] = $lifetimeLinks[$i];
            $lifetimeCards[$i]['abilities'] = ['order-list'];
        }
    }

    $salesCards = [
        ['lbl' => 'Today',        'val' => '৳ ' . number_format((float) ($today_sales ?? 0), 2), 'icon' => 'dollar',   'mic' => 'brand',
         'sub' => now()->format('d M Y'), 'pill' => '+৳' . number_format((float) ($today_sales ?? 0), 0), 'pill_c' => 'brand'],
        ['lbl' => 'Yesterday',    'val' => '৳ ' . number_format((float) ($yesterday_sales ?? 0), 2), 'icon' => 'dollar', 'mic' => 'gray',
         'sub' => now()->subDay()->format('d M')],
        ['lbl' => 'This Month',   'val' => '৳ ' . number_format((float) ($month_sales ?? 0), 2), 'icon' => 'calendar', 'mic' => 'purple',
         'sub' => now()->format('F Y'), 'pill' => now()->format('M Y'), 'pill_c' => 'purple'],
        ['lbl' => 'Total Revenue','val' => '৳ ' . number_format((float) ($total_revenue ?? 0), 2), 'icon' => 'dollar', 'mic' => 'brand',
         'sub' => 'সর্বমোট আয়', 'special' => true],
    ];

    $productCards = [
        ['lbl' => 'Total Products', 'val' => (int) ($total_products ?? 0),  'icon' => 'grid',  'mic' => 'gray',    'sub' => 'মোট পোডাক্ট',
         'url' => route('inhouse.products.index'), 'abilities' => ['product-list']],
        ['lbl' => 'Active Products','val' => (int) ($active_products ?? 0), 'icon' => 'check', 'mic' => 'emerald', 'sub' => 'সক্রিয় পোডাক্ট', 'sub_c' => 'emerald',
         'url' => route('inhouse.products.index'), 'abilities' => ['product-list']],
        ['lbl' => 'Low Stock',      'val' => (int) ($low_stock ?? 0),       'icon' => 'alert', 'mic' => 'amber',   'sub' => 'অল্প স্টক', 'sub_c' => 'amber',
         'url' => route('admin.stock_alerts.index'), 'abilities' => ['product-list']],
        ['lbl' => 'Out of Stock',   'val' => (int) ($out_of_stock ?? 0),    'icon' => 'x',     'mic' => 'red',     'sub' => 'স্টক শেষ', 'sub_c' => 'red',
         'url' => route('admin.stock_alerts.index'), 'abilities' => ['product-list']],
    ];

    $customerCards = [
        ['lbl' => 'Total Customers', 'val' => (int) ($total_customers ?? 0),     'icon' => 'users',    'mic' => 'gray',    'sub' => 'মোট গ্রাহক',
         'url' => route('customers.index'), 'abilities' => ['customer-list']],
        ['lbl' => 'New Today',       'val' => (int) ($new_customers_today ?? 0), 'icon' => 'user-add', 'mic' => 'emerald', 'sub' => 'আজ নতুন গ্রাহক', 'sub_c' => 'emerald'],
        ['lbl' => 'Returning',       'val' => (int) ($returning_customers ?? 0), 'icon' => 'archive',  'mic' => 'gray',    'sub' => 'আগের গ্রাহক'],
    ];

    $attentionItems = [
        ['name' => 'Pending Orders',     'val' => (int) ($total_pending ?? 0),         'icon' => 'alert', 'mic' => 'amber', 'sub' => 'পেন্ডিং অর্ডার',  'link' => 'View orders →',   'tone' => 'amber',
         'url' => $orderList($pendingSlug ?? 'pending'), 'abilities' => ['order-list']],
        ['name' => 'Incomplete Orders',  'val' => (int) ($total_incomplete ?? 0),      'icon' => 'alert', 'mic' => 'gray',  'sub' => 'অসম্পূর্ণ অর্ডার', 'link' => 'View →',          'tone' => 'gray',
         'url' => route('admin.incomplete-orders.index'), 'abilities' => ['order-list']],
        ['name' => 'Low Stock Products', 'val' => (int) ($low_stock ?? 0),             'icon' => 'alert', 'mic' => 'amber', 'sub' => 'অল্প স্টক',       'link' => 'Check stock →',   'tone' => 'amber',
         'url' => route('admin.stock_alerts.index'), 'abilities' => ['product-list']],
        ['name' => 'Failed Payments',    'val' => (int) ($failed_payments_count ?? 0), 'icon' => 'x',     'mic' => 'red',   'sub' => 'পেমেন্ট ব্যর্থ',   'link' => 'Resolve →',       'tone' => 'red',
         'url' => $orderList('all'), 'abilities' => ['order-list']],
    ];

    $quickActions = [
        ['name' => 'Add Product',   'sub' => 'নতুন পোডাক্ট যোগ করুন',  'icon' => 'plus',    'brand' => true,
         'url' => route('products.create'), 'abilities' => ['product-create']],
        ['name' => 'Create Order',  'sub' => 'নতুন অর্ডার তৈরি করুন',  'icon' => 'bag',     'brand' => true,
         'url' => route('admin.order.create'), 'abilities' => ['order-create']],
        ['name' => 'Add Category',  'sub' => 'নতুন ক্যাটাগরি যোগ করুন', 'icon' => 'folder',
         'url' => route('categories.create'), 'abilities' => ['category-list', 'category-create']],
        ['name' => 'Create Coupon', 'sub' => 'নতুন কুপন তৈরি করুন',    'icon' => 'tag',
         'url' => route('admin.coupons.create'), 'abilities' => ['coupon-list', 'coupon-create']],
        ['name' => 'Send SMS',      'sub' => 'এসএমএস পাঠান',           'icon' => 'message',
         'url' => route('admin.sms.custom.page'), 'abilities' => ['sms-send']],
        ['name' => 'Customer List', 'sub' => 'গ্রাহকের তালিকা',        'icon' => 'users',
         'url' => route('customers.index'), 'abilities' => ['customer-list', 'customer-create', 'customer-edit']],
    ];

    // permission অনুযায়ী URL resolve (না থাকলে null)
    $resolveUrl = function (array $item) use ($can): ?string {
        if (empty($item['url'])) {
            return null;
        }
        if (empty($item['abilities'])) {
            return $item['url'];
        }

        foreach ($item['abilities'] as $ability) {
            if ($can($ability)) {
                return $item['url'];
            }
        }

        return null;
    };
@endphp

<div class="dashboard-shell">

    {{-- ======== Welcome banner ======== --}}
    <section>
        <div class="mhero">
            <div class="mhero-inner">
                <div class="mhero-hello">
                    <p class="hi">Welcome back</p>
                    <h2>{{ $adminUser->name ?? 'Admin' }}</h2>
                    <p class="sub">Today's overview — orders, sales, and stock at a glance.</p>
                </div>
                <div class="mhero-stats">
                    <div class="mstat">
                        <p class="lbl">Today Sales</p>
                        <p class="val">৳ {{ number_format((float) ($today_sales ?? 0), 2) }}</p>
                    </div>
                    <div class="mstat-div"></div>
                    <div class="mstat">
                        <p class="lbl">Today Orders</p>
                        <p class="val">{{ number_format((int) ($today_order ?? 0)) }}</p>
                    </div>
                    <div class="mstat-div"></div>
                    <div class="mstat">
                        <p class="lbl">Pending Orders</p>
                        <p class="val">{{ number_format((int) ($total_pending ?? 0)) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======== Today's Overview ======== --}}
    <section>
        <div class="msec-head">
            <h3 class="msec-title">Today's Overview</h3>
            <span class="msec-sub">— {{ now()->format('d M Y') }}</span>
        </div>
        <div class="mgrid mgrid-6">
            @foreach($todayCards as $card)
                @php $url = $resolveUrl($card); @endphp
                @if($url)
                    <a class="mcard" href="{{ $url }}">
                @else
                    <div class="mcard">
                @endif
                    <div class="mcard-head">
                        <span class="mic mic--{{ $card['mic'] }}">{!! $svg($card['icon']) !!}</span>
                        <span class="mcard-lbl">{{ $card['lbl'] }}</span>
                    </div>
                    <p class="mval">{{ number_format($card['val']) }}</p>
                    <p class="msub {{ !empty($card['sub_c']) ? 'msub--' . $card['sub_c'] : '' }}">{{ $card['sub'] }}</p>
                @if($url)</a>@else</div>@endif
            @endforeach
        </div>
    </section>

    {{-- ======== Lifetime Overview ======== --}}
    <section>
        <div class="msec-head">
            <h3 class="msec-title">Lifetime Overview</h3>
            <span class="msec-sub">— সর্বমোট হিসাব</span>
        </div>
        <div class="mgrid mgrid-6">
            @foreach($lifetimeCards as $card)
                @php $url = $resolveUrl($card); @endphp
                @if($url)
                    <a class="mcard" href="{{ $url }}">
                @else
                    <div class="mcard">
                @endif
                    <div class="mcard-head">
                        <span class="mic mic--{{ $card['mic'] }}">{!! $svg($card['icon']) !!}</span>
                        <span class="mcard-lbl">{{ $card['lbl'] }}</span>
                    </div>
                    <p class="mval">{{ number_format($card['val']) }}</p>
                    <p class="msub {{ !empty($card['sub_c']) ? 'msub--' . $card['sub_c'] : '' }}">{{ $card['sub'] }}</p>
                @if($url)</a>@else</div>@endif
            @endforeach
        </div>
    </section>

    {{-- ======== Sales Overview ======== --}}
    <section>
        <div class="msec-head">
            <h3 class="msec-title">Sales Overview</h3>
            <span class="msec-sub">— আয়ের তথ্য</span>
        </div>
        <div class="mgrid mgrid-4">
            @foreach($salesCards as $card)
                <div class="mcard {{ !empty($card['special']) ? 'mcard--revenue' : '' }}">
                    <div class="mcard-head">
                        <span class="mic mic--{{ $card['mic'] }}">{!! $svg($card['icon']) !!}</span>
                        <span class="mcard-lbl">{{ $card['lbl'] }}</span>
                        @if(!empty($card['pill']))
                            <span class="mpill mpill--{{ $card['pill_c'] }}">{{ $card['pill'] }}</span>
                        @endif
                    </div>
                    <p class="mval mval-lg">{{ $card['val'] }}</p>
                    <p class="msub {{ !empty($card['special']) ? 'msub--emerald' : '' }}">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ======== Product Overview ======== --}}
    <section>
        <div class="msec-head">
            <h3 class="msec-title">Product Overview</h3>
            <span class="msec-sub">— পোডাক্ট ও স্টক</span>
        </div>
        <div class="mgrid mgrid-4">
            @foreach($productCards as $card)
                @php $url = $resolveUrl($card); @endphp
                @if($url)
                    <a class="mcard" href="{{ $url }}">
                @else
                    <div class="mcard">
                @endif
                    <div class="mcard-head">
                        <span class="mic mic--{{ $card['mic'] }}">{!! $svg($card['icon']) !!}</span>
                        <span class="mcard-lbl">{{ $card['lbl'] }}</span>
                    </div>
                    <p class="mval mval-lg">{{ number_format($card['val']) }}</p>
                    <p class="msub {{ !empty($card['sub_c']) ? 'msub--' . $card['sub_c'] : '' }}">{{ $card['sub'] }}</p>
                @if($url)</a>@else</div>@endif
            @endforeach
        </div>
    </section>

    {{-- ======== Customer Overview ======== --}}
    <section>
        <div class="msec-head">
            <h3 class="msec-title">Customer Overview</h3>
            <span class="msec-sub">— গ্রাহক তথ্য</span>
        </div>
        <div class="mgrid mgrid-3">
            @foreach($customerCards as $card)
                @php $url = $resolveUrl($card); @endphp
                @if($url)
                    <a class="mcard" href="{{ $url }}">
                @else
                    <div class="mcard">
                @endif
                    <div class="mcard-head">
                        <span class="mic mic--{{ $card['mic'] }}">{!! $svg($card['icon']) !!}</span>
                        <span class="mcard-lbl">{{ $card['lbl'] }}</span>
                    </div>
                    <p class="mval mval-lg">{{ number_format($card['val']) }}</p>
                    <p class="msub {{ !empty($card['sub_c']) ? 'msub--' . $card['sub_c'] : '' }}">{{ $card['sub'] }}</p>
                @if($url)</a>@else</div>@endif
            @endforeach
        </div>
    </section>

    {{-- ======== Need Attention ======== --}}
    <section>
        <div class="msec-head">
            <span class="msec-ico msec-ico--amber">{!! $svg('alert') !!}</span>
            <h3 class="msec-title">Need Attention</h3>
            <span class="msec-sub">— দ্রুত দেখবেন</span>
        </div>
        <div class="mgrid mgrid-at">
            @foreach($attentionItems as $item)
                @php $url = $resolveUrl($item); @endphp
                @if($url)
                    <a class="acard acard--{{ $item['tone'] }}" href="{{ $url }}">
                @else
                    <div class="acard acard--{{ $item['tone'] }}">
                @endif
                    <div class="acard-top">
                        <span class="mic mic--{{ $item['mic'] }}">{!! $svg($item['icon']) !!}</span>
                        <div class="min-w-0">
                            <p class="acard-name">{{ $item['name'] }}</p>
                            <p class="acard-sub">{{ $item['sub'] }}</p>
                        </div>
                    </div>
                    <p class="acard-count">{{ number_format($item['val']) }}</p>
                    @if($url)
                        <span class="acard-link">{{ $item['link'] }}</span>
                    @endif
                @if($url)</a>@else</div>@endif
            @endforeach
        </div>
    </section>

    {{-- ======== Quick Actions ======== --}}
    <section>
        <div class="msec-head">
            <span class="msec-ico msec-ico--brand">{!! $svg('plus') !!}</span>
            <h3 class="msec-title">Quick Actions</h3>
            <span class="msec-sub">— এক ক্লিকে শুরু করুন</span>
        </div>
        <div class="qgrid">
            @foreach($quickActions as $action)
                @php $url = $resolveUrl($action); @endphp
                @if($url)
                    <a class="qa {{ !empty($action['brand']) ? 'qa--brand' : '' }}" href="{{ $url }}">
                        <span class="mic">{!! $svg($action['icon']) !!}</span>
                        <div class="min-w-0">
                            <p class="qa-name">{{ $action['name'] }}</p>
                            <p class="qa-sub">{{ $action['sub'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

</div>
@endsection
