<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{GeneralSetting, Category, Brand, SocialMedia, Contact, CreatePage, OrderStatus, EcomPixel, GoogleTagManager, Order, PaymentGateway, User, Review};
use Illuminate\Support\Facades\{Config, Session, Gate, Cache, Auth};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * * Laravel 12: Kernel parameter removed as middleware is now configured in bootstrap/app.php
     */
    public function boot(): void
    {
    
        // পেমেন্ট ক্যালব্যাক ৪১৯ এড়াতে CSRF থেকে স্ট্যাটিক এক্সক্লুড
        \App\Http\Middleware\VerifyCsrfToken::except([
            'aamarpay/success', 'aamarpay/fail', 'aamarpay/cancel', 'aamarpay/checkout',
            'uddoktapay/verify', 'uddoktapay/ipn', 'uddoktapay/cancel',
            'payment-success', 'payment-cancel',
            'bkash/checkout-url/callback',
        ]);

        /**
         * 🟢 Super Admin Override - Use admin guard for Blade @can/@canany
         * Optimized: Check admin guard user permissions properly (avoid infinite loop)
         * Direct database check to bypass guard name mismatch issues
         */
        Gate::before(function ($user, $ability) {
            // Skip if not admin guard (for Blade directives only)
            if (!Auth::guard('admin')->check()) {
                return null;
            }
            
            $adminUser = Auth::guard('admin')->user();
            
            // Super Admin (id=1) or Admin role has all permissions - fast check
            if ($adminUser->id == 1) {
                return true;
            }
            
            // Check Admin role (cached by Spatie) - case-insensitive
            $spatieRoles = $adminUser->getRoleNames()->map(fn($role) => strtolower($role))->toArray();
            if (in_array('admin', $spatieRoles)) {
                return true;
            }
            
            // ✅ Direct database check - bypass guard name mismatch
            // Check if user has permission directly or via roles (ignore guard_name)
            try {
                $permSet = Cache::remember('admin_perm_set_'.$adminUser->id, 300, function () use ($adminUser) {
                    $roleIds = \DB::table('model_has_roles')
                        ->where('model_type', get_class($adminUser))
                        ->where('model_id', $adminUser->id)
                        ->pluck('role_id')
                        ->all();
                    $names = [];
                    if ($roleIds) {
                        $names = \DB::table('role_has_permissions')
                            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                            ->whereIn('role_has_permissions.role_id', $roleIds)
                            ->pluck('permissions.name')
                            ->all();
                    }
                    $direct = \DB::table('model_has_permissions')
                        ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                        ->where('model_has_permissions.model_type', get_class($adminUser))
                        ->where('model_has_permissions.model_id', $adminUser->id)
                        ->pluck('permissions.name')
                        ->all();
                    return array_fill_keys(array_unique(array_merge($names, $direct)), true);
                });
                if (isset($permSet[$ability])) {
                    return true;
                }
                return null;
            } catch (\Exception $e) {
                return null;
            }
        });

        /**
         * 🧩 Shurjopay Dynamic Config (Cached 30 min - performance fix)
         */
        try {
            $shurjopay = Cache::remember('shurjopay_gateway_config', 1800, function () {
                return PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
            });
            if ($shurjopay) {
                Config::set([
                    'shurjopay.apiCredentials.username'   => $shurjopay->username,
                    'shurjopay.apiCredentials.password'   => $shurjopay->password,
                    'shurjopay.apiCredentials.prefix'     => $shurjopay->prefix,
                    'shurjopay.apiCredentials.return_url' => $shurjopay->success_url,
                    'shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url,
                    'shurjopay.apiCredentials.base_url'   => $shurjopay->base_url,
                ]);
            }
        } catch (\Exception $e) {}

        /**
         * 🧠 Global View Share (Optimized with Cache)
         */
        try {
            $isAdminRequest = request()->is('admin') || request()->is('admin/*');

            $generalsetting = Cache::remember('general_setting', 1800, function () {
                return GeneralSetting::where('status', 1)->first();
            });
            view()->share('generalsetting', $generalsetting);
            view()->share('demoMode', filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN));

            if ($isAdminRequest) {
                view()->share('pending_reviews', Cache::remember('pending_reviews_count', 300, function () {
                    return Review::where('status', 'pending')->count();
                }));
                view()->share('neworder', Cache::remember('new_order_count', 120, function () {
                    return Order::where('order_status', 1)->count();
                }));
                view()->share('pendingorder', Cache::remember('pending_orders_list', 120, function () {
                    return Order::where('order_status', 1)->select('id', 'invoice_id', 'customer_id', 'created_at')->latest()->limit(6)->get();
                }));
                view()->share('orderstatus', Cache::remember('order_status_list', 1800, function () {
                    return OrderStatus::select('id', 'name', 'slug')->get();
                }));
                view()->share('sidecategories', collect());
                view()->share('menucategories', collect());
                view()->share('contact', Cache::remember('contact_info', 1800, function () {
                    return Contact::where('status', 1)->first();
                }));
                view()->share('socialicons', collect());
                view()->share('pages', collect());
                view()->share('pagesright', collect());
                view()->share('cmnmenu', collect());
                view()->share('brands', collect());
                view()->share('pixels', collect());
                view()->share('gtm_code', collect());
            } else {
                view()->share('pending_reviews', 0);
                view()->share('neworder', 0);
                view()->share('pendingorder', collect());
                view()->share('orderstatus', collect());

                $sidecategories = Cache::remember('side_categories', 1800, function () {
                    return Category::where('parent_id', 0)->where('status', 1)->select('id', 'name', 'slug', 'status', 'image')->get();
                });
                view()->share('sidecategories', $sidecategories);

                $menucategories = Cache::remember('menu_categories_nav', 1800, function () {
                    return Category::where('status', 1)
                        ->where('parent_id', 0)
                        ->select('id', 'name', 'slug', 'status', 'image', 'icon')
                        ->with(['subcategories.childcategories'])
                        ->orderBy('id', 'ASC')
                        ->get();
                });
                view()->share('menucategories', $menucategories);

                $contact = Cache::remember('contact_info', 1800, function () {
                    return Contact::where('status', 1)->first();
                });
                view()->share('contact', $contact);

                $socialicons = Cache::remember('social_icons', 1800, function () {
                    return SocialMedia::where('status', 1)->get();
                });
                view()->share('socialicons', $socialicons);

                $pages = Cache::remember('pages_top', 1800, function () {
                    return CreatePage::where('status', 1)->limit(3)->get();
                });
                view()->share('pages', $pages);

                $pagesright = Cache::remember('pages_right', 1800, function () {
                    return CreatePage::where('status', 1)->skip(1)->limit(5)->get();
                });
                view()->share('pagesright', $pagesright);

                $cmnmenu = Cache::remember('common_menu', 1800, function () {
                    return CreatePage::where('status', 1)->get();
                });
                view()->share('cmnmenu', $cmnmenu);

                $brands = Cache::remember('brands_list', 1800, function () {
                    return Brand::where('status', 1)->select('id', 'name', 'slug', 'image')->limit(24)->get();
                });
                view()->share('brands', $brands);

                $pixels = Cache::remember('pixels_list', 1800, function () {
                    return EcomPixel::where('status', 1)->get();
                });
                view()->share('pixels', $pixels);

                $gtm_code = Cache::remember('gtm_code_list', 1800, function () {
                    return GoogleTagManager::where('status', 1)->get();
                });
                view()->share('gtm_code', $gtm_code);
            }
        } catch (\Exception $e) {}
    }
}
