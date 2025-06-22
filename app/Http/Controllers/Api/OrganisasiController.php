<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\OrganisasiResource;
use Illuminate\Support\Facades\Validator;

class OrganisasiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAMA_ORGANISASI' => 'required|string|max:255',
            'PROFILE_ORGANISASI' => 'nullable|string|max:255',
            'ALAMAT_ORGANISASI' => 'required|string|max:255',
            'NO_TELP_ORGANISASI' => 'required|string|max:25',
            'EMAIL_ORGANISASI' => 'required|string|email|max:255|unique:organisasis,EMAIL_ORGANISASI',
            'PASSWORD_ORGANISASI' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['PASSWORD_ORGANISASI'] = Hash::make($validated['PASSWORD_ORGANISASI']);

        $organisasi = Organisasi::create($validated);
        $token = $organisasi->createToken('organisasi-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi organisasi berhasil',
            'data' => new OrganisasiResource($organisasi),
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'EMAIL_ORGANISASI' => 'required|string|email|max:255',
            'PASSWORD_ORGANISASI' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $organisasi = Organisasi::where('EMAIL_ORGANISASI', $request->EMAIL_ORGANISASI)->first();

        if (!$organisasi || !Hash::check($request->PASSWORD_ORGANISASI, $organisasi->PASSWORD_ORGANISASI)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $organisasi->createToken('organisasi-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => new OrganisasiResource($organisasi)
        ]);
    }

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
        return response()->json([
            'success' => true,
            'message' => 'Data organisasi yang sedang login',
            'data' => new OrganisasiResource($request->user())
        ]);
    }

    public function index()
    {
        $organisasis = Organisasi::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar data organisasi',
            'data' => OrganisasiResource::collection($organisasis)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAMA_ORGANISASI' => 'required|string|max:255',
            'PROFILE_ORGANISASI' => 'nullable|string|max:255',
            'ALAMAT_ORGANISASI' => 'required|string|max:255',
            'NO_TELP_ORGANISASI' => 'required|string|max:25',
            'EMAIL_ORGANISASI' => 'required|string|email|max:255|unique:organisasis,EMAIL_ORGANISASI',
            'PASSWORD_ORGANISASI' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (!password_get_info($validated['PASSWORD_ORGANISASI'])['algo']) {
            $validated['PASSWORD_ORGANISASI'] = bcrypt($validated['PASSWORD_ORGANISASI']);
        }

        $organisasi = Organisasi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Organisasi berhasil dibuat',
            'data' => new OrganisasiResource($organisasi)
        ], 201);
    }

    public function show(Organisasi $organisasi)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail organisasi',
            'data' => new OrganisasiResource($organisasi)
        ]);
    }

    // This method is for admin to update any organization by ID
    public function update(Request $request, Organisasi $organisasi) 
    {
        $validator = Validator::make($request->all(), [
            'NAMA_ORGANISASI' => 'sometimes|string|max:255',
            'PROFILE_ORGANISASI' => 'nullable|string|max:255',
            'ALAMAT_ORGANISASI' => 'sometimes|required|string|max:255', // Assuming if sent, it's required
            'NO_TELP_ORGANISASI' => 'sometimes|required|string|max:25', // Assuming if sent, it's required
            'EMAIL_ORGANISASI' => 'sometimes|string|email|max:255|unique:organisasis,EMAIL_ORGANISASI,' . $organisasi->ID_ORGANISASI . ',ID_ORGANISASI',
            'PASSWORD_ORGANISASI' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['PASSWORD_ORGANISASI'])) {
            if (!password_get_info($validated['PASSWORD_ORGANISASI'])['algo']) {
                $validated['PASSWORD_ORGANISASI'] = bcrypt($validated['PASSWORD_ORGANISASI']);
            }
        }

        $organisasi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Organisasi berhasil diperbarui',
            'data' => new OrganisasiResource($organisasi)
        ]);
    }

    /**
     * Update the authenticated organization's profile.
     */
    public function updateMe(Request $request)
    {
        $organisasi = $request->user();

        if (!$organisasi instanceof Organisasi) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or not an organization.'], 401);
        }

        // Frontend sends: NAMA_ORGANISASI, NO_TELP_ORGANISASI, ALAMAT_ORGANISASI
        // Email and Password are not updated via this form in the frontend.
        $validator = Validator::make($request->all(), [
            'NAMA_ORGANISASI' => 'required|string|max:255',
            'NO_TELP_ORGANISASI' => 'required|string|max:25',
            'ALAMAT_ORGANISASI' => 'required|string|max:255',
            'PROFILE_ORGANISASI' => 'nullable|string|max:255', // Add if frontend supports this
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $organisasi->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profil organisasi berhasil diperbarui',
            'data' => new OrganisasiResource($organisasi->fresh())
        ]);
    }

    public function destroy(Organisasi $organisasi)
    {
        $organisasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organisasi berhasil dihapus',
            'data' => null
        ], 204);
    }
}
