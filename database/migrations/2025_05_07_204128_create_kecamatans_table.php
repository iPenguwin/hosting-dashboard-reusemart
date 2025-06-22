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
        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id('id_kecamatan');
            $table->unsignedBigInteger('id_kabupaten_kota');
            $table->string('nama_kecamatan', 255);
            $table->timestamps();

            $table->foreign('id_kabupaten_kota')
                ->references('id_kabupaten_kota')
                ->on('kabupatens')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kecamatans');
    }
};
