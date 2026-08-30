{{-- ============================================================
     সেকশন ৬: Need Attention (Alert Strip)
     বর্তমানে: Pending Orders, Incomplete Orders,
               Low Stock Products, Failed Payments
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <div class="w-6 h-6 flex items-center justify-center rounded-full bg-amber-100">
      <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
      </svg>
    </div>
    <h3 class="text-base font-semibold text-gray-900">Need Attention</h3>
    <span class="text-xs text-gray-400">— দ্রুত দেখবেন</span>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    {{-- Pending Orders --}}
    <div class="rounded-xl border-l-4 border-l-amber-400 bg-white p-3 md:p-4 shadow-sm">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-50">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-800">Pending Orders</p>
          <p class="text-xs text-gray-400">পেন্ডিং অর্ডার</p>
        </div>
      </div>
      <p class="mt-3 text-lg font-bold text-amber-700">{{ $pending_orders_count ?? 0 }}</p>
      <a href="{{ route('admin.orders', ['slug' => isset($pendingSlug) ? $pendingSlug : 'pending']) }}"
         class="inline-block mt-1 text-xs font-medium text-amber-600 hover:text-amber-800">
        View orders →
      </a>
    </div>

    {{-- Incomplete Orders --}}
    <div class="rounded-xl border-l-4 border-l-gray-400 bg-white p-3 md:p-4 shadow-sm">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-100">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-800">Incomplete Orders</p>
          <p class="text-xs text-gray-400">অসম্পূর্ণ অর্ডার</p>
        </div>
      </div>
      <p class="mt-3 text-lg font-bold text-gray-700">{{ $incomplete_orders_count ?? 0 }}</p>
      <a href="{{ route('admin.incomplete-orders.index') }}"
         class="inline-block mt-1 text-xs font-medium text-gray-500 hover:text-gray-700">
        View →
      </a>
    </div>

    {{-- Low Stock Products --}}
    <a href="{{ route('admin.stock_alerts.index') }}"
       class="rounded-xl border-l-4 border-l-amber-400 bg-white p-3 md:p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-50">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-800">Low Stock Products</p>
          <p class="text-xs text-gray-400">অল্প স্টক</p>
        </div>
      </div>
      <p class="mt-3 text-lg font-bold text-amber-700">{{ $low_stock_count ?? 0 }}</p>
      <span class="inline-block mt-1 text-xs font-medium text-amber-600 group-hover:text-amber-800">
        Check stock →
      </span>
    </a>

    {{-- Failed Payments --}}
    <div class="rounded-xl border-l-4 border-l-red-400 bg-white p-3 md:p-4 shadow-sm">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-red-50">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-800">Failed Payments</p>
          <p class="text-xs text-gray-400">পেমেন্ট ব্যর্থ</p>
        </div>
      </div>
      <p class="mt-3 text-lg font-bold text-red-700">{{ $failed_payments_count ?? 0 }}</p>
      <a href="{{ route('admin.orders', ['slug' => isset($pendingSlug) ? $pendingSlug : 'pending']) }}"
         class="inline-block mt-1 text-xs font-medium text-red-600 hover:text-red-800">
        Resolve →
      </a>
    </div>
  </div>
</section>
