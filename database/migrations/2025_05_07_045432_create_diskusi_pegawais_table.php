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
        Schema::create('diskusi_pegawais', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_PEGAWAI');
            $table->unsignedBigInteger('ID_DISKUSI');
            $table->foreign('ID_PEGAWAI')
                ->references('ID_PEGAWAI')
                ->on('pegawais')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('ID_DISKUSI')
                ->references('ID_DISKUSI')
                ->on('diskusis')
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
        Schema::dropIfExists('diskusi_pegawais');
    }
};
