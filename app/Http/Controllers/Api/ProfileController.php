<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiPembelianBarang;

class ProfileController extends Controller
{
    /**
     * GET /api/user/profile
     * Mengembalikan JSON: { id, name, email, point }
     */
    public function getProfile()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'id'    => $user->ID_PEMBELI,
            'name'  => $user->NAMA_PEMBELI,
            'email' => $user->EMAIL_PEMBELI,
            'point' => $user->POINT_LOYALITAS_PEMBELI,
        ]);
    }

    public function getTransactions(Request $request)
    {
        $user = Auth::user();
        $query = TransaksiPembelianBarang::with('detailTransaksiPembelians.barang')
            ->where('ID_PEMBELI', $user->ID_PEMBELI);
        // filter tanggal…
        $list = $query->orderBy('TGL_PESAN_PEMBELIAN','desc')->get();

        $data = $list->map(function($t) {
            $details = $t->detailTransaksiPembelians;
            return [
                'ID_TRANSAKSI_PEMBELIAN'   => $t->ID_TRANSAKSI_PEMBELIAN,
                'TGL_PESAN_PEMBELIAN'      => $t->TGL_PESAN_PEMBELIAN,
                'TOT_HARGA_PEMBELIAN'      => $t->TOT_HARGA_PEMBELIAN,
                'STATUS_PEMBAYARAN'        => $t->STATUS_PEMBAYARAN,
                // snake_case tetap dikirim, client akan map ke camelCase:
                'detail_transaksi_pembelians' => $details,
            ];
        });

        return response()->json(['data' => $data]);
    }


    /**
     * GET /api/user/transactions/{id}
     * Mengembalikan JSON detail satu transaksi (beserta relasi detailTransaksiPembelians → barang)
     */
    public function getTransactionDetail($id)
    {
        $user = Auth::user();
        $transaction = TransaksiPembelianBarang::with('detailTransaksiPembelians.barang')
            ->where('ID_TRANSAKSI_PEMBELIAN', $id)
            ->where('ID_PEMBELI', $user->ID_PEMBELI)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction);
    }
}
