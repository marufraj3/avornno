{{-- ============================================================
     সেকশন ৭: Quick Actions (দ্রুত কাজের বাটন)
     ============================================================ --}}
<section class="mb-6 md:mb-8">
  <div class="flex items-center gap-2 mb-3">
    <div class="w-6 h-6 flex items-center justify-center rounded-full bg-brand-100">
      <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
    </div>
    <h3 class="text-base font-semibold text-gray-900">Quick Actions</h3>
    <span class="text-xs text-gray-400">— এক ক্লিকে শুরু করুন</span>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    {{-- Add Product --}}
    <a href="{{ route('products.create') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-brand-50 hover:border-brand-200 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-brand-100 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-brand-600 transition-colors truncate">Add Product</p>
        <p class="text-xs text-gray-400 truncate">নতুন পোডাক্ট যোগ করুন</p>
      </div>
    </a>

    {{-- Create Order --}}
    <a href="{{ route('admin.order.create') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-brand-50 hover:border-brand-200 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-brand-100 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-brand-600 transition-colors truncate">Create Order</p>
        <p class="text-xs text-gray-400 truncate">নতুন অর্ডার তৈরি করুন</p>
      </div>
    </a>

    {{-- Add Category --}}
    <a href="{{ route('categories.create') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-gray-700 transition-colors truncate">Add Category</p>
        <p class="text-xs text-gray-400 truncate">নতুন ক্যাটাগরি যোগ করুন</p>
      </div>
    </a>

    {{-- Create Coupon --}}
    <a href="{{ route('admin.coupons.create') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-gray-700 transition-colors truncate">Create Coupon</p>
        <p class="text-xs text-gray-400 truncate">নতুন কুপন তৈরি করুন</p>
      </div>
    </a>

    {{-- Send SMS --}}
    <a href="{{ route('admin.sms.custom.page') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-gray-700 transition-colors truncate">Send SMS</p>
        <p class="text-xs text-gray-400 truncate">এসএমএস পাঠান</p>
      </div>
    </a>

    {{-- Customer List --}}
    <a href="{{ route('customers.index') }}"
       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm
              hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-150 group">
      <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 group-hover:text-gray-700 transition-colors truncate">Customer List</p>
        <p class="text-xs text-gray-400 truncate">গ্রাহকের তালিকা</p>
      </div>
    </a>
  </div>
</section>
