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
        Schema::create('merchandises', function (Blueprint $table) {
            $table->id('ID_MERCHANDISE');
            $table->string('NAMA_MERCHANDISE', 255);
            $table->integer('POIN_DIBUTUHKAN')->default(0);
            $table->integer('JUMLAH')->default(0);
            $table->string('GAMBAR')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchandises');
    }
};
