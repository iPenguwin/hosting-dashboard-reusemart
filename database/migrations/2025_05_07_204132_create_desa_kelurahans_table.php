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
        Schema::create('desa_kelurahans', function (Blueprint $table) {
            $table->id('id_desa_kelurahan');
            $table->unsignedBigInteger('id_kecamatan');
            $table->string('nama_desa_kelurahan', 255);
            $table->timestamps();

            $table->foreign('id_kecamatan')
                ->references('id_kecamatan')
                ->on('kecamatans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa_kelurahans');
    }
};
