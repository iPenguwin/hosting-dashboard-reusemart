<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    // Tampilkan daftar cart item user yang login
    public function index(Request $request)
    {
        $user = Auth::user();

        // Eager load barang dan penitip
        $cartItems = CartItem::with('barang.penitip')
            ->where('ID_PEMBELI', $user->ID_PEMBELI)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems,
        ]);
    }

    // Tambah barang ke cart atau update quantity jika sudah ada
    public function add(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ID_BARANG' => 'required|exists:barangs,ID_BARANG',
            'quantity' => 'integer|min:1',
        ]);

        $quantity = $request->quantity ?? 1;

        $cartItem = CartItem::updateOrCreate(
            [
                'ID_PEMBELI' => $user->ID_PEMBELI,
                'ID_BARANG' => $request->ID_BARANG,
            ],
            ['QUANTITY' => $quantity]
        );

        return response()->json([
            'success' => true,
            'data' => $cartItem,
        ]);
    }

    // Hapus barang dari cart
    public function remove(Request $request, $id_barang)
    {
        $user = Auth::user();

        $deleted = CartItem::where('ID_PEMBELI', $user->ID_PEMBELI)
            ->where('ID_BARANG', $id_barang)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Berhasil hapus barang dari cart']);
        }

        return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan di cart'], 404);
    }
}
