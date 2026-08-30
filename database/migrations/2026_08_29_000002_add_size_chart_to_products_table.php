<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ল্যান্ডিং পেজের "Size Chart" সেকশনের জন্য (rongoutfit স্টাইল)।
 * JSON হিসেবে সংরক্ষিত হয়, যেমন:
 *   [{"size":"M","chest":"38","length":"27"},{"size":"L","chest":"40","length":"28"}]
 * খালি/NULL থাকলে ল্যান্ডিং পেজে সেকশনটাই দেখাবে না।
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'size_chart')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('size_chart')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'size_chart')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('size_chart');
            });
        }
    }
};
