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
        Schema::create('detail_transaksi_pembelian_barangs', function (Blueprint $table) {
            $table->id('ID_DETAIL_TRANSAKSI_PEMBELIAN');
            $table->unsignedBigInteger('ID_TRANSAKSI_PEMBELIAN');
            $table->unsignedBigInteger('ID_BARANG');
            $table->foreign('ID_TRANSAKSI_PEMBELIAN')
                ->references('ID_TRANSAKSI_PEMBELIAN')
                ->on('transaksi_pembelian_barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->name('fk_detail_transaksi_pembelian');
            $table->foreign('ID_BARANG')
                ->references('ID_BARANG')
                ->on('barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->name('fk_detail_barang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_pembelian_barangs');
    }
};
