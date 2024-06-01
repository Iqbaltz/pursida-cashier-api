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
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->string('cashier_name');
            $table->string('customer_name');
            $table->string('payment_method_name');
            $table->bigInteger('payment_amount');
            $table->boolean('payment_status');
        });
        Schema::table('cashier_transaction_items', function (Blueprint $table) {
            $table->string('barang_name');
            $table->bigInteger('price_per_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->dropColumn('cashier_name');
            $table->dropColumn('customer_name');
            $table->dropColumn('payment_method_name');
            $table->dropColumn('payment_amount');
            $table->boolean('payment_status');
        });
        Schema::table('cashier_transaction_items', function (Blueprint $table) {
            $table->dropColumn('barang_name');
            $table->dropColumn('price_per_barang');
        });
    }
};
