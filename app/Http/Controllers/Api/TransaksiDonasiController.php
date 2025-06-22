<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDonasi;
use App\Models\Request as RequestModel; // Alias for App\Models\Request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiDonasiController extends Controller
{

    public function index()
    {
        try {
            $transaksis = TransaksiDonasi::with(['organisasi', 'request', 'request.barang'])->get();
            return response()->json([
                'success' => true,
                'data' => $transaksis,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi donasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $illuminateRequest)
    {
        $validator = Validator::make($illuminateRequest->all(), [
            'ID_REQUEST' => 'required|exists:requests,ID_REQUEST', // Ensure request exists
            'TGL_DONASI' => 'required|date',
            'PENERIMA' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            // Fetch the related Request to get ID_ORGANISASI
            $requestModel = RequestModel::find($validatedData['ID_REQUEST']);
            if (!$requestModel) {
                // This case should ideally be caught by 'exists' validation rule
                return response()->json(['success' => false, 'message' => 'Request tidak ditemukan'], 404);
            }

            $transaksiDonasi = TransaksiDonasi::create([
                'ID_ORGANISASI' => $requestModel->ID_ORGANISASI,
                'ID_REQUEST' => $validatedData['ID_REQUEST'],
                'TGL_DONASI' => $validatedData['TGL_DONASI'],
                'PENERIMA' => $validatedData['PENERIMA'],
            ]);

            // Optionally, update the status of the $requestModel here if needed
            // e.g., $requestModel->update(['STATUS_REQUEST' => 'Donasi Dicatat']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Donasi berhasil dicatat',
                'data' => $transaksiDonasi->load(['organisasi', 'request', 'request.barang'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mencatat transaksi donasi: ' . $e->getMessage()], 500);
        }
    }

    public function show(TransaksiDonasi $transaksiDonasi)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $transaksiDonasi->load(['organisasi', 'request', 'request.barang']),
            ], 200);
        } catch (\Exception $e) {
            // This might occur if loading relations fails, though unlikely with findOrFail
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail transaksi donasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $illuminateRequest, TransaksiDonasi $transaksiDonasi)
    {
        $validator = Validator::make($illuminateRequest->all(), [
            'TGL_DONASI' => 'sometimes|required|date',
            'PENERIMA' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();
            $transaksiDonasi->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Donasi berhasil diperbarui',
                'data' => $transaksiDonasi->fresh()->load(['organisasi', 'request', 'request.barang']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi donasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(TransaksiDonasi $transaksiDonasi)
    {
        try {
            $transaksiDonasi->delete();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Donasi berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi donasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
