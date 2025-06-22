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
        Schema::create('detail_transaksi_penitip_barangs', function (Blueprint $table) {
            $table->id('ID_DETAIL_TRANSAKSI_PENITIPAN');
            $table->unsignedBigInteger('ID_TRANSAKSI_PENITIPAN');
            $table->unsignedBigInteger('ID_BARANG');

            $table->foreign('ID_TRANSAKSI_PENITIPAN')
                ->references('ID_TRANSAKSI_PENITIPAN')
                ->on('transaksi_penitipan_barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_BARANG')
                ->references('ID_BARANG')
                ->on('barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();

            $table->string('NAMA_BARANG', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_penitip_barangs');
    }
};
