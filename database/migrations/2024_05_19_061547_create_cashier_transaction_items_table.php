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
        Schema::create('cashier_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_transaction_id');
            $table->unsignedBigInteger('barang_id');
            $table->enum('transaction_type', ['satuan', 'grosir', 'reseller']);
            $table->bigInteger('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_transaction_items');
    }
};
