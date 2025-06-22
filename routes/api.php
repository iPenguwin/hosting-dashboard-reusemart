<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PembeliController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\TransaksiDonasiController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\DiskusiController;
use App\Http\Controllers\Api\TransaksiPembelianController;
use App\Http\Controllers\Api\TransaksiPenitipanController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\MerchandiseController;
use App\Http\Controllers\LaporanPenjualanBulananController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\KlaimMerchandiseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\MagicLinkController;

Route::get('/magic-link-login', [MagicLinkController::class, 'create'])->name('magic-link-login');
Route::post('/magic-link-login', [MagicLinkController::class, 'store'])->name('magic-link-login.store');
Route::get('/magic-link-login/{user}', [MagicLinkController::class, 'loginViaToken'])
    ->name('login.token')
    ->middleware('signed');

// New routes for Pegawai magic link
Route::get('/pegawai/magic-link-login', [MagicLinkController::class, 'createPegawai'])->name('pegawai.magic-link-login');
Route::post('/pegawai/magic-link-login', [MagicLinkController::class, 'storePegawai'])->name('pegawai.magic-link-login.store');
Route::get('/pegawai/magic-link-login/{user}', [MagicLinkController::class, 'loginPegawaiViaToken'])
    ->name('pegawai.login.token')
    ->middleware('signed');

// Routes public
Route::post('/login', [AuthController::class, 'login']);

Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);

Route::post('/pembeli/register', [PembeliController::class, 'register']);
Route::post('/pembeli/login', [PembeliController::class, 'login']);

Route::post('/penitip/register', [PenitipController::class, 'register']);
Route::post('/penitip/login', [PenitipController::class, 'login']);
Route::get('/penitip/login-with-token/{token}', [PenitipController::class, 'loginWithToken']);

Route::post('/organisasi/register', [OrganisasiController::class, 'register']);
Route::post('/organisasi/login', [OrganisasiController::class, 'login']);

// Public resource fetches
Route::get('/provinsi', [AlamatController::class, 'getProvinsi']);
Route::get('/kabupaten/{provinsiId}', [AlamatController::class, 'getKabupaten']);
Route::get('/kecamatan/{kabupatenId}', [AlamatController::class, 'getKecamatan']);
Route::get('/desa/{kecamatanId}', [AlamatController::class, 'getDesa']);

Route::get('/produk', [BarangController::class, 'index']);
Route::get('/produk/{id}', [BarangController::class, 'show']);
Route::get('/barang/request', [BarangController::class, 'forRequest']);

