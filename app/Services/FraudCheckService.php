<?php

namespace App\Services;

use App\Models\GeneralSetting;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * কুরিয়ার ফ্রড/সাকসেস রেশিও চেক (Hoorin Courier Search API)।
 *
 * ডকুমেন্টেশন: https://dash.hoorin.com/doc
 * এন্ডপয়েন্ট: GET https://plugin.hoorin.com/courier/api/v1/search
 *
 * API Key অ্যাডমিন প্যানেলের Settings → Fraud API পেজ থেকে দেওয়া হয়
 * (GeneralSetting.fraud_api_key), যেটা dash.hoorin.com ড্যাশবোর্ড থেকে পাওয়া যায়।
 *
 * আগে এই লজিকটা শুধু Admin\OrderController@fraudCheck() এর ভেতরে ছিল, তাই
 * ম্যানুয়ালি বাটনে ক্লিক না করলে কখনো চলত না। এখন এটি একটি সার্ভিস — অ্যাডমিন
 * প্যানেলের ম্যানুয়াল চেক আর নতুন অর্ডারের অটো-চেক দুটোই একই কোড ব্যবহার করে।
 */
class FraudCheckService
{
    private const API_URL = 'https://plugin.hoorin.com/courier/api/v1/search';

    /**
     * একটি মোবাইল নম্বরের কুরিয়ার পার্সেল হিস্ট্রি (ফ্রড/সাকসেস রেশিও) আনবে
     * এবং ওই নম্বরের সব অর্ডারে সেভ করবে।
     *
     * @return array{status: string, message?: string, data?: array}
     */
    public function checkPhone(?string $mobile, int $timeout = 20, bool $live = false): array
    {
        $mobile = trim((string) $mobile);

        if ($mobile === '') {
            return ['status' => 'failed', 'message' => 'Mobile number missing'];
        }

        // Hoorin API-তে ১১ ডিজিটের মোবাইল নাম্বার প্রত্যাশিত —
        // ফরম্যাট নরমালাইজ করে নিই (যেমন: +8801642444088 / 8801642444088 → 01642444088)
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        if (strlen($mobile) === 13 && str_starts_with($mobile, '880')) {
            $mobile = '0' . substr($mobile, 3);
        }

        if (strlen($mobile) !== 11) {
            return ['status' => 'failed', 'message' => 'সঠিক ১১ ডিজিটের মোবাইল নাম্বার দিন'];
        }

        $apiKey = $this->apiKey();

        if (!$apiKey) {
            return ['status' => 'failed', 'message' => 'Fraud API Key missing — অ্যাডমিন প্যানেলের Fraud API সেটিংস থেকে API Key সেট করুন'];
        }

        try {
            $response = Http::timeout($timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(self::API_URL, [
                    'apiKey'     => $apiKey,
                    'searchTerm' => $mobile,
                    'view'       => 'full',
                    // ম্যানুয়াল চেকের সময় রেডিস ক্যাশ বাইপাস করে লাইভ ডেটা আনা হয়;
                    // অটো-চেকে ক্যাশড (দ্রুত) রেসপন্স ব্যবহার করা হয়।
                    'cache'      => $live ? 'off' : 'on',
                ]);

            $res = $response->json();

            if (!is_array($res) || empty($res['success'])) {
                return [
                    'status'  => 'failed',
                    'message' => is_array($res) && isset($res['message'])
                        ? $res['message']
                        : 'Fraud check ব্যর্থ হয়েছে (HTTP ' . $response->status() . ')',
                ];
            }

            $this->applyToOrders($mobile, $res);

            return ['status' => 'success', 'data' => $this->normalize($res)];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => 'API Error: ' . $e->getMessage()];
        }
    }

    /**
     * অর্ডার তৈরি হওয়ার পর ব্যাকগ্রাউন্ডে (response পাঠানোর পর) চেক চালায়।
     * কাস্টমারকে কখনোই অপেক্ষা করানো হয় না — ব্যর্থ হলে শুধু লগ হয়।
     */
    public function queueAfterResponse(?string $mobile, ?int $orderId = null): void
    {
        $mobile = trim((string) $mobile);

        if ($mobile === '' || !$this->apiKey()) {
            return;
        }

        register_shutdown_function(function () use ($mobile, $orderId) {
            try {
                $result = $this->checkPhone($mobile, 12);

                if (($result['status'] ?? '') !== 'success') {
                    Log::warning('Auto fraud check skipped for order ' . ($orderId ?? '-') . ': ' . ($result['message'] ?? 'unknown'));
                }
            } catch (\Throwable $e) {
                Log::error('Auto fraud check failed for order ' . ($orderId ?? '-') . ': ' . $e->getMessage());
            }
        });
    }

    private function apiKey(): ?string
    {
        $generalSetting = GeneralSetting::where('status', 1)->first() ?: GeneralSetting::first();
        $apiKey = $generalSetting->fraud_api_key ?? null;

        return $apiKey ? (string) $apiKey : null;
    }

    /**
     * Hoorin API রেসপন্সকে আগের (legacy) ফরম্যাটে রূপান্তর করে, যাতে অ্যাডমিন
     * প্যানেলের ম্যানুয়াল চেক পেজ ও অর্ডার লিস্টের JS কোনো পরিবর্তন ছাড়াই কাজ করে।
     *
     * Hoorin response:
     *   overall: { total_parcels, delivered_parcels, cancelled_parcels, success_ratio }
     *   couriers: { steadfast|redx|pathao|carrybee: { total_parcels, delivered_parcels,
     *               cancelled_parcels, success_ratio, details[] } }
     *
     * Legacy format:
     *   summary: { total_parcel, success_parcel, cancelled_parcel, success_ratio }
     *   couriers: { total_parcel, success_parcel, cancelled_parcel, success_ratio, details[] }
     */
    private function normalize(array $res): array
    {
        $overall = is_array($res['overall'] ?? null) ? $res['overall'] : [];
        $couriers = is_array($res['couriers'] ?? null) ? $res['couriers'] : [];

        $data = [
            'summary' => [
                'total_parcel'     => (int) ($overall['total_parcels'] ?? 0),
                'success_parcel'   => (int) ($overall['delivered_parcels'] ?? 0),
                'cancelled_parcel' => (int) ($overall['cancelled_parcels'] ?? 0),
                'success_ratio'    => round((float) ($overall['success_ratio'] ?? 0), 2),
            ],
        ];

        foreach ($couriers as $key => $info) {
            if (!is_array($info)) {
                continue;
            }

            // কুরিয়ার কানেক্ট করা না থাকলে Hoorin কানেক্ট করার অ্যালার্ট মেসেজ পাঠায় —
            // সেক্ষেত্রে সংখ্যা 0 রেখে মেসেজটাও পাস করি (যাতে ইউআইতে দেখা যায়)।
            $data[$key] = [
                'total_parcel'     => (int) ($info['total_parcels'] ?? 0),
                'success_parcel'   => (int) ($info['delivered_parcels'] ?? 0),
                'cancelled_parcel' => (int) ($info['cancelled_parcels'] ?? 0),
                'success_ratio'    => round((float) ($info['success_ratio'] ?? 0), 2),
                'details'          => $info['details'] ?? [],
            ];
        }

        return $data;
    }

    /**
     * একই নম্বরের সব অর্ডারে কুরিয়ারভিত্তিক সাকসেস/ক্যান্সেল ডাটা বসায়।
     */
    private function applyToOrders(string $mobile, array $res): void
    {
        $orders = Order::whereHas('shipping', fn ($q) => $q->where('phone', $mobile))->get();

        if ($orders->isEmpty()) {
            return;
        }

        $overall = is_array($res['overall'] ?? null) ? $res['overall'] : [];
        $couriers = is_array($res['couriers'] ?? null) ? $res['couriers'] : [];

        foreach ($orders as $order) {
            // অর্ডার টেবিলে যেসব কুরিয়ারের কলাম আছে (pathao, redx, steadfast)
            foreach (['pathao', 'redx', 'steadfast'] as $courier) {
                $cData = is_array($couriers[$courier] ?? null) ? $couriers[$courier] : [];

                $order->{$courier . '_success'} = (int) ($cData['delivered_parcels'] ?? 0);
                $order->{$courier . '_cancel'}  = (int) ($cData['cancelled_parcels'] ?? 0);
                $order->{$courier . '_rate'}    = round((float) ($cData['success_ratio'] ?? 0), 2);
            }

            // সার্বিক (সব কুরিয়ার মিলিয়ে) সাকসেস রেশিও
            $order->fraud_success = (int) ($overall['delivered_parcels'] ?? 0);
            $order->fraud_cancel  = (int) ($overall['cancelled_parcels'] ?? 0);
            $order->fraud_rate    = round((float) ($overall['success_ratio'] ?? 0), 2);

            $order->save();
        }
    }
}
