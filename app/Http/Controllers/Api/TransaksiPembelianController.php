<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiPembelianBarang;
use App\Models\DetailTransaksiPembelianBarang;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransaksiPembelianController extends Controller
{
    /**
     * List pesanan milik pembeli yang login
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !isset($user->ID_PEMBELI)) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated atau akses ditolak'], 401);
        }

        // Mulai query dengan relasi dan filter ID_PEMBELI
        $query = TransaksiPembelianBarang::with(['barang', 'detailTransaksiPembelians'])
            ->where('ID_PEMBELI', $user->ID_PEMBELI);

        // Tambah filter periode jika ada
        if ($request->has('start')) {
            $query->whereDate('TGL_PESAN_PEMBELIAN', '>=', $request->query('start'));
        }
        if ($request->has('end')) {
            $query->whereDate('TGL_PESAN_PEMBELIAN', '<=', $request->query('end'));
        }

        // Ambil hasil, urut terbaru, lalu mapping response sama seperti semula
        $pesanan = $query
            ->orderByDesc('TGL_PESAN_PEMBELIAN')
            ->get()
            ->map(function ($t) {
                return [
                    'id'               => $t->ID_TRANSAKSI_PEMBELIAN,
                    'kode'             => 'INV' . str_pad($t->ID_TRANSAKSI_PEMBELIAN, 6, '0', STR_PAD_LEFT),
                    'tanggal'          => $t->TGL_PESAN_PEMBELIAN,
                    'status_pembayaran'=> $t->STATUS_PEMBAYARAN,
                    'status_transaksi' => $t->STATUS_TRANSAKSI,
                    'total'            => $t->TOT_HARGA_PEMBELIAN + $t->ONGKOS_KIRIM,
                    'poin_didapat'     => $t->POIN_DIDAPAT,
                    'poin_potongan'    => $t->POIN_POTONGAN,
                    'barang'           => [
                        'nama'       => $t->barang->NAMA_BARANG ?? 'Barang Tidak Dikenal',
                        'foto_utama' => $t->barang->foto_barang_url ?? null,
                    ],
                    'delivery_method'  => $t->DELIVERY_METHOD,
                    'tgl_ambil_kirim'  => $t->TGL_AMBIL_KIRIM,
                    'tgl_lunas'        => $t->TGL_LUNAS_PEMBELIAN,
                ];
            });

        return response()->json(['success' => true, 'data' => $pesanan]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !isset($user->ID_PEMBELI)) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated atau akses ditolak'], 401);
        }

        $t = TransaksiPembelianBarang::with(['barang', 'detailTransaksiPembelians'])
            ->where('ID_TRANSAKSI_PEMBELIAN', $id)
            ->where('ID_PEMBELI', $user->ID_PEMBELI)
            ->first();

        if (!$t) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $formatted = [
            'id' => $t->ID_TRANSAKSI_PEMBELIAN,
            'kode' => 'INV' . str_pad($t->ID_TRANSAKSI_PEMBELIAN, 6, '0', STR_PAD_LEFT),
            'tanggal' => $t->TGL_PESAN_PEMBELIAN,           // pakai 'tanggal'
            'status_transaksi' => $t->STATUS_TRANSAKSI,               // pakai 'status'
            'total' => $t->TOT_HARGA_PEMBELIAN,             // pakai 'total'
            'ongkos_kirim' => $t->ONGKOS_KIRIM,
            'total_bayar' => $t->TOT_HARGA_PEMBELIAN + $t->ONGKOS_KIRIM,
            'poin_didapat' => $t->POIN_DIDAPAT,
            'poin_potongan' => $t->POIN_POTONGAN,
            'barang' => [
                'id' => $t->barang->ID_BARANG,
                'nama' => $t->barang->NAMA_BARANG,
                'harga' => $t->TOT_HARGA_PEMBELIAN,
                'gambar' => $t->barang->GAMBAR_BARANG ?? null,
            ],
            'detail_transaksi' => $t->detailTransaksiPembelians,
            'bukti_transfer' => $t->BUKTI_TRANSFER,
            'status_bukti_transfer' => $t->STATUS_BUKTI_TRANSFER,
            'delivery_method' => $t->DELIVERY_METHOD,
            'tanggal_ambil_kirim' => $t->TGL_AMBIL_KIRIM,
            'tanggal_lunas' => $t->TGL_LUNAS_PEMBELIAN,
        ];

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'metode_pengiriman' => 'required|in:Di Kirim,Ambil Sendiri',
            'id_alamat_pengiriman' => 'nullable|exists:alamats,ID_ALAMAT',
            'id_barang' => 'required|exists:barangs,ID_BARANG',
            'poin_ditukar' => 'nullable|integer|min:0',  // misal ditambahkan untuk input poin potongan
        ]);

        if ($data['metode_pengiriman'] === 'Di Kirim' && empty($data['id_alamat_pengiriman'])) {
            return response()->json(['error' => 'Alamat pengiriman wajib diisi untuk metode kurir'], 422);
        }

        $barang = Barang::findOrFail($data['id_barang']);
        $totalHarga = $barang->HARGA_BARANG;

        // Hitung potongan poin yang dipakai (jika ada)
        $poinDitukar = $data['poin_ditukar'] ?? 0;
        $nilaiPoin = 10000; // 1 poin = 10.000
        $potonganHarga = $poinDitukar * $nilaiPoin;

        // Hitung poin didapat
        $bonusMultiplier = $totalHarga > 500000 ? 1.2 : 1.0;
        $poinDidapat = floor(($totalHarga / $nilaiPoin) * $bonusMultiplier);

        $now = Carbon::now();

        if ($now->hour >= 16) {
            $tgl_ambil_kirim = $now->copy()->addDay()->toDateString();
        } else {
            $tgl_ambil_kirim = $now->toDateString();
        }

        $transaksi = TransaksiPembelianBarang::create([
            'ID_PEMBELI' => $user->ID_PEMBELI,
            'ID_BARANG' => $data['id_barang'],
            'DELIVERY_METHOD' => $data['metode_pengiriman'],
            'ID_ALAMAT_PENGIRIMAN' => $data['id_alamat_pengiriman'] ?? null,
            'TGL_PESAN_PEMBELIAN' => $now->toDateString(),
            'TGL_AMBIL_KIRIM' => null,
            'TOT_HARGA_PEMBELIAN' => $totalHarga,
            'STATUS_PEMBAYARAN' => 'Belum dibayar',
            'STATUS_BUKTI_TRANSFER' => 'N/A',
            'STATUS_TRANSAKSI' => 'Menunggu Pembayaran',
            'ONGKOS_KIRIM' => 0,
            'POIN_DIDAPAT' => $poinDidapat,
            'POIN_POTONGAN' => $poinDitukar,
        ]);

        return response()->json(['success' => true, 'data' => $transaksi], 201);
    }

    public function uploadBuktiPembayaran(Request $request, $orderId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $transaksi = TransaksiPembelianBarang::where('ID_TRANSAKSI_PEMBELIAN', $orderId)
            ->where('ID_PEMBELI', $user->ID_PEMBELI)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bukti_transfer' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $file = $request->file('bukti_transfer');
        $path = $file->store('bukti_transfer', 'public');

        $transaksi->BUKTI_TRANSFER = $path;
        $transaksi->TGL_LUNAS_PEMBELIAN = now()->toDateString();
        $transaksi->STATUS_TRANSAKSI = 'Diproses';
        $transaksi->STATUS_PEMBAYARAN = 'Sudah dibayar';
        $transaksi->STATUS_BUKTI_TRANSFER = 'Menunggu Verifikasi';  // <<< Tambahkan ini
        $transaksi->save();

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil diupload']);
    }

    public function batalTransaksi(Request $request, $orderId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $transaksi = TransaksiPembelianBarang::where('ID_TRANSAKSI_PEMBELIAN', $orderId)
            ->where('ID_PEMBELI', $user->ID_PEMBELI)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }


        $transaksi->STATUS_TRANSAKSI = 'Hangus';
        $transaksi->STATUS_PEMBAYARAN = 'Belum dibayar';
        $transaksi->save();

        return response()->json(['success' => true, 'message' => 'Transaksi dibatalkan']);
    }
}
