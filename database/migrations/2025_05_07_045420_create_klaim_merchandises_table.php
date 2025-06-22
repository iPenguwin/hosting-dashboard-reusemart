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
        Schema::create('klaim_merchandises', function (Blueprint $table) {
            $table->id('ID_KLAIM');
            $table->unsignedBigInteger('ID_MERCHANDISE');
            $table->unsignedBigInteger('ID_PEMBELI');
            $table->date('TGL_KLAIM')->nullable();
            $table->date('TGL_PENGAMBILAN')->nullable();
            $table->foreign('ID_MERCHANDISE')
                ->references('ID_MERCHANDISE')
                ->on('merchandises')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_PEMBELI')
                ->references('ID_PEMBELI')
                ->on('pembelis')
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
        Schema::dropIfExists('klaim_merchandises');
    }
};
