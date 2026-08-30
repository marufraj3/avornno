{{-- ============================================================
     সেকশন ৩: Sales Overview (৪টি কার্ড)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <h3 class="text-base font-semibold text-gray-900">Sales Overview</h3>
    <span class="text-xs text-gray-400">— আয়ের তথ্য</span>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
    {{-- Today's Sales --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-brand-50">
            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today</span>
        </div>
        <span class="text-xs font-medium text-brand-600 bg-brand-50 px-2 py-1 rounded-full">+৳{{ number_format($today_sales ?? 0, 0) }}</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">৳ {{ number_format($today_sales ?? 0, 2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
    </div>

    {{-- Yesterday Sales --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Yesterday</span>
        </div>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">৳ {{ number_format($yesterday_sales ?? 0, 2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ Carbon\Carbon::yesterday()->format('d M') }}</p>
    </div>

    {{-- This Month Sales --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-purple-50">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">This Month</span>
        </div>
        <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">{{ Carbon\Carbon::now()->format('M Y') }}</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">৳ {{ number_format($month_sales ?? 0, 2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ Carbon\Carbon::now()->format('F Y') }}</p>
    </div>

    {{-- Total Revenue --}}
    <div class="rounded-xl border-2 border-brand-500 bg-brand-50 p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-brand-100">
          <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-brand-700 uppercase tracking-wide">Total Revenue</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-brand-700 mt-3">৳ {{ number_format($total_revenue ?? 0, 2) }}</p>
      <p class="text-xs text-brand-600 mt-1">সর্বমোট আয়</p>
    </div>
  </div>
</section>
