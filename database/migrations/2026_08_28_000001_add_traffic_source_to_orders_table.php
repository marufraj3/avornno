<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'traffic_source')) {
                $table->string('traffic_source', 60)->nullable()->index();
            }
            if (!Schema::hasColumn('orders', 'traffic_referrer')) {
                $table->string('traffic_referrer', 500)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'traffic_source')) {
                $table->dropIndex(['traffic_source']);
                $table->dropColumn('traffic_source');
            }
            if (Schema::hasColumn('orders', 'traffic_referrer')) {
                $table->dropColumn('traffic_referrer');
            }
        });
    }
};
