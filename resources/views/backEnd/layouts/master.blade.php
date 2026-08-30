<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title')@if(isset($generalsetting) && $generalsetting) - {{ $generalsetting->name }}@endif</title>
  <link rel="shortcut icon" href="{{ asset(isset($generalsetting->favicon) ? $generalsetting->favicon : 'public/backEnd/assets/images/favicon.ico') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700;9..144,800&family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('public/backEnd/assets/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('public/backEnd/assets/css/icons.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('public/backEnd/assets/css/toastr.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('public/backEnd/assets/css/app.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('public/backEnd/assets/css/admin-fast.css') }}?v=20260830navy" rel="stylesheet" />
  @yield('css')
</head>
<body class="admin-body">
  <div id="nprog"></div>
  <div id="wrapper">
    <header class="navbar-custom">
      <div class="topbar-inner">
        <div class="topbar-left">
          <button type="button" class="button-menu-mobile" id="sidebarToggleBtn" title="Toggle sidebar" aria-label="Toggle sidebar" aria-controls="adminSidebar" aria-expanded="true">
            <span></span><span></span><span></span>
          </button>
          <a class="brand" href="{{ url('admin/dashboard') }}">
            <img src="{{ asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png') }}" alt="logo" />
            <strong>{{ $generalsetting->name ?? 'Admin' }}</strong>
          </a>
        </div>
        <div class="gsearch" id="gsearchBox">
          <input type="search" id="gsearch" placeholder="Search invoice, phone or customer" autocomplete="off" aria-label="Search orders" />
          <div class="gsearch-res" id="gsearchRes"></div>
        </div>
        <div class="topbar-right">
          <button type="button" class="topbar-icon-btn" id="gsearchToggle" aria-label="Toggle search" aria-expanded="false">⌕</button>
          <a class="top-link hide-xs" href="{{ route('home') }}" target="_blank">Visit site</a>
          <span class="topbar-divider hide-xs"></span>
          @if(isset($demoMode) && $demoMode)
          <span class="badge bg-warning">Demo</span>
          @endif
          <div class="dropdown notification-list">
            <a class="top-chip" data-bs-toggle="dropdown" href="#" aria-label="Pending orders">
              <span class="label">Orders</span>
              <span class="count">{{ $neworder ?? 0 }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-orders">
              @forelse(($pendingorder ?? []) as $porder)
              <a href="{{ route('admin.orders',['slug'=>'pending']) }}" class="dropdown-item">
                <b>#{{ $porder->invoice_id }}</b>
                <span>{{ optional($porder->shipping)->phone ?? optional($porder->customer)->phone ?? optional($porder->customer)->name ?? 'Pending order' }}</span>
              </a>
              @empty
              <span class="dropdown-item text-muted">No pending orders</span>
              @endforelse
              <a href="{{ route('admin.orders',['slug'=>'pending']) }}" class="dropdown-item">View all pending</a>
            </div>
          </div>
          <div class="dropdown notification-list">
            <a class="top-user" data-bs-toggle="dropdown" href="#">
              <span class="avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}</span>
              <span class="hide-xs">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <a href="{{ url('admin/dashboard') }}" class="dropdown-item">Dashboard</a>
              <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item">Logout</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
          </div>
        </div>
      </div>
    </header>

    @include('backEnd.layouts.sidebar')

    <div class="content-page">
      <div class="content">
        <div class="content-shell">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <script>window.ADMIN_ORDER_SEARCH = @json(route('admin.order.search'));</script>
  <script src="{{ asset('public/backEnd/assets/js/vendor.min.js') }}"></script>
  <script src="{{ asset('public/backEnd/assets/js/toastr.min.js') }}"></script>
  <script src="{{ asset('public/backEnd/assets/js/admin-fast.js') }}?v=20260828"></script>
  {!! Toastr::message() !!}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      @if(Session::has('success')) if (window.toastr) toastr.success(@json(Session::get('success'))); @endif
      @if(Session::has('error') && !Session::has('demo_mode_blocked')) if (window.toastr) toastr.error(@json(Session::get('error'))); @endif
      @if(Session::has('info')) if (window.toastr) toastr.info(@json(Session::get('info'))); @endif
      @if(Session::has('warning')) if (window.toastr) toastr.warning(@json(Session::get('warning'))); @endif
      @if(Session::has('demo_mode_blocked'))
        if (window.toastr) { toastr.warning('Demo mode: admin data cannot be changed.'); }
      @endif
      @if(isset($demoMode) && $demoMode)
      document.addEventListener('submit', function (e) {
        var action = (e.target.action || '').toLowerCase();
        if (action.indexOf('logout') !== -1) return;
        var method = ((e.target.querySelector('input[name="_method"]') || {}).value || e.target.method || 'get').toLowerCase();
        if (method === 'get') return;
        e.preventDefault();
        if (window.toastr) { toastr.warning('Demo mode: admin data cannot be changed.'); }
      });
      @endif
    });
  </script>
  @yield('script')
</body>
</html>
