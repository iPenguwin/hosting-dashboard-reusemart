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
        Schema::create('transaksi_penitipan_barangs', function (Blueprint $table) {
            $table->id('ID_TRANSAKSI_PENITIPAN');
            $table->unsignedBigInteger('ID_PENITIP');
            $table->date('TGL_MASUK_TITIPAN');
            $table->date('TGL_KELUAR_TITIPAN');
            $table->string('NO_NOTA_TRANSAKSI_TITIPAN', 255);
            $table->foreign('ID_PENITIP')
                ->references('ID_PENITIP')
                ->on('penitips')
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
        Schema::dropIfExists('transaksi_penitipan_barangs');
    }
};
