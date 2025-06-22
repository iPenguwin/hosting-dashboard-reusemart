<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PembeliController extends Controller
{
    /**
     * Register new pembeli
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAMA_PEMBELI' => 'required|string|max:255',
            'PROFILE_PENITIP' => 'nullable|string|max:255',
            'TGL_LAHIR_PEMBELI' => 'required|date',
            'NO_TELP_PEMBELI' => 'required|string|max:25',
            'EMAIL_PEMBELI' => 'required|string|email|max:255|unique:pembelis,EMAIL_PEMBELI',
            'PASSWORD_PEMBELI' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['PASSWORD_PEMBELI'] = Hash::make($validated['PASSWORD_PEMBELI']);
        $validated['POINT_LOYALITAS_PEMBELI'] = 0;

        $pembeli = Pembeli::create($validated);
        $token = $pembeli->createToken('pembeli-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi pembeli berhasil',
            'data' => $pembeli,
            'token' => $token
        ], 201);
    }

    /**
     * Login pembeli
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'EMAIL_PEMBELI' => 'required|string|email',
            'PASSWORD_PEMBELI' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pembeli = Pembeli::where('EMAIL_PEMBELI', $request->EMAIL_PEMBELI)->first();

        if (!$pembeli || !Hash::check($request->PASSWORD_PEMBELI, $pembeli->PASSWORD_PEMBELI)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $pembeli->createToken('pembeli-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => $pembeli
        ]);
    }

    /**
     * Logout pembeli
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get authenticated pembeli data
     */
    public function showData(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pembeli yang sedang login',
            'data' => $request->user()
        ]);
    }

    /**
     * Display a listing of the pembelis.
     */
    public function index()
    {
        $pembelis = Pembeli::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar data pembeli',
            'data' => $pembelis
        ]);
    }

    /**
     * Display the specified pembeli.
     */
    public function show(Pembeli $pembeli)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pembeli',
            'data' => $pembeli
        ]);
    }

    /**
     * Update the specified pembeli in storage.
     */
    public function update(Request $request, Pembeli $pembeli)
    {
        $pembeli = $request->user(); // Get logged-in pembeli

        $validator = Validator::make($request->all(), [
            'NAMA_PEMBELI' => 'sometimes|string|max:255',
            'PROFILE_PENITIP' => 'nullable|string|max:255',
            'TGL_LAHIR_PEMBELI' => 'sometimes|date',
            'NO_TELP_PEMBELI' => 'sometimes|string|max:25',
            'EMAIL_PEMBELI' => 'sometimes|string|email|max:255|unique:pembelis,EMAIL_PEMBELI,' . $pembeli->ID_PEMBELI . ',ID_PEMBELI',
            'PASSWORD_PEMBELI' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['PASSWORD_PEMBELI'])) {
            $validated['PASSWORD_PEMBELI'] = bcrypt($validated['PASSWORD_PEMBELI']);
        }

        $pembeli->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pembeli berhasil diperbarui',
            'data' => $pembeli->fresh()
        ]);
    }

    /**
     * Remove the specified pembeli from storage.
     */
    public function destroy(Pembeli $pembeli)
    {
        $pembeli->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembeli berhasil dihapus',
            'data' => null
        ], 204);
    }

    /**
     * Update pembeli point
     */
    public function updateLoyaltyPoints(Request $request, Pembeli $pembeli)
    {
        $validator = Validator::make($request->all(), [
            'transaction_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $transactionAmount = $request->transaction_amount;
        $basePoints = floor($transactionAmount / 10000); // 1 point per Rp10.000

        // Bonus 20% if transaction > Rp500.000
        if ($transactionAmount > 500000) {
            $bonusPoints = floor($basePoints * 0.2);
            $totalPoints = $basePoints + $bonusPoints;
        } else {
            $totalPoints = $basePoints;
        }

        // Update pembeli's points
        $pembeli->POINT_LOYALITAS_PEMBELI += $totalPoints;
        $pembeli->save();

        return response()->json([
            'success' => true,
            'message' => 'Poin loyalitas berhasil diperbarui',
            'data' => [
                'transaction_amount' => $transactionAmount,
                'base_points' => $basePoints,
                'bonus_points' => $bonusPoints ?? 0,
                'total_points_added' => $totalPoints,
                'new_total_points' => $pembeli->POINT_LOYALITAS_PEMBELI
            ]
        ]);
    }
}
