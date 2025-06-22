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
        // Create the alamats table
        Schema::create('alamats', function (Blueprint $table) {
            $table->id('ID_ALAMAT');
            $table->unsignedBigInteger('ID_PEMBELI');
            $table->string('JUDUL', 255);
            $table->string('NAMA_JALAN', 255);
            $table->string('DESA_KELURAHAN', 255);
            $table->string('KECAMATAN', 255);
            $table->string('KABUPATEN', 255);
            $table->string('PROVINSI', 255);
            $table->boolean('is_default')->default(false);
            $table->foreign('ID_PEMBELI')
                ->references('ID_PEMBELI')
                ->on('pembelis')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamats');
    }
};
