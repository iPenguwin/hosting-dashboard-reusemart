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
        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id('id_kabupaten_kota');
            $table->unsignedBigInteger('id_provinsi');
            $table->string('nama_kabupaten_kota', 255);
            $table->timestamps();

            $table->foreign('id_provinsi')
                ->references('id_provinsi')
                ->on('provinsis')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupatens');
    }
};
