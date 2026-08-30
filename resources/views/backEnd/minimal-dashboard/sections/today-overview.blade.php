{{-- ============================================================
     সেকশন ১: Today's Overview (৬টি ছোট কার্ড)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <h3 class="text-base font-semibold text-gray-900">Today's Overview</h3>
    <span class="text-xs text-gray-400">— {{ now()->format('d M Y') }}</span>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">
    {{-- ১. Today Orders --}}
    <a href="{{ route('admin.orders', ['slug' => 'all']) }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Orders</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_order ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">Created today</p>
    </a>

    {{-- ২. Today Pending (Yellow) --}}
    <a href="{{ route('admin.orders', ['slug' => isset($pendingSlug) ? $pendingSlug : 'pending']) }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 group-hover:bg-amber-100 transition-colors">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_pending ?? 0) }}</p>
      <p class="text-xs text-amber-600 mt-1">Needs action</p>
    </a>

    {{-- ৩. Today Confirmed (Green) --}}
    <a href="{{ route('admin.orders', ['slug' => isset($confirmSlug) ? $confirmSlug : 'confirm']) }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-50 group-hover:bg-emerald-100 transition-colors">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Confirmed</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_confirm ?? 0) }}</p>
      <p class="text-xs text-emerald-600 mt-1">Confirmed today</p>
    </a>

    {{-- ৪. Today Cancelled (Red) --}}
    <a href="{{ route('admin.orders', ['slug' => isset($cancelSlug) ? $cancelSlug : 'cancel']) }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cancelled</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_cancel ?? 0) }}</p>
      <p class="text-xs text-red-600 mt-1">Cancelled today</p>
    </a>

    {{-- ৫. Today Shipped (Blue) --}}
    <a href="{{ route('admin.orders', ['slug' => isset($shippedSlug) ? $shippedSlug : 'shipped']) }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Shipped</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_shipped ?? 0) }}</p>
      <p class="text-xs text-blue-600 mt-1">Dispatched today</p>
    </a>

    {{-- ৬. Today Incomplete (Gray) --}}
    <a href="{{ route('admin.incomplete-orders.index') }}"
       class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm
              hover:shadow-md hover:border-gray-300 transition-all duration-150">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Incomplete</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($today_incomplete ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">Unfinished checkout</p>
    </a>
  </div>
</section>
