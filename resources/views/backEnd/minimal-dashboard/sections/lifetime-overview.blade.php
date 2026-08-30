{{-- ============================================================
     সেকশন ২: Lifetime Overview (৬টি কার্ড)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <h3 class="text-base font-semibold text-gray-900">Lifetime Overview</h3>
    <span class="text-xs text-gray-400">— সর্বমোট তথ্য</span>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">
    {{-- Total Orders --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Orders</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_order ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">সর্বমোট অর্ডার</p>
    </div>

    {{-- Total Pending (Yellow) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pending</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_pending ?? 0) }}</p>
      <p class="text-xs text-amber-600 mt-1">Pending হয়ে আছে</p>
    </div>

    {{-- Total Confirmed (Green) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-50">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Confirmed</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_confirm ?? 0) }}</p>
      <p class="text-xs text-emerald-600 mt-1">সম্পূর্ণ হয়েছে</p>
    </div>

    {{-- Total Cancelled (Red) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Cancelled</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_cancel ?? 0) }}</p>
      <p class="text-xs text-red-600 mt-1">বাতিল হয়েছে</p>
    </div>

    {{-- Total Shipped (Blue) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Shipped</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_shipped ?? 0) }}</p>
      <p class="text-xs text-blue-600 mt-1">পাঠানো হয়েছে</p>
    </div>

    {{-- Total Incomplete (Gray) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Incomplete</span>
      </div>
      <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($total_incomplete ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">অসম্পূর্ণ অর্ডার</p>
    </div>
  </div>
</section>
