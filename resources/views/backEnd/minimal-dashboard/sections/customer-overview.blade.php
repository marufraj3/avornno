{{-- ============================================================
     সেকশন ৫: Customer Overview (৩টি কার্ড)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <h3 class="text-base font-semibold text-gray-900">Customer Overview</h3>
    <span class="text-xs text-gray-400">— গ্রাহক তথ্য</span>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
    {{-- Total Customers --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Customers</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($total_customers ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">মোট গ্রাহক</p>
    </div>

    {{-- New Customers Today (Green) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-50">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">New Today</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($new_customers_today ?? 0) }}</p>
      <p class="text-xs text-emerald-600 mt-1">আজ নতুন গ্রাহক</p>
    </div>

    {{-- Returning Customers --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm group hover:shadow-md transition-shadow">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-brand-50">
          <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
          </svg>
        </div>
        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Returning</span>
      </div>
      <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-3">{{ number_format($returning_customers ?? 0) }}</p>
      <p class="text-xs text-gray-400 mt-1">পুনরায় আসা গ্রাহক</p>
    </div>
  </div>
</section>
