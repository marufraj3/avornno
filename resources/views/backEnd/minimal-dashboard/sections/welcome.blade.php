{{-- ============================================================
     সেকশন ০: Welcome Banner
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl px-5 py-5 md:px-8 md:py-6 text-white shadow-lg shadow-brand-500/20">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div class="min-w-0">
        <p class="text-white/80 text-sm font-medium">Welcome back</p>
        <h2 class="text-xl md:text-2xl font-bold mt-0.5 truncate">
          {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
        </h2>
        <p class="text-white/70 text-sm mt-1">
          Today's overview — orders, sales, and stock at a glance.
        </p>
      </div>
      <div class="flex items-center gap-4 md:gap-6 flex-shrink-0">
        <div class="text-right">
          <p class="text-white/80 text-xs font-medium uppercase tracking-wider">Today Sales</p>
          <p class="text-2xl md:text-3xl font-bold mt-0.5">৳ {{ number_format($today_sales ?? 0, 2) }}</p>
        </div>
        <div class="h-12 w-px bg-white/20 hidden sm:block"></div>
        <div class="text-right">
          <p class="text-white/80 text-xs font-medium uppercase tracking-wider">Today Orders</p>
          <p class="text-2xl md:text-3xl font-bold mt-0.5">{{ number_format($today_order ?? 0) }}</p>
        </div>
        <div class="h-12 w-px bg-white/20 hidden sm:block"></div>
        <div class="text-right">
          <p class="text-white/80 text-xs font-medium uppercase tracking-wider">Pending Orders</p>
          <p class="text-2xl md:text-3xl font-bold mt-0.5">{{ number_format($today_pending ?? 0) }}</p>
        </div>
      </div>
    </div>
  </div>
</section>
