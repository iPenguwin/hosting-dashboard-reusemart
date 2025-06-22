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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('ID_BARANG');
            $table->unsignedBigInteger('ID_KATEGORI');
            $table->unsignedBigInteger('ID_PENITIP')->nullable();
            $table->unsignedBigInteger('ID_PEGAWAI')->nullable();
            $table->unsignedBigInteger('ID_ORGANISASI')->nullable(); // Added organization relationship
            $table->string('NAMA_BARANG', 255);
            $table->string('KODE_BARANG', 5)->nullable();
            $table->float('HARGA_BARANG');
            $table->date('TGL_MASUK');
            $table->date('TGL_KELUAR')->nullable();
            $table->date('TGL_AMBIL')->nullable();
            $table->date('GARANSI')->nullable();
            $table->decimal('BERAT', 8, 2)->nullable(); // Changed from integer to decimal
            $table->string('DESKRIPSI', 1000);
            $table->float('RATING')->default(0);
            $table->string('STATUS_BARANG', 255);
            $table->string('FOTO_BARANG', 255);

            // Foreign key constraints
            $table->foreign('ID_KATEGORI')
                ->references('ID_KATEGORI')
                ->on('kategoribarangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_PENITIP')
                ->references('ID_PENITIP')
                ->on('penitips')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('ID_PEGAWAI')
                ->references('ID_PEGAWAI')
                ->on('pegawais')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('ID_ORGANISASI') // Added organization foreign key
                ->references('ID_ORGANISASI')
                ->on('organisasis')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
