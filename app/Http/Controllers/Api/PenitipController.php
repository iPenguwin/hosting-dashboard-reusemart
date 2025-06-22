<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\PenitipResource as ResourcesPenitipResource;
use App\Http\Controllers\Controller;
use App\Models\Penitip;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\PenitipResource;
use App\Models\TransaksiPenitipanBarang;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PenitipController extends Controller
{
    /**
     * Register new penitip
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAMA_PENITIP' => 'required|string|max:255',
            'PROFILE_PENITIP' => 'nullable|string|max:255',
            'NO_KTP' => 'required|string|max:16',
            'ALAMAT_PENITIP' => 'required|string|max:255',
            'TGL_LAHIR_PENITIP' => 'required|date',
            'NO_TELP_PENITIP' => 'required|string|max:25',
            'EMAIL_PENITIP' => 'required|string|email|max:255|unique:penitips,EMAIL_PENITIP',
            'PASSWORD_PENITIP' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['POINT_LOYALITAS_PENITIP'] = 0;

        $penitip = Penitip::create($validated);
        $token = \Illuminate\Support\Str::random(60);
        $penitip->remember_token = hash('sha256', $token);
        $penitip->save();

        $plainTextToken = $token;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi penitip berhasil',
            'data' => $penitip,
            'token' => $plainTextToken,
        ], 201);
    }

    /**
     * Login penitip
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'EMAIL_PENITIP' => 'required|string|max:25',
            'PASSWORD_PENITIP' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $penitip = Penitip::where('EMAIL_PENITIP', $request->EMAIL_PENITIP)->first();

        if (!$penitip || !Hash::check($request->PASSWORD_PENITIP, $penitip->PASSWORD_PENITIP)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = \Illuminate\Support\Str::random(60);
        $penitip->remember_token = hash('sha256', $token);
        $penitip->save();

        $plainTextToken = $token;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $plainTextToken,
            'data' => new ResourcesPenitipResource($penitip),
        ]);
    }

    /**
     * Login with token
     */
    public function loginWithToken(string $token)
    {
        $hashedToken = hash('sha256', $token);
        \Illuminate\Support\Facades\Log::info('Hashed Token: ' . $hashedToken);
        $penitip = Penitip::where('remember_token', $hashedToken)->first();

        if (! $penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        auth()->guard('penitip')->login($penitip);

        session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => new ResourcesPenitipResource($penitip)
        ]);
    }

    /**
     * Logout penitip
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $penitip = $request->user();
        // Eager load komisis relationship
        $penitip->load('komisis');

        $totalKomisi = $penitip->komisis->sum('NOMINAL_KOMISI');
        // tanggal hari ini dalam format Y-m-d
        $today = Carbon::now()->toDateString();

        $badge = $penitip->badges()
            ->where('NAMA_BADGE', 'Top Seller')
            ->where('START_DATE', '<=', $today)
            ->where('END_DATE', '>=', $today)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'ID_PENITIP'             => $penitip->ID_PENITIP,
                'NAMA_PENITIP'           => $penitip->NAMA_PENITIP,
                'EMAIL_PENITIP'          => $penitip->EMAIL_PENITIP,
                'NO_TELP_PENITIP'        => $penitip->NO_TELP_PENITIP,
                'ALAMAT_PENITIP'         => $penitip->ALAMAT_PENITIP,
                'SALDO_PENITIP'          => $penitip->SALDO_PENITIP,
                'POINT_LOYALITAS_PENITIP' => $penitip->POINT_LOYALITAS_PENITIP,
                'TOTAL_KOMISI_PENITIP'   => $totalKomisi,
                'RATING_PENITIP'         => $penitip->RATING_PENITIP,
                'badge'          => $badge ? [
                    'name' => $badge->NAMA_BADGE,
                    'from' => $badge->START_DATE,
                    'to'   => $badge->END_DATE,
                ] : null,
            ],
        ]);
    }

    /**
     * List all penitip
     */
    public function index()
    {
        $penitips = Penitip::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar penitip',
            'data' => $penitips
        ]);
    }

    /**
     * Show specific penitip
     */
    public function show(Penitip $penitip)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail penitip',
            'data' => $penitip
        ]);
    }

    /**
     * Update penitip
     */
    public function update(Request $request)
    {
        $penitip = $request->user();

        $validator = Validator::make($request->all(), [
            'NAMA_PENITIP' => 'sometimes|string|max:255',
            'PROFILE_PENITIP' => 'nullable|string|max:255',
            'NO_KTP' => 'sometimes|string|max:16',
            'ALAMAT_PENITIP' => 'sometimes|string|max:255',
            'TGL_LAHIR_PENITIP' => 'sometimes|date',
            'NO_TELP_PENITIP' => 'sometimes|string|max:25',
            'EMAIL_PENITIP' => 'sometimes|string|email|max:255|unique:penitips,EMAIL_PENITIP,' . $penitip->ID_PENITIP . ',ID_PENITIP',
            'PASSWORD_PENITIP' => 'sometimes|string|min:8',
            'POINT_LOYALITAS_PENITIP' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['PASSWORD_PENITIP'])) {
            if (!password_get_info($validated['PASSWORD_PENITIP'])['algo']) {
                $validated['PASSWORD_PENITIP'] = bcrypt($validated['PASSWORD_PENITIP']);
            }
        }

        $penitip->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data penitip diperbarui',
            'data' => $penitip
        ]);
    }

    /**
     * Delete penitip
     */
    public function destroy(Penitip $penitip)
    {
        $penitip->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penitip dihapus',
            'data' => null
        ], 204);
    }

    public function getByPenitip(Request $req)
    {
        // Get the ID of the authenticated Penitip user
        $userId = $req->user()->ID_PENITIP;
        $q = TransaksiPenitipanBarang::where('ID_PENITIP', $userId);

        if ($req->has('from') && $req->has('to')) {
            $q->whereBetween('TGL_MASUK_TITIPAN', [
                Carbon::parse($req->from)->startOfDay(),
                Carbon::parse($req->to)->endOfDay(),
            ]);
        }

        $list = $q->with('detailTransaksiPenitipans')->get();
        return response()->json([
            'success' => true,
            'data'    => $list,
        ]);
    }

    public function ratePenitip(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $penitip = Penitip::findOrFail($id);

        // Simpan rating – di sini bisa disimpan langsung ke kolom RATING_PENITIP
        // atau kamu prefer menambah logika rata-rata seperti di rate() pada BarangController
        $penitip->RATING_PENITIP = $request->rating;
        $penitip->save();

        return response()->json([
            'success'         => true,
            'message'         => 'Rating penitip berhasil disimpan',
            'data'            => ['rating_penitip' => $penitip->RATING_PENITIP],
        ]);
    }
}
