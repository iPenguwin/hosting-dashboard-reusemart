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
        Schema::create('pembelis', function (Blueprint $table) {
            $table->id('ID_PEMBELI');
            $table->string('NAMA_PEMBELI', 255);
            $table->string('PROFILE_PEMBELI')->nullable();
            $table->date('TGL_LAHIR_PEMBELI');
            $table->string('NO_TELP_PEMBELI', 25);
            $table->string('EMAIL_PEMBELI', 255);
            $table->string('PASSWORD_PEMBELI', 255);
            $table->integer('POINT_LOYALITAS_PEMBELI')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelis');
    }
};
