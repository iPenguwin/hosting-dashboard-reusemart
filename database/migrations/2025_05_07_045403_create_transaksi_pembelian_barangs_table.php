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
        // Membuat tabel baru (jika belum ada)
        if (!Schema::hasTable('transaksi_pembelian_barangs')) {
            Schema::create('transaksi_pembelian_barangs', function (Blueprint $table) {
                $table->id('ID_TRANSAKSI_PEMBELIAN');
                $table->unsignedBigInteger('ID_PEMBELI');
                $table->unsignedBigInteger('ID_BARANG');
                $table->string('BUKTI_TRANSFER', 255)->nullable();
                $table->date('TGL_AMBIL_KIRIM')->nullable();
                $table->date('TGL_LUNAS_PEMBELIAN')->nullable();
                $table->date('TGL_PESAN_PEMBELIAN');
                $table->float('TOT_HARGA_PEMBELIAN');
                $table->string('STATUS_PEMBAYARAN', 255);
                $table->string('DELIVERY_METHOD', 255);
                $table->float('ONGKOS_KIRIM');
                $table->integer('POIN_DIDAPAT')->default(0);
                $table->integer('POIN_POTONGAN')->nullable();
                $table->unsignedBigInteger('ID_ALAMAT_PENGIRIMAN')->nullable();
                $table->string('STATUS_BUKTI_TRANSFER', 255);
                $table->string('STATUS_TRANSAKSI', 255);

                // Foreign key constraints
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

                $table->foreign('ID_ALAMAT_PENGIRIMAN')
                    ->references('ID_ALAMAT')
                    ->on('alamats')
                    ->onDelete('set null');

                $table->timestamps();
            });

            // If you really need the column order, you can use a separate alter statement
            Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
                $table->unsignedBigInteger('ID_ALAMAT_PENGIRIMAN')
                    ->nullable()
                    ->after('POIN_POTONGAN')
                    ->change();
            });
        }

        // Modifikasi kolom BUKTI_TRANSFER menjadi nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_pembelian_barangs', 'BUKTI_TRANSFER')) {
                $table->string('BUKTI_TRANSFER')->nullable()->change();
            }
        });

        // Modifikasi kolom TGL_AMBIL_KIRIM menjadi nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_pembelian_barangs', 'TGL_AMBIL_KIRIM')) {
                $table->date('TGL_AMBIL_KIRIM')->nullable()->change();
            }
        });

        // Modifikasi kolom TGL_LUNAS_PEMBELIAN menjadi nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_pembelian_barangs', 'TGL_LUNAS_PEMBELIAN')) {
                $table->date('TGL_LUNAS_PEMBELIAN')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan TGL_LUNAS_PEMBELIAN ke tidak nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            $table->date('TGL_LUNAS_PEMBELIAN')->nullable(false)->change();
        });

        // Mengembalikan TGL_AMBIL_KIRIM ke tidak nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            $table->date('TGL_AMBIL_KIRIM')->nullable(false)->change();
        });

        // Mengembalikan BUKTI_TRANSFER ke tidak nullable
        Schema::table('transaksi_pembelian_barangs', function (Blueprint $table) {
            $table->string('BUKTI_TRANSFER')->nullable(false)->change();
        });

        // Opsional: Menghapus tabel (jika diperlukan)
        // Schema::dropIfExists('transaksi_pembelian_barangs');
    }
};
