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
        Schema::create('transaksi_donasis', function (Blueprint $table) {
            $table->id('ID_TRANSAKSI_DONASI');
            $table->unsignedBigInteger('ID_ORGANISASI');
            $table->unsignedBigInteger('ID_REQUEST');
            $table->date('TGL_DONASI')->nullable();
            $table->string('PENERIMA', 255)->nullable();
            $table->foreign('ID_ORGANISASI')
                ->references('ID_ORGANISASI')
                ->on('organisasis')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_REQUEST')
                ->references('ID_REQUEST')
                ->on('requests')
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
        Schema::dropIfExists('transaksi_donasis');
    }
};
