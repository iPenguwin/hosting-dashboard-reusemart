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
        Schema::create('kategoribarangs', function (Blueprint $table) {
            $table->id('ID_KATEGORI');
            $table->string('NAMA_KATEGORI', 255);
            $table->integer('JML_BARANG')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoribarangs');
    }
};
