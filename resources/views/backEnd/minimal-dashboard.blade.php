<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') @if(isset($generalsetting) && $generalsetting) — {{ $generalsetting->name }}@endif</title>
  <link rel="shortcut icon" href="{{ asset(isset($generalsetting->favicon) ? $generalsetting->favicon : 'public/backEnd/assets/images/favicon.ico') }}" />

  {{-- Tailwind CSS (CDN for rapid development; replace with build step for production) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#ecfdf5',
              100: '#d1fae5',
              200: '#a7f3d0',
              300: '#6ee7b7',
              400: '#34d399',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
              800: '#065f46',
              900: '#064e3b',
            },
          },
          fontFamily: {
            sans: ['Nunito', 'ui-sans-serif', 'system-ui', 'sans-serif'],
          },
        },
      },
    }
  </script>

  @stack('head')
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased">

  {{-- ==================== MOBILE HEADER ==================== --}}
  <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 shadow-sm md:hidden">
    <div class="flex items-center justify-between px-4 h-16">
      <button id="mobileMenuToggle"
              class="p-2 -ml-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500"
              aria-label="Toggle menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <a href="{{ url('admin/dashboard') }}" class="flex items-center gap-2">
        <img src="{{ asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png') }}"
             class="h-7 w-auto" alt="logo" />
        <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">
          {{ $generalsetting->name ?? 'Admin' }}
        </span>
      </a>

      <div class="flex items-center gap-2">
        @if(isset($demoMode) && $demoMode)
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Demo</span>
        @endif
        <div class="relative">
          <button id="mobileUserMenuBtn"
                  class="p-2 -mr-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500"
                  aria-label="Account menu">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-100 text-brand-700 text-sm font-bold">
              {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
            </span>
          </button>
          <div id="mobileUserMenu"
               class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 hidden">
            <div class="px-4 py-2 border-b border-gray-100">
              <p class="text-sm font-semibold text-gray-800 truncate">
                {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
              </p>
              <p class="text-xs text-gray-500 truncate">admin@{{ $generalsetting?->name ?? 'admin' }}.com</p>
            </div>
            <a href="{{ route('admin.minimal_dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Dashboard
            </a>
            <a href="{{ route('change_password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Change Password
            </a>
            <form action="{{ route('logout') }}" method="POST" class="block">
              @csrf
              <button type="submit"
                      class="flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </header>

  {{-- ==================== SIDEBAR (DESKTOP + MOBILE DRAWER) ==================== --}}
  <aside id="sidebar"
         class="fixed top-0 left-0 bottom-0 z-50 w-64 bg-white border-r border-gray-200
                transform -translate-x-full md:translate-x-0
                transition-transform duration-200 ease-in-out md:relative md:block
                flex flex-col shadow-sm">

    {{-- Mobile close button (inside drawer) --}}
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200 md:hidden">
      <span class="text-sm font-semibold text-gray-700">Menu</span>
      <button id="sidebarCloseBtn"
              class="p-2 -mr-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- Brand card (slim) --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-100 text-brand-700 text-sm font-bold">
        {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
      </span>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-800 truncate">
          {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
        </p>
        <p class="text-xs text-gray-500">Signed in</p>
      </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1" id="sidebarMenu">
      {{-- প্রতিটি মেনু আইটেম brand-500 কালারে, অ্যাক্টিভ হলে ব্যাকগ্রাউন্ড হালকা --}}
      @php
        $menuItems = [
          ['route' => 'admin.minimal_dashboard',  'label' => 'Dashboard',                'icon' => 'airplay'],
          ['route' => 'admin.order.create',       'label' => 'POS System',                'icon' => 'shopping-cart'],
          ['route' => 'admin.orders',             'label' => 'All Orders',                'icon' => 'shopping-bag', 'params' => ['slug' => 'all']],
          ['route' => 'admin.incomplete-orders.index', 'label' => 'Incomplete Orders',    'icon' => 'alert-triangle'],
          ['route' => 'inhouse.products.index',   'label' => 'All Products',              'icon' => 'grid'],
          ['route' => 'products.create',          'label' => 'Add Product',               'icon' => 'plus-circle'],
          ['route' => 'categories.index',         'label' => 'Categories',               'icon' => 'folder'],
          ['route' => 'customers.index',          'label' => 'Customers',                'icon' => 'users'],
          ['route' => 'admin.coupons.index',      'label' => 'Coupons',                  'icon' => 'tag'],
          ['route' => 'admin.reports.orders',     'label' => 'Reports',                  'icon' => 'bar-chart-2'],
          ['route' => 'settings.index',           'label' => 'Settings',                 'icon' => 'settings'],
        ];
      @endphp

      @foreach($menuItems as $item)
        @php
          $isActive = request()->routeIs($item['route']);
          $url = isset($item['params'])
              ? route($item['route'], $item['params'])
              : route($item['route']);
        @endphp
        <a href="{{ $url }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  transition-colors
                  {{ $isActive
                     ? 'bg-brand-50 text-brand-700'
                     : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
           aria-current="{{ $isActive ? 'page' : 'false' }}"
           onclick="closeMobileMenu()">
          {{-- ইকন (ডায়নামিক — সরল SVG) --}}
          @include('backEnd.partials.sidebar-icon', ['icon' => $item['icon']])
          <span class="truncate">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>

    {{-- Footer --}}
    <div class="px-3 py-4 border-t border-gray-200">
      <a href="{{ route('home') }}"
         target="_blank"
         rel="noopener"
         class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-8m-2 14l-4-4h2.5l3 3 3-3h3.5l-4-4z"/>
        </svg>
        Visit storefront
      </a>
    </div>
  </aside>

  {{-- Background overlay (mobile only) --}}
  <div id="sidebarOverlay"
       class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm hidden md:hidden"
       onclick="closeMobileMenu()"></div>

  {{-- ==================== MAIN LAYOUT ==================== --}}
  <div class="min-h-screen flex flex-col md:pl-64 pt-16 md:pt-0">

    {{-- ===== Desktop Top Bar ===== --}}
    <header class="hidden md:flex sticky top-0 items-center justify-between gap-4 px-6 py-3 bg-white border-b border-gray-200 shadow-sm z-30">

      <div class="flex items-center gap-4 flex-1 min-w-0">
        <h1 class="text-lg font-semibold text-gray-900 truncate">Dashboard</h1>
        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    bg-brand-50 text-brand-700 border border-brand-200">
          {{ now()->format('l, d M Y') }}
        </span>
      </div>

      <div class="flex items-center gap-4">

        {{-- Global search --}}
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
               stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input type="search"
                 id="globalSearch"
                 placeholder="Search invoice or phone…"
                 autocomplete="off"
                 class="w-48 lg:w-60 pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200
                        bg-gray-50 text-gray-700 placeholder-gray-400
                        focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" />
        </div>

        {{-- Visit site --}}
        <a href="{{ route('home') }}"
           target="_blank"
           class="hidden lg:inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-8m-2 14l-4-4h2.5l3 3 3-3h3.5l-4-4z"/>
          </svg>
          Visit Site
        </a>

        {{-- User avatar dropdown --}}
        <div class="relative">
          <button id="userMenuBtn"
                  class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg border border-gray-200 bg-white
                         text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-brand-100 text-brand-700 text-xs font-bold">
              {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
            </span>
            <span class="hidden sm:inline text-gray-700 font-medium">
              {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div id="userMenu"
               class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-1 hidden">
            <div class="px-4 py-2 border-b border-gray-100">
              <p class="text-sm font-semibold text-gray-800 truncate">
                {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
              </p>
              <p class="text-xs text-gray-500 truncate">Administrator</p>
            </div>
            <a href="{{ route('admin.minimal_dashboard') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Dashboard
            </a>
            <a href="{{ route('change_password') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Change Password
            </a>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit"
                      class="flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </header>

    {{-- ===== MOBILE TOP BAR (inside scrollable content) ===== --}}
    <div class="md:hidden px-4 pt-14 pb-2 bg-white border-b border-gray-200 sticky top-14 z-20">
      <div class="flex items-center justify-between gap-2">
        <h1 class="text-base font-semibold text-gray-900 truncate">Dashboard</h1>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    bg-brand-50 text-brand-700 border border-brand-200 whitespace-nowrap">
          {{ now()->format('d M') }}
        </span>
      </div>
    </div>

    {{-- ===== SCROLLABLE CONTENT AREA ===== --}}
    <main class="flex-1 px-4 pb-10 md:px-6 lg:px-8 pt-2 md:pt-6 bg-gray-50">

      @include('backEnd.minimal-dashboard.sections.welcome')

      @include('backEnd.minimal-dashboard.sections.today-overview')

      @include('backEnd.minimal-dashboard.sections.lifetime-overview')

      @include('backEnd.minimal-dashboard.sections.sales-overview')

      @include('backEnd.minimal-dashboard.sections.product-overview')

      @include('backEnd.minimal-dashboard.sections.customer-overview')

      @include('backEnd.minimal-dashboard.sections.need-attention')

      @include('backEnd.minimal-dashboard.sections.quick-actions')

      {{-- Footer (minimal) --}}
      <footer class="mt-10 text-center text-xs text-gray-400 border-t border-gray-200 pt-4">
        &copy; {{ now()->year }} {{ $generalsetting?->name ?? config('app.name', 'Admin Panel') }}.
        Built with Laravel &amp; Tailwind CSS.
      </footer>
    </main>
  </div>

  {{-- ==================== SCRIPTS ==================== --}}
  <script>
    window.addEventListener('DOMContentLoaded', function () {
      // ---- Mobile menu toggle ----
      const sidebar    = document.getElementById('sidebar');
      const overlay    = document.getElementById('sidebarOverlay');
      const openBtn    = document.getElementById('mobileMenuToggle');
      const closeBtn   = document.getElementById('sidebarCloseBtn');

      function openMobileMenu() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      }
      function closeMobileMenu() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
      if (openBtn)  openBtn.addEventListener('click', openMobileMenu);
      if (closeBtn) closeBtn.addEventListener('click', closeMobileMenu);

      // ---- User dropdown (desktop + mobile) ----
      function setupDropdown(btnId, menuId) {
        const btn  = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          const isHidden = menu.classList.contains('hidden');
          document.querySelectorAll('[id^="userMenu"], [id^="mobileUserMenu"]').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
          });
          menu.classList.toggle('hidden', !isHidden);
        });
        document.addEventListener('click', function (e) {
          if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
          }
        });
      }
      setupDropdown('userMenuBtn', 'userMenu');
      setupDropdown('mobileUserMenuBtn', 'mobileUserMenu');

      // ---- Simple global search (placeholder behavior) ----
      const searchInput = document.getElementById('globalSearch');
      if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' && this.value.trim()) {
            const q = encodeURIComponent(this.value.trim());
            window.location.href = '{{ route('admin.order.search') }}?q=' + q;
          }
        });
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
