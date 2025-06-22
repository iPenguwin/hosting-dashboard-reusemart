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
        Schema::create('penitips', function (Blueprint $table) {
            $table->id('ID_PENITIP');
            $table->string('NAMA_PENITIP', 255);
            $table->string('PROFILE_PENITIP')->nullable();
            $table->string('NO_KTP', 16);
            $table->string('ALAMAT_PENITIP', 255);
            $table->date('TGL_LAHIR_PENITIP');
            $table->string('NO_TELP_PENITIP', 25);
            $table->string('EMAIL_PENITIP', 25);
            $table->string('PASSWORD_PENITIP');
            $table->string('FOTO_NIK')->nullable();
            $table->float('SALDO_PENITIP')->default(0);
            $table->integer('POINT_LOYALITAS_PENITIP')->default(0);
            $table->integer('RATING_PENITIP')->default(0);
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitips');
    }
};
