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
        Schema::create('pegawai_transaksi_pembelians', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_PEGAWAI');
            $table->unsignedBigInteger('ID_TRANSAKSI_PEMBELIAN');
            $table->foreign('ID_PEGAWAI')
                ->references('ID_PEGAWAI')
                ->on('pegawais')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_TRANSAKSI_PEMBELIAN')
                ->references('ID_TRANSAKSI_PEMBELIAN')
                ->on('transaksi_pembelian_barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_transaksi_pembelians');
    }
};
