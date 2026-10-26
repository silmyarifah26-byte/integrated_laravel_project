<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'image_url')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('material');
            });
        }

        if (Schema::hasTable('sales') && ! Schema::hasColumn('sales', 'payment_method')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('QRIS')->after('status');
                $table->string('payment_status', 30)->default('paid')->after('payment_method');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'image_url')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }

        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'payment_method')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn(['payment_method', 'payment_status']);
            });
        }
    }
};
