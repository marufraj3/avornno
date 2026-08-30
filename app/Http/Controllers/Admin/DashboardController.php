<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\FundTransaction;
use App\Models\Expense;
use App\Models\Category;
use App\Models\OrderStatus;
use App\Models\IncompleteOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Session;
use Toastr;
use Auth;
use DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // চাইলে এখানে auth middleware চালু করতে পারো
        // $this->middleware('auth')->except(['locked','unlocked']);
    }

    public function minimalDashboard()
    {
        $today = Carbon::today()->toDateString();

        $statusRows = OrderStatus::select('id', 'name', 'slug')->get();
        $statusId = function (array $needles) use ($statusRows) {
            foreach ($statusRows as $row) {
                $hay = strtolower(($row->slug ?? '') . ' ' . ($row->name ?? ''));
                foreach ($needles as $needle) {
                    if (str_contains($hay, $needle)) {
                        return (int) $row->id;
                    }
                }
            }
            return null;
        };

        $pendingId  = $statusId(['pending']) ?? 1;
        $confirmId  = $statusId(['confirm']) ?? 2;
        $cancelId   = $statusId(['cancel']) ?? 4;
        $shippedId  = $statusId(['shipped', 'shipping', 'courier']) ?? 5;
        $incompleteId = null;

        // Today counts
        $todayByStatus = Order::select('order_status', DB::raw('COUNT(*) as c'))
            ->whereDate('created_at', $today)
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $countStatus = function ($bag, $id) {
            if (!$id) return 0;
            return (int) ($bag[$id] ?? $bag[(string) $id] ?? 0);
        };

        $today_order       = (int) $todayByStatus->sum();
        $today_pending     = $countStatus($todayByStatus, $pendingId);
        $today_confirm     = $countStatus($todayByStatus, $confirmId);
        $today_cancel      = $countStatus($todayByStatus, $cancelId);
        $today_shipped     = $countStatus($todayByStatus, $shippedId);
        $today_incomplete  = (int) IncompleteOrder::whereDate('created_at', $today)->count();

        // Lifetime totals
        $totalsByStatus = Order::select('order_status', DB::raw('COUNT(*) as c'))
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $total_order      = (int) $totalsByStatus->sum();
        $total_pending    = $countStatus($totalsByStatus, $pendingId);
        $total_confirm    = $countStatus($totalsByStatus, $confirmId);
        $total_cancel     = $countStatus($totalsByStatus, $cancelId);
        $total_shipped    = $countStatus($totalsByStatus, $shippedId);
        $total_incomplete = (int) IncompleteOrder::count();

        // Sales
        $yesterday = Carbon::yesterday()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();

        $today_sales     = (float) Order::whereDate('created_at', $today)->sum('amount');
        $yesterday_sales = (float) Order::whereDate('created_at', $yesterday)->sum('amount');
        $month_sales     = (float) Order::whereDate('created_at', '>=', $monthStart)->sum('amount');
        $total_revenue   = (float) Order::sum('amount');

        // Products
        $total_products    = (int) Product::count();
        $active_products   = (int) Product::where('status', 'active')->orWhere('status', 1)->count();
        $low_stock         = (int) Product::whereRaw('COALESCE(stock,0) > 0 AND COALESCE(stock,0) <= low_stock_limit')->count();
        $out_of_stock      = (int) Product::whereRaw('COALESCE(stock,0) = 0')->count();

        // Customers
        $total_customers      = (int) Customer::count();
        $new_customers_today  = (int) Customer::whereDate('created_at', $today)->count();

        // Returning customers = total - new today (approximation)
        $returning_customers  = max(0, $total_customers - $new_customers_today);

        // Need Attention alerts
        $pending_orders_count    = $total_pending;
        $incomplete_orders_count = $total_incomplete;
        $low_stock_count         = $low_stock;
        $failed_payments_count   = (int) Order::where('payment_status', 'failed')->orWhere('payment_status', 'unpaid')->count();

        return view('backEnd.minimal-dashboard', compact(
            // Today
            'today_order', 'today_pending', 'today_confirm', 'today_cancel', 'today_shipped', 'today_incomplete',
            // Lifetime
            'total_order', 'total_pending', 'total_confirm', 'total_cancel', 'total_shipped', 'total_incomplete',
            // Sales
            'today_sales', 'yesterday_sales', 'month_sales', 'total_revenue',
            // Products
            'total_products', 'active_products', 'low_stock', 'out_of_stock',
            // Customers
            'total_customers', 'new_customers_today', 'returning_customers',
            // Alerts
            'pending_orders_count', 'incomplete_orders_count', 'low_stock_count', 'failed_payments_count'
        ));
    }

    public function dashboard()
    {
        $today = Carbon::today()->toDateString();
        $cached = Cache::remember('admin_dash_counts_'.$today, 45, function () {
            return $this->dashboardCounts();
        });
        return view('backEnd.admin.dashboard', $cached);
    }

    /**
     * `/admin/dashboard` (route name: dashboard) পুরোনো alias-টি একই
     * ড্যাশবোর্ড রেন্ডার করে — যাতে brand link ও redirect দুই জায়গা থেকেই কাজ করে।
     */
    public function index()
    {
        return $this->dashboard();
    }

    protected function dashboardCounts()
    {
        // Lightweight aggregate-only dashboard data (no heavy lists/charts).
        // সব সংখ্যা cached — প্রতি রিফ্রেশে DB hit হয় না।
        $today    = Carbon::today();
        $todayStr = $today->toDateString();
        $ydayStr  = Carbon::yesterday()->toDateString();
        $monthStr = Carbon::now()->startOfMonth()->toDateString();

        // ---- Order status map (1 query) -----------------------------
        $statusRows = OrderStatus::select('id', 'name', 'slug')->get();
        $statusId = function (array $needles) use ($statusRows) {
            foreach ($statusRows as $row) {
                $hay = strtolower(($row->slug ?? '') . ' ' . ($row->name ?? ''));
                foreach ($needles as $needle) {
                    if (str_contains($hay, $needle)) {
                        return (int) $row->id;
                    }
                }
            }

            return null;
        };

        $pendingId = $statusId(['pending']) ?? 1;
        $confirmId = $statusId(['confirm']) ?? 2;
        $cancelId  = $statusId(['cancel']) ?? 4;
        $shippedId = $statusId(['shipped', 'shipping', 'courier']) ?? 5;

        // ---- Order counts: lifetime + today (2 grouped queries) -----
        $totalsByStatus = Order::select('order_status', DB::raw('COUNT(*) as c'))
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $todayByStatus = Order::select('order_status', DB::raw('COUNT(*) as c'))
            ->whereDate('created_at', $todayStr)
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $countStatus = function ($bag, $id) {
            if (!$id) {
                return 0;
            }

            return (int) ($bag[$id] ?? $bag[(string) $id] ?? 0);
        };

        // Today's overview
        $today_order      = (int) $todayByStatus->sum();
        $today_pending    = $countStatus($todayByStatus, $pendingId);
        $today_confirm    = $countStatus($todayByStatus, $confirmId);
        $today_cancel     = $countStatus($todayByStatus, $cancelId);
        $today_shipped    = $countStatus($todayByStatus, $shippedId);
        $today_incomplete = (int) IncompleteOrder::whereDate('created_at', $todayStr)->count();

        // Lifetime overview
        $total_order      = (int) $totalsByStatus->sum();
        $total_pending    = $countStatus($totalsByStatus, $pendingId);
        $total_confirm    = $countStatus($totalsByStatus, $confirmId);
        $total_cancel     = $countStatus($totalsByStatus, $cancelId);
        $total_shipped    = $countStatus($totalsByStatus, $shippedId);
        $total_incomplete = (int) IncompleteOrder::count();

        // ---- Sales: ৪টি figure একটি aggregate query-তে ----------------
        $sales = Order::selectRaw(
            'COALESCE(SUM(CASE WHEN DATE(created_at) = ? THEN amount END), 0) AS today_sales,
             COALESCE(SUM(CASE WHEN DATE(created_at) = ? THEN amount END), 0) AS yesterday_sales,
             COALESCE(SUM(CASE WHEN DATE(created_at) >= ? THEN amount END), 0) AS month_sales,
             COALESCE(SUM(amount), 0) AS total_revenue',
            [$todayStr, $ydayStr, $monthStr]
        )->first();

        $today_sales     = (float) ($sales->today_sales ?? 0);
        $yesterday_sales = (float) ($sales->yesterday_sales ?? 0);
        $month_sales     = (float) ($sales->month_sales ?? 0);
        $total_revenue   = (float) ($sales->total_revenue ?? 0);

        // ---- Products: ৪টি figure একটি aggregate query-তে -------------
        $thresholdSql = Schema::hasColumn('products', 'low_stock_threshold')
            ? 'COALESCE(low_stock_threshold, 3)'
            : '3';

        $products = Product::selectRaw(
            "COUNT(*) AS total_products,
             COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) AS active_products,
             COALESCE(SUM(CASE WHEN stock > 0 AND stock <= {$thresholdSql} THEN 1 ELSE 0 END), 0) AS low_stock,
             COALESCE(SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END), 0) AS out_of_stock"
        )->first();

        $total_products  = (int) ($products->total_products ?? 0);
        $active_products = (int) ($products->active_products ?? 0);
        $low_stock       = (int) ($products->low_stock ?? 0);
        $out_of_stock    = (int) ($products->out_of_stock ?? 0);

        // ---- Customers: ২টি figure একটি aggregate query-তে ------------
        $customers = Customer::selectRaw(
            'COUNT(*) AS total_customers,
             COALESCE(SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END), 0) AS new_customers_today',
            [$todayStr]
        )->first();

        $total_customers     = (int) ($customers->total_customers ?? 0);
        $new_customers_today = (int) ($customers->new_customers_today ?? 0);
        $returning_customers = max(0, $total_customers - $new_customers_today);

        // ---- Failed payments (কলাম না থাকলেও ড্যাশবোর্ড ভাঙবে না) ------
        try {
            $failed_payments_count = (int) Order::whereIn('payment_status', ['failed', 'unpaid'])->count();
        } catch (\Throwable $e) {
            $failed_payments_count = 0;
        }

        // ---- Existing order-list slugs (links reuse করার জন্য) -------
        $pendingSlug = optional($statusRows->firstWhere('id', $pendingId))->slug ?? 'pending';
        $confirmSlug = optional($statusRows->firstWhere('id', $confirmId))->slug ?? 'confirm';
        $cancelSlug  = optional($statusRows->firstWhere('id', $cancelId))->slug ?? 'cancel';
        $shippedSlug = optional($statusRows->firstWhere('id', $shippedId))->slug ?? 'shipped';

        return compact(
            // Today
            'today_order', 'today_pending', 'today_confirm', 'today_cancel', 'today_shipped', 'today_incomplete',
            // Lifetime
            'total_order', 'total_pending', 'total_confirm', 'total_cancel', 'total_shipped', 'total_incomplete',
            // Sales
            'today_sales', 'yesterday_sales', 'month_sales', 'total_revenue',
            // Products
            'total_products', 'active_products', 'low_stock', 'out_of_stock',
            // Customers
            'total_customers', 'new_customers_today', 'returning_customers',
            // Need attention
            'failed_payments_count',
            // Status slugs
            'pendingSlug', 'confirmSlug', 'cancelSlug', 'shippedSlug'
        );
    }

    public function changepassword()
    {
        return view('backEnd.admin.changepassword');
    }

    public function newpassword(Request $request)
    {
        $this->validate($request, [
            'old_password'     => 'required',
            'new_password'     => 'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $user = User::find(Auth::id());
        $hashPass = $user->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('dashboard'); // অথবা route('admin.dashboard') তোমার রাউট নাম অনুযায়ী
        } else {
            Toastr::error('Failed', 'Old password not match!');
            return back();
        }
    }

    public function locked()
    {
        Session::put('locked', true);
        return view('backEnd.auth.locked');
    }

    public function unlocked(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $password = $request->password;

        if (Hash::check($password, Auth::user()->password)) {
            Session::forget('locked');
            Toastr::success('Success', 'You are logged in successfully!');
            return redirect()->route('dashboard'); // অথবা route('admin.dashboard')
        }

        Toastr::error('Failed', 'Your password not match!');
        return back();
    }
}
