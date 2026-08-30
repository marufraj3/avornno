<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * অ্যাডমিন Contact settings-এ WhatsApp ইনপুট ফিল্ড আগে থেকেই আছে
 * (backEnd/contact/index.blade.php), কিন্তু contacts টেবিলে কলামটা কখনো
 * যোগ হয়নি। Contact মডেল $guarded = [] এবং কন্ট্রোলার $request->except('hidden_id')
 * সরাসরি update() করে — তাই ওই ফিল্ড সহ ফর্ম সেভ করলে
 * "Unknown column 'whatsapp'" SQL error হতো। এই মাইগ্রেশন সেটা ঠিক করে।
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('contacts', 'whatsapp')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('whatsapp', 50)->nullable()->after('phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contacts', 'whatsapp')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('whatsapp');
            });
        }
    }
};
