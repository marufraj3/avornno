<div class="left-side-menu" id="adminSidebar">
  <div id="sidebar-menu">
    <div class="sidebar-close">
      <strong>Menu</strong>
      <button type="button" id="sidebarCloseBtn" aria-label="Close menu"><i class="fe-x"></i></button>
    </div>
    <div class="sidebar-search">
      <div class="search-wrap">
        <input type="text" id="sidebarMenuSearch" placeholder="Search menu..." autocomplete="off" />
      </div>
      <div class="no-result" id="sidebarSearchNoResult">No menu found</div>
    </div>

    @php
      $pending_reviews = \Illuminate\Support\Facades\Cache::remember('sidebar_pending_reviews_count', 120, function () {
          return \App\Models\Review::where('status', 'pending')->count();
      });
      $stockAlertCount = \Illuminate\Support\Facades\Cache::remember('sidebar_stock_alert_count', 120, function () {
          try {
              return app(\App\Services\StockAlertService::class)->unreadCount();
          } catch (\Throwable $e) {
              return 0;
          }
      });
      $salesOpen = request()->routeIs('admin.orders', 'admin.incomplete-orders.*', 'orderstatus.*', 'customers.ip_block', 'admin.refunds.*', 'backEnd.complaints.*', 'manualFraud.*');
      $catalogOpen = request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'reviews.*', 'admin.stock_alerts.*');
      $purchaseOpen = request()->routeIs('purchases.*', 'admin.suppliers.*');
      $marketOpen = request()->routeIs('admin.coupons.*', 'admin.order_bumps.*', 'campaign.*', 'banners.*', 'admin.popup.*', 'admin.sms.custom.*', 'admin.newsletter.subscribers*', 'admin.facebook_page.*', 'admin.ads_analytics.*', 'tagmanagers.*', 'pixels.*', 'tiktok.pixels.*');
      $contentOpen = request()->routeIs('admin.blog.*', 'admin.contact.messages*');
      $financeOpen = request()->routeIs('admin.fund.*', 'admin.expenses.*', 'admin.reports.*');
      $teamOpen = request()->routeIs('admin.employees.*', 'admin.attendances.*', 'admin.leaves.*', 'admin.salaries.*', 'admin.bonuses.*', 'admin.salary_payments.*', 'users.*', 'roles.*', 'permissions.*', 'customers.index');
      $setOpen = request()->routeIs('settings.*', 'socialmedias.*', 'contact.index', 'pages.*', 'shippingcharges.*', 'email_setting*', 'admin.seo_settings.*', 'admin.sitemap.*', 'admin.fraud.*', 'admin.order.restriction.*', 'paymentgeteway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.cron.*');
      $adminUser = Auth::guard('admin')->user();
    @endphp

    <div class="sidebar-brand-card sidebar-brand-card--slim">
      <div class="sidebar-brand-card__avatar">{{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}</div>
      <div class="sidebar-brand-card__body">
        <strong>{{ $adminUser->name ?? 'Admin' }}</strong>
        <span>Signed in</span>
      </div>
    </div>

    <ul id="side-menu">
      <li class="{{ request()->is('admin/dashboard') ? 'menuitem-active' : '' }}">
        <a href="{{ url('admin/dashboard') }}"><i class="fe-airplay"></i><span>Dashboard</span></a>
      </li>
      <li class="{{ request()->routeIs('admin.gemini.*') ? 'menuitem-active' : '' }}">
        <a href="{{ route('admin.gemini.index') }}"><i class="fe-cpu"></i><span>Gemini Assistant</span><span class="badge badge-ai">AI</span></a>
      </li>
      @can('order-create')
      <li class="{{ request()->routeIs('admin.order.create') ? 'menuitem-active' : '' }}">
        <a href="{{ route('admin.order.create') }}"><i class="fe-shopping-cart"></i><span>POS System</span></a>
      </li>
      @endcan

      @canany(['order-list', 'order-edit', 'order-create', 'complaint-list', 'fraud-check'])
      <li class="has-sub {{ $salesOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-sales" data-sidebar-toggle><i class="fe-shopping-bag"></i><span>Sales</span>@if(($neworder ?? 0) > 0)<span class="badge bg-danger">{{ $neworder }}</span>@endif<span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $salesOpen ? 'show' : '' }}" id="sidebar-sales">
          <ul class="nav-second-level">
            @can('order-list')
            <li><a href="{{ route('admin.orders', ['slug' => 'all']) }}">All Orders</a></li>
            <li><a href="{{ route('admin.incomplete-orders.index') }}">Incomplete Orders</a></li>
            @foreach($orderstatus ?? [] as $value)
            <li><a href="{{ route('admin.orders', ['slug' => $value->slug]) }}">{{ $value->name }}</a></li>
            @endforeach
            @endcan
            @can('order-edit')
            <li><a href="{{ route('orderstatus.index') }}">Order Status</a></li>
            @endcan
            @canany(['order-list', 'order-edit'])
            <li><a href="{{ route('admin.refunds.index') }}">Refunds</a></li>
            @endcanany
            @canany(['complaint-list', 'complaint-create', 'complaint-edit'])
            <li><a href="{{ route('backEnd.complaints.index') }}">Complaints</a></li>
            @endcanany
            @can('fraud-check')
            <li><a href="{{ route('manualFraud.page') }}">Fraud Check</a></li>
            @endcan
            @can('order-manage')
            <li><a href="{{ route('customers.ip_block') }}">IP Block</a></li>
            @endcan
          </ul>
        </div>
      </li>
      @endcanany

      @canany(['product-list', 'product-create', 'category-list', 'subcategory-list', 'childcategory-list', 'review-list'])
      <li class="has-sub {{ $catalogOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-catalog" data-sidebar-toggle><i class="fe-grid"></i><span>Catalog</span>@if($stockAlertCount > 0)<span class="badge bg-danger">{{ $stockAlertCount }}</span>@endif<span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $catalogOpen ? 'show' : '' }}" id="sidebar-catalog">
          <ul class="nav-second-level">
            @can('product-list')
            <li><a href="{{ route('inhouse.products.index') }}">All Products</a></li>
            <li><a href="{{ route('products.pending') }}">Pending Products</a></li>
            @endcan
            @can('product-create')
            <li><a href="{{ route('products.create') }}">Add Product</a></li>
            @endcan
            @can('category-list')
            <li><a href="{{ route('categories.index') }}">Categories</a></li>
            @endcan
            @can('subcategory-list')
            <li><a href="{{ route('subcategories.index') }}">Subcategories</a></li>
            @endcan
            @can('childcategory-list')
            <li><a href="{{ route('childcategories.index') }}">Childcategories</a></li>
            @endcan
            @canany(['brand-list', 'brand-create', 'brand-edit'])
            <li><a href="{{ route('brands.index') }}">Brands</a></li>
            @endcanany
            @canany(['color-list', 'color-create', 'color-edit'])
            <li><a href="{{ route('colors.index') }}">Colors</a></li>
            @endcanany
            @canany(['size-list', 'size-create', 'size-edit'])
            <li><a href="{{ route('sizes.index') }}">Sizes</a></li>
            @endcanany
            @can('review-list')
            <li><a href="{{ route('reviews.index') }}">Reviews @if($pending_reviews > 0)<span class="badge bg-warning">{{ $pending_reviews }}</span>@endif</a></li>
            @endcan
            <li>
              <a href="{{ route('admin.stock_alerts.index') }}">
                Stock Alerts
                @if ($stockAlertCount > 0)<span class="badge bg-danger">{{ $stockAlertCount }}</span>@endif
              </a>
            </li>
          </ul>
        </div>
      </li>
      @endcanany

      @canany(['purchase-list', 'purchase-create', 'purchase-edit', 'supplier-list', 'supplier-create', 'supplier-edit'])
      <li class="has-sub {{ $purchaseOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-purchase" data-sidebar-toggle><i class="fe-truck"></i><span>Purchasing</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $purchaseOpen ? 'show' : '' }}" id="sidebar-purchase">
          <ul class="nav-second-level">
            @canany(['purchase-list', 'purchase-create', 'purchase-edit'])
            <li><a href="{{ route('purchases.index') }}">Purchases</a></li>
            @endcanany
            @canany(['supplier-list', 'supplier-create', 'supplier-edit'])
            <li><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
            @endcanany
          </ul>
        </div>
      </li>
      @endcanany

      <li class="has-sub {{ $marketOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-market" data-sidebar-toggle><i class="fe-volume-2"></i><span>Marketing</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $marketOpen ? 'show' : '' }}" id="sidebar-market">
          <ul class="nav-second-level">
            @canany(['coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete'])
            <li><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
            <li><a href="{{ route('admin.order_bumps.index') }}">Order Bumps</a></li>
            @endcanany
            @canany(['campaign-list', 'campaign-create'])
            <li><a href="{{ route('campaign.index') }}">Landing Pages</a></li>
            @endcanany
            @canany(['banner-list'])
            <li><a href="{{ route('banners.index') }}">Banner &amp; Sliders</a></li>
            @endcanany
            @canany(['popup-list', 'popup-manage'])
            <li><a href="{{ route('admin.popup.index') }}">Popup Offer</a></li>
            @endcanany
            @can('sms-send')
            <li><a href="{{ route('admin.sms.custom.page') }}">Send SMS</a></li>
            @endcan
            <li><a href="{{ route('admin.newsletter.subscribers') }}">Newsletter</a></li>
            @canany(['pixel-manage'])
            <li><a href="{{ route('admin.facebook_page.settings') }}">Facebook Page</a></li>
            <li><a href="{{ route('admin.ads_analytics.dashboard') }}">Live Ads</a></li>
            <li><a href="{{ route('pixels.index') }}">Pixel &amp; GTM</a></li>
            @endcanany
          </ul>
        </div>
      </li>

      @canany(['blog-list', 'blog-create', 'contact-list'])
      <li class="has-sub {{ $contentOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-content" data-sidebar-toggle><i class="fe-file-text"></i><span>Content</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $contentOpen ? 'show' : '' }}" id="sidebar-content">
          <ul class="nav-second-level">
            @canany(['blog-list', 'blog-create', 'blog-edit'])
            <li><a href="{{ route('admin.blog.index') }}">Blog</a></li>
            @endcanany
            @can('contact-list')
            <li><a href="{{ route('admin.contact.messages') }}">Contact Messages</a></li>
            @endcan
          </ul>
        </div>
      </li>
      @endcanany

      @canany(['fund-list', 'expense-list', 'report-view', 'order-report', 'purchase-report', 'expense-report', 'stock-report', 'profit-loss-report'])
      <li class="has-sub {{ $financeOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-finance" data-sidebar-toggle><i class="fe-bar-chart"></i><span>Finance</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $financeOpen ? 'show' : '' }}" id="sidebar-finance">
          <ul class="nav-second-level">
            @canany(['fund-list', 'fund-create', 'fund-edit'])
            <li><a href="{{ route('admin.fund.index') }}">Fund / Account</a></li>
            @endcanany
            @canany(['expense-list', 'expense-create', 'expense-edit'])
            <li><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
            @endcanany
            @canany(['order-report', 'report-view'])
            <li><a href="{{ route('admin.reports.orders') }}">Order Report</a></li>
            @endcanany
            @canany(['purchase-report', 'report-view'])
            <li><a href="{{ route('admin.reports.purchases') }}">Purchase Report</a></li>
            @endcanany
            @canany(['expense-report', 'report-view'])
            <li><a href="{{ route('admin.reports.expenses') }}">Expense Report</a></li>
            @endcanany
            @canany(['stock-report', 'report-view'])
            <li><a href="{{ route('admin.reports.stock') }}">Stock Report</a></li>
            @endcanany
            @canany(['profit-loss-report', 'report-view'])
            <li><a href="{{ route('admin.reports.profit_loss') }}">Profit &amp; Loss</a></li>
            @endcanany
          </ul>
        </div>
      </li>
      @endcanany

      <li class="has-sub {{ $teamOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-team" data-sidebar-toggle><i class="fe-users"></i><span>Team</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $teamOpen ? 'show' : '' }}" id="sidebar-team">
          <ul class="nav-second-level">
            <li><a href="{{ route('admin.employees.index') }}">Employees</a></li>
            <li><a href="{{ route('admin.attendances.index') }}">Attendance</a></li>
            <li><a href="{{ route('admin.leaves.index') }}">Leaves</a></li>
            <li><a href="{{ route('admin.salaries.index') }}">Salaries</a></li>
            <li><a href="{{ route('admin.bonuses.index') }}">Bonuses</a></li>
            <li><a href="{{ route('admin.salary_payments.index') }}">Salary Payments</a></li>
            @can('user-list')
            <li><a href="{{ route('users.index') }}">Users</a></li>
            @endcan
            @can('role-list')
            <li><a href="{{ route('roles.index') }}">Roles</a></li>
            @endcan
            @can('permission-list')
            <li><a href="{{ route('permissions.index') }}">Permissions</a></li>
            @endcan
            @canany(['customer-list', 'customer-create', 'customer-edit'])
            <li><a href="{{ route('customers.index') }}">Customers</a></li>
            @endcanany
          </ul>
        </div>
      </li>

      @canany(['setting-list', 'setting-edit', 'social-list', 'contact-list', 'email-setting-list', 'seo-manage', 'sitemap-manage', 'fraud-setting-list', 'api-manage'])
      <li class="has-sub {{ $setOpen ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-settings" data-sidebar-toggle><i class="fe-settings"></i><span>Settings</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ $setOpen ? 'show' : '' }}" id="sidebar-settings">
          <ul class="nav-second-level">
            @can('setting-list')
            <li><a href="{{ route('settings.index') }}">General Setting</a></li>
            @endcan
            @can('social-list')
            <li><a href="{{ route('socialmedias.index') }}">Social Media</a></li>
            @endcan
            @can('contact-list')
            <li><a href="{{ route('contact.index') }}">Contact</a></li>
            @endcan
            @canany(['page-list', 'page-create', 'page-edit'])
            <li><a href="{{ route('pages.index') }}">Pages</a></li>
            @endcanany
            @canany(['shipping-list', 'shipping-create', 'shipping-edit'])
            <li><a href="{{ route('shippingcharges.index') }}">Shipping Charge</a></li>
            @endcanany
            @can('email-setting-list')
            <li><a href="{{ route('email_setting') }}">Email Settings</a></li>
            @endcan
            @can('seo-manage')
            <li><a href="{{ route('admin.seo_settings.index') }}">SEO Settings</a></li>
            @endcan
            @can('sitemap-manage')
            <li><a href="{{ route('admin.sitemap.index') }}">Sitemap</a></li>
            @endcan
            @can('fraud-setting-list')
            <li><a href="{{ route('admin.fraud.index') }}">Fraud API</a></li>
            @endcan
            @canany(['setting-list', 'setting-edit'])
            <li><a href="{{ route('admin.order.restriction.setting.index') }}">Order Restriction</a></li>
            @endcanany
            @canany(['api-manage'])
            <li><a href="{{ route('paymentgeteway.manage') }}">Payment Gateway</a></li>
            <li><a href="{{ route('smsgeteway.manage') }}">SMS Gateway</a></li>
            <li><a href="{{ route('courierapi.manage') }}">Courier API</a></li>
            <li><a href="{{ route('admin.facebook_capi.edit') }}">Facebook CAPI</a></li>
            <li><a href="{{ route('admin.cron.index') }}">Cron Job</a></li>
            @endcanany
          </ul>
        </div>
      </li>
      @endcanany

      <li class="has-sub {{ request()->routeIs('error-log.*') ? 'open menuitem-active' : '' }}">
        <a href="#sidebar-system" data-sidebar-toggle><i class="fe-server"></i><span>System</span><span class="menu-arrow"></span></a>
        <div class="menu-collapse {{ request()->routeIs('error-log.*') ? 'show' : '' }}" id="sidebar-system">
          <ul class="nav-second-level">
            <li><a href="{{ route('admin.clear.cache') }}" onclick="return confirm('Clear all cache?')">Clear Cache</a></li>
            <li><a href="{{ route('error-log.index') }}">Error Log</a></li>
          </ul>
        </div>
      </li>
    </ul>

    <div class="sidebar-footer">
      <a href="{{ route('home') }}" target="_blank" rel="noopener">Visit storefront</a>
    </div>
  </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
