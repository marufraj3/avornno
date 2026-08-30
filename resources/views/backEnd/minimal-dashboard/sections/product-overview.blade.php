{{-- ============================================================
     সেকশন ৪: Product Overview (৪টি কার্ড)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <h3 class="text-base font-semibold text-gray-900">Product Overview</h3>
    <span class="text-xs text-gray-400">— সামগ্রী তথ্য</span>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
    {{-- Total Products --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Products</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($total_products ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">মোট পোডাক্ট</p>
    </div>

    {{-- Active Products (Green) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-50">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Products</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($active_products ?? 0) }}</p>
      <p class="text-xs text-emerald-600 mt-1">সক্রিয় পোডাক্ট</p>
    </div>

    {{-- Low Stock (Amber/Warning) --}}
    <a href="{{ route('admin.stock_alerts.index') }}"
       class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Low Stock</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($low_stock ?? 0) }}</p>
      <p class="text-xs text-amber-600 mt-1">অল্প স্টক</p>
    </a>

    {{-- Out of Stock (Red) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Out of Stock</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($out_of_stock ?? 0) }}</p>
      <p class="text-xs text-red-600 mt-1">স্টক শেষ</p>
    </div>
  </div>
</section>
