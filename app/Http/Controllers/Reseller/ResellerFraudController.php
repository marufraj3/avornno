<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Services\FraudCheckService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerFraudController extends Controller
{
    public function manualFraudCheckPage()
    {
        $user = Auth::guard('admin')->user();

        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.fraud.manual_check', compact('user'));
    }

    public function manualFraudCheck(Request $request, FraudCheckService $fraudCheckService)
    {
        $user = Auth::guard('admin')->user();

        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            return redirect()->route('reseller.dashboard');
        }

        $mobile = trim((string) $request->input('mobile'));

        if ($mobile === '') {
            return back()->with('error', 'দয়া করে একটি মোবাইল নাম্বার লিখুন');
        }

        $result = $fraudCheckService->checkPhone($mobile, 20, true);

        if (($result['status'] ?? '') !== 'success') {
            return back()->with('error', $result['message'] ?? 'Fraud check ব্যর্থ হয়েছে');
        }

        $data = $result['data'] ?? [];

        return view('reseller.fraud.manual_check', compact('mobile', 'data', 'user'));
    }
}