Route::get('/produk/{id}/diskusi', [DiskusiController::class, 'index']);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::apiResource('organisasi/requests', RequestController::class)->parameters([
            'requests' => 'requestModel'
        ]);
    }
);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Checkout & transaksi pembelian
    Route::post('/checkout', [TransaksiPembelianController::class, 'store']);
    Route::post('/checkout/{orderId}/upload-bukti', [TransaksiPembelianController::class, 'uploadBuktiPembayaran']);
    Route::post('/checkout/{orderId}/batal', [TransaksiPembelianController::class, 'batalTransaksi']);

    // Diskusi store
    Route::post('/produk/{id}/diskusi', [DiskusiController::class, 'store']);

    // Titipan umum
    Route::get('/titipan', [TransaksiPenitipanController::class, 'index']);
    Route::get('/titipan/{id}', [TransaksiPenitipanController::class, 'show']);

    // Logout each guard
    Route::post('/pegawai/logout', [PegawaiController::class, 'logout']);
    Route::post('/pembeli/logout', [PembeliController::class, 'logout']);
    Route::post('/penitip/logout', [PenitipController::class, 'logout']);

    Route::get('/klaim-merchandise', [KlaimMerchandiseController::class, 'index']);
    Route::post('/klaim-merchandise', [KlaimMerchandiseController::class, 'store']);

    Route::get('/pembeli/me', [PembeliController::class, 'showData']);

    Route::put('/pembeli/me/update', [PembeliController::class, 'update']);

    Route::prefix('pembeli/me')->group(function () {
        Route::get('/alamat', [AlamatController::class, 'index']);
        Route::post('/alamat', [AlamatController::class, 'store']);
        Route::put('/alamat/{alamat}', [AlamatController::class, 'update']);
        Route::delete('/alamat/{alamat}', [AlamatController::class, 'destroy']);
    });

    Route::get('/pegawai/me', [PegawaiController::class, 'me']);
    Route::put('/pegawai/me/update', [PegawaiController::class, 'updateMe']);

    Route::apiResource('pegawai', PegawaiController::class);
    Route::apiResource('pembeli', PembeliController::class);

    Route::put('/organisasi/me/update', [OrganisasiController::class, 'updateMe']);
    Route::get('/organisasi/me', [OrganisasiController::class, 'me']);
    Route::post('/organisasi/logout', [OrganisasiController::class, 'logout']);
    Route::get('/organisasi/{organisasi}', [OrganisasiController::class, 'show'])->name('organisasi.show');
    Route::apiResource('organisasi', OrganisasiController::class)->except(['show']);

    Route::apiResource('requests', RequestController::class);

    Route::get('/merchandises', [MerchandiseController::class, 'index']);
    Route::post('/merchandises', [MerchandiseController::class, 'store']);
    Route::get('/merchandises/{id}', [MerchandiseController::class, 'show']);
    Route::put('/merchandises/{id}', [MerchandiseController::class, 'update']);
    Route::delete('/merchandises/{id}', [MerchandiseController::class, 'destroy']);

    Route::apiResource('transaksi-donasi', TransaksiDonasiController::class);

    Route::post('/pembeli/{pembeli}/update-points', [PembeliController::class, 'updateLoyaltyPoints']);

    Route::post('/produk/{id}/diskusis', [DiskusiController::class, 'store']);

    Route::get('/pesanan', [TransaksiPembelianController::class, 'index']);
    Route::get('/pesanan/{id}', [TransaksiPembelianController::class, 'show']);
    Route::post('/barang/{id}/rating', [BarangController::class, 'rate'])->name('barang.rate');

    Route::get('/cart-items', [CartItemController::class, 'index']);
    Route::post('/cart-items', [CartItemController::class, 'add']);
    Route::delete('/cart-items/remove/{id_barang}', [CartItemController::class, 'remove']);

    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
    Route::get('/user/transactions', [ProfileController::class, 'getTransactions']);
    Route::get('/user/transactions/{id}', [ProfileController::class, 'getTransactionDetail']);

    Route::prefix('penitip')->group(function () {
        Route::get('/me', [PenitipController::class, 'me']);
        Route::get('/me/transactions', [TransaksiPenitipanController::class, 'transactions']);
        Route::get('/me/barangs', [BarangController::class, 'myBarangs']);
    });

    // Resource Penitip standar
    Route::apiResource('penitip', PenitipController::class);

    Route::put('/penitip/me/update', [PenitipController::class, 'update']);

    // Rate penitip
    Route::post('/penitip/{id}/rating', [PenitipController::class, 'ratePenitip']);
});

Route::middleware('auth:sanctum')
    ->post('/penitip/{id}/rating', [PenitipController::class, 'ratePenitip']);

Route::get('badges/top-sellers', [BadgeController::class, 'current']);
// routes/api.php
Route::middleware('auth:sanctum')->get('/pegawai/{id}/profile', [PegawaiController::class, 'showKurir']);
Route::middleware('auth:sanctum')->get('/pegawai/{id}/tugas', [PegawaiController::class, 'getKurirTugas']);
Route::middleware('auth:sanctum')->get('/pegawai/{id}/tugas-history', [PegawaiController::class, 'getKurirTugasHistory']);
Route::middleware('auth:sanctum')->patch('/pegawai/tugas/{id}/selesai', [PegawaiController::class, 'updateStatusPengiriman']);
Route::middleware('auth:sanctum')->get('/pegawai/{id}/komisi', [PegawaiController::class, 'getHunterKomisi']);

// The route below was a duplicate and pointed to a non-existent method 'getProfile' in PegawaiController.
// Route::middleware('auth:sanctum')->get('/pegawai/{id}/profile', [PegawaiController::class, 'getProfile']);
