<?php

namespace App\Http\Controllers\Api;

use App\Models\TransaksiPenitipanBarang;
use App\Http\Controllers\Controller;
use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiPenitipanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksiPenitipan = TransaksiPenitipanBarang::with(['penitip', 'detailTransaksiPenitipans', 'pegawaiTransaksiPenitipans'])->get();
        return response()->json(['data' => $transaksiPenitipan], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_PENITIP' => 'required|exists:penitips,ID_PENITIP',
            'TGL_MASUK_TITIPAN' => 'required|date',
            'TGL_KELUAR_TITIPAN' => 'nullable|date|after_or_equal:TGL_MASUK_TITIPAN',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaksi = TransaksiPenitipanBarang::create($request->all());

        return response()->json([
            'message' => 'Transaksi penitipan barang created successfully',
            'data' => $transaksi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiPenitipanBarang::with(['penitip', 'detailTransaksiPenitipans', 'pegawaiTransaksiPenitipans'])
            ->find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi penitipan barang not found'], 404);
        }

        return response()->json(['data' => $transaksi], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = TransaksiPenitipanBarang::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi penitipan barang not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ID_PENITIP' => 'exists:penitips,ID_PENITIP',
            'TGL_MASUK_TITIPAN' => 'date',
            'TGL_KELUAR_TITIPAN' => 'nullable|date|after_or_equal:TGL_MASUK_TITIPAN',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaksi->update($request->all());

        return response()->json([
            'message' => 'Transaksi penitipan barang updated successfully',
            'data' => $transaksi
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = TransaksiPenitipanBarang::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi penitipan barang not found'], 404);
        }

        // Check if there are related records that might prevent deletion
        if ($transaksi->detailTransaksiPenitipans()->count() > 0 || $transaksi->pegawaiTransaksiPenitipans()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete transaksi penitipan barang because it has related records',
                'suggestion' => 'Delete the related records first or consider using soft deletes'
            ], 400);
        }

        $transaksi->delete();

        return response()->json(['message' => 'Transaksi penitipan barang deleted successfully'], 200);
    }

    /**
     * Get transactions by penitip ID
     */
    // app/Http/Controllers/Api/TransaksiPenitipanController.php

    public function getByPenitip($idPenitip)
    {
        $penitip = Penitip::find($idPenitip);
        if (!$penitip) {
            return response()->json(['message' => 'Penitip not found'], 404);
        }

        $transactions = TransaksiPenitipanBarang::where('ID_PENITIP', $idPenitip)
            // tambahkan barang di dalam detailTransaksiPenitipans
            ->with([
            'detailTransaksiPenitipans.barang', 
            'pegawaiTransaksiPenitipans.pegawai'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ], 200);
    }

    /**
     * Get active transactions (where TGL_KELUAR_TITIPAN is null or in the future)
     */
    public function getActiveTransactions()
    {
        $activeTransactions = TransaksiPenitipanBarang::whereNull('TGL_KELUAR_TITIPAN')
            ->orWhere('TGL_KELUAR_TITIPAN', '>', now())
            ->with(['penitip', 'detailTransaksiPenitipans'])
            ->get();

        return response()->json(['data' => $activeTransactions], 200);
    }

    public function transactions(Request $request)
    {
        $user = auth()->user(); // guard: penitip
        $query = TransaksiPenitipanBarang::where('ID_PENITIP', $user->ID_PENITIP);

        if ($request->has('start')) {
            $query->whereDate('TGL_MASUK_TITIPAN', '>=', $request->query('start'));
        }
        if ($request->has('end')) {
            $query->whereDate('TGL_MASUK_TITIPAN', '<=', $request->query('end'));
        }

        $tx = $query
            // eager load detail + barang di dalamnya
            ->with('detailTransaksiPenitipans.barang')
            ->orderBy('TGL_MASUK_TITIPAN', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $tx,
        ], 200);
    }
}
