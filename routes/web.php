<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TransaksiPenitipanApiController;
use App\Http\Controllers\Api\BarangApiController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use App\Http\Controllers\LaporanPenjualanBulananController;
use App\Filament\Pages\LaporanKomisiBulanan;
use App\Http\Controllers\LaporanKomisiBulananController;
use App\Filament\Pages\LaporanStokGudang;
use App\Http\Controllers\LaporanStokGudangController;
use App\Http\Controllers\LaporanDonasiBarangController;
use App\Http\Controllers\LaporanTransaksiPenitipController;
use App\Http\Controllers\LaporanRequestDonasiController;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/api/transaksi-penitipan/{transaksi}/cetak-nota', [TransaksiPenitipanApiController::class, 'cetakNota'])->name('transaksi-penitipan.cetak-nota');
Route::get('/api/barang/{barang}/cetak-nota', [BarangApiController::class, 'cetakNota'])->name('barang.cetak-nota');
Route::get('/filament/laporan/penjualan-bulanan/pdf', [LaporanPenjualanBulananController::class, 'unduhPdf'])
    ->name('filament.laporan.penjualan-bulanan.pdf')
    ->middleware([FilamentAuthenticate::class]);

Route::get(
    '/filament/pages/laporan-komisi-bulanan',
    LaporanKomisiBulanan::class
)
    ->name('filament.pages.laporan-komisi-bulanan')
    ->middleware([FilamentAuthenticate::class]);

Route::get(
    '/filament/laporan/komisi-bulanan-per-produk/pdf',
    [LaporanKomisiBulananController::class, 'unduhPdf']
)->name('filament.laporan.komisi-bulanan-per-produk.pdf');

Route::get(
    '/filament/pages/laporan-stok-gudang',
    LaporanStokGudang::class
)
    ->name('filament.pages.laporan-stok-gudang')
    ->middleware([FilamentAuthenticate::class]);

// 2) ROUTE DOWNLOAD PDF:
Route::get(
    '/filament/laporan/stok-gudang/pdf',
    [LaporanStokGudangController::class, 'unduhPdf']
)
    ->name('filament.laporan.stok-gudang.pdf')
    ->middleware([FilamentAuthenticate::class]);

Route::get('/laporan-donasi-barang/pdf', [LaporanDonasiBarangController::class, 'export'])
    ->name('laporan-donasi-barang.pdf')
    ->middleware([FilamentAuthenticate::class]);

Route::get('/laporan-transaksi-penitip/pdf', [LaporanTransaksiPenitipController::class, 'unduhPdf'])
    ->name('laporan-transaksi-penitip.pdf')
    ->middleware([FilamentAuthenticate::class]);

Route::get('/laporan-request-donasi/pdf', [LaporanRequestDonasiController::class, 'export'])
    ->name('laporan-request-donasi.pdf');


use App\Http\Controllers\MagicLinkController;

Route::get('/magic-link-login', [MagicLinkController::class, 'create'])->name('magic-link-login');
Route::post('/magic-link-login', [MagicLinkController::class, 'store'])->name('magic-link-login.store');
Route::get('/magic-link-login/{user}', [MagicLinkController::class, 'loginViaToken'])
    ->name('login.token')
    ->middleware('signed');

// Routes for Pegawai magic link
Route::get('/pegawai/magic-link-login', [MagicLinkController::class, 'createPegawai'])->name('pegawai.magic-link-login');
Route::post('/pegawai/magic-link-login', [MagicLinkController::class, 'storePegawai'])->name('pegawai.magic-link-login.store');
Route::get('/pegawai/magic-link-login/{user}', [MagicLinkController::class, 'loginPegawaiViaToken'])
    ->name('pegawai.login.token')
    ->middleware('signed');
    