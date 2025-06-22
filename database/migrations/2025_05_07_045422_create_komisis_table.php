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
        Schema::create('komisis', function (Blueprint $table) {
            $table->id('ID_KOMISI');
            $table->enum('JENIS_KOMISI', ['Hunter', 'Penitip', 'Reusemart']);
            $table->unsignedBigInteger('ID_PENITIP')->nullable();
            $table->unsignedBigInteger('ID_PEGAWAI')->nullable();
            $table->unsignedBigInteger('ID_TRANSAKSI_PEMBELIAN');
            $table->foreign('ID_PENITIP')
                ->references('ID_PENITIP')
                ->on('penitips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
            $table->float('NOMINAL_KOMISI');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komisis');
    }
};
