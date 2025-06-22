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
        Schema::create('badges', function (Blueprint $table) {
            $table->id('ID_BADGE');
            $table->unsignedBigInteger('ID_PENITIP');
            $table->string('NAMA_BADGE', 255);
            $table->date('START_DATE');
            $table->date('END_DATE');
            $table->foreign('ID_PENITIP')
                ->references('ID_PENITIP')
                ->on('penitips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
