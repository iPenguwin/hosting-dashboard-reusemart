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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id('ID_PEGAWAI');
            $table->unsignedBigInteger('ID_JABATAN');
            $table->string('NAMA_PEGAWAI', 255);
            $table->string('PROFILE_PEGAWAI')->nullable();
            $table->string('NO_TELP_PEGAWAI', 25);
            $table->string('EMAIL_PEGAWAI', 255)->unique();
            $table->string('PASSWORD_PEGAWAI', 255);
            $table->float('KOMISI_PEGAWAI')->default(0);
            $table->date('TGL_LAHIR_PEGAWAI');
            $table->foreign('ID_JABATAN')
                ->references('ID_JABATAN')
                ->on('jabatans')
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
        Schema::dropIfExists('pegawais');
    }
};
