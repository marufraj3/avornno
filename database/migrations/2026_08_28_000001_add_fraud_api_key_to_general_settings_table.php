<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hoorin Courier Search API-র জন্য API Key
     * (অ্যাডমিন প্যানেল: Settings → Fraud API)
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'fraud_api_key')) {
                $table->string('fraud_api_key')->nullable()->after('duplicate_order_api_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'fraud_api_key')) {
                $table->dropColumn('fraud_api_key');
            }
        });
    }
};
