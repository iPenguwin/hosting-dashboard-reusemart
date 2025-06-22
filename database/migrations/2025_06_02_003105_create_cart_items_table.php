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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('ID_CART'); // primary key
            $table->unsignedBigInteger('ID_PEMBELI'); // foreign key ke pembelis
            $table->unsignedBigInteger('ID_BARANG');  // foreign key ke barangs
            $table->integer('QUANTITY')->default(1);  // jumlah barang, default 1
            $table->timestamps();

            $table->unique(['ID_PEMBELI', 'ID_BARANG']); // kombinasi unik

            $table->foreign('ID_PEMBELI')
                ->references('ID_PEMBELI')
                ->on('pembelis')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('ID_BARANG')
                ->references('ID_BARANG')
                ->on('barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
