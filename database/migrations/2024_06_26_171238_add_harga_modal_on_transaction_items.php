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
        Schema::table('cashier_transaction_items', function (Blueprint $table) {
            $table->bigInteger('harga_modal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_transaction_items', function (Blueprint $table) {
            $table->dropColumn('harga_modal');
        });
    }
};
