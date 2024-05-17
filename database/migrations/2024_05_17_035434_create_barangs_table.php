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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->boolean('hitung_stok')->default(0);
            $table->bigInteger('harga_modal');
            $table->bigInteger('harga_jual_satuan');
            $table->bigInteger('harga_jual_grosir');
            $table->bigInteger('harga_jual_reseller');
            $table->bigInteger('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
