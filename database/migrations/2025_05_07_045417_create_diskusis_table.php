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
        Schema::create('diskusis', function (Blueprint $table) {
            $table->id('ID_DISKUSI');
            $table->unsignedBigInteger('ID_BARANG');
            $table->unsignedBigInteger('ID_PEMBELI');
            $table->string('PERTANYAAN', 1000);
            $table->date('CREATE_AT');
            $table->text('JAWABAN')->nullable();
            $table->unsignedBigInteger('ID_PEGAWAI')->nullable();
            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawais');
            $table->foreign('ID_BARANG')
                ->references('ID_BARANG')
                ->on('barangs')
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
        Schema::dropIfExists('diskusis');
    }
};
