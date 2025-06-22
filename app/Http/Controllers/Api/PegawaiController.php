<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Komisi;
use App\Models\TransaksiPembelianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'ID_JABATAN' => 'required|exists:jabatans,ID_JABATAN',
            'NAMA_PEGAWAI' => 'required|string|max:255',
            'PROFILE_PEGAWAI' => 'nullable|string|max:255',
            'NO_TELP_PEGAWAI' => 'required|string|max:25',
            'EMAIL_PEGAWAI' => 'required|email|unique:pegawais,EMAIL_PEGAWAI',
            'PASSWORD_PEGAWAI' => 'required|string|min:8|confirmed',
            'TGL_LAHIR_PEGAWAI' => 'required|date',
        ]);

        $pegawai = Pegawai::create([
            'ID_JABATAN' => $validated['ID_JABATAN'],
            'NAMA_PEGAWAI' => $validated['NAMA_PEGAWAI'],
            'PROFILE_PEGAWAI' => $validated['PROFILE_PEGAWAI'],
            'NO_TELP_PEGAWAI' => $validated['NO_TELP_PEGAWAI'],
            'EMAIL_PEGAWAI' => $validated['EMAIL_PEGAWAI'],
            'PASSWORD_PEGAWAI' => bcrypt($validated['PASSWORD_PEGAWAI']),
            'TGL_LAHIR_PEGAWAI' => $validated['TGL_LAHIR_PEGAWAI'],
        ]);

        $token = $pegawai->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $pegawai,
            'token' => $token
        ]);
    }

    /**
     * Handles login for Pegawai, Pembeli, and Penitip.
     * Note: This login logic is duplicated in AuthController.php. Consider consolidating to a single login endpoint.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Cek pegawai dulu
        $pegawai = Pegawai::where('EMAIL_PEGAWAI', $request->email)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->PASSWORD_PEGAWAI)) {
            $token = $pegawai->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $pegawai,
                'token' => $token,
                'role' => 'pegawai',
                'jabatan' => $pegawai->jabatans->NAMA_JABATAN ?? null
            ]);
        }

        // 2. Cek pembeli
        $pembeli = Pembeli::where('EMAIL_PEMBELI', $request->email)->first();
        if ($pembeli && Hash::check($request->password, $pembeli->PASSWORD_PEMBELI)) {
            $token = $pembeli->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $pembeli,
                'token' => $token,
                'role' => 'pembeli',
            ]);
        }

        // 3. Cek penitip
        $penitip = Penitip::where('EMAIL_PENITIP', $request->email)->first();
        if ($penitip && Hash::check($request->password, $penitip->PASSWORD_PENITIP)) {
            $token = $penitip->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $penitip,
                'token' => $token,
                'role' => 'penitip',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function index()
    {
        $pegawais = Pegawai::with('jabatans')->get();
        return response()->json($pegawais);
    }

    public function create()
    {
        $jabatans = Jabatan::all();
        return view('pegawai.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_JABATAN' => 'required|exists:jabatans,ID_JABATAN',
            'NAMA_PEGAWAI' => 'required|string|max:255',
            'PROFILE_PEGAWAI' => 'nullable|string|max:255',
            'NO_TELP_PEGAWAI' => 'required|string|max:25',
            'EMAIL_PEGAWAI' => 'required|email|unique:pegawais,EMAIL_PEGAWAI',
            'PASSWORD_PEGAWAI' => 'required|string|min:8|confirmed',
            'TGL_LAHIR_PEGAWAI' => 'required|date',
        ]);

        if (!password_get_info($validated['PASSWORD_PEGAWAI'])['algo']) {
            $validated['PASSWORD_PEGAWAI'] = bcrypt($validated['PASSWORD_PEGAWAI']);
        }

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai created successfully.');
    }

    public function show(Pegawai $pegawai)
    {
        return view('pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $jabatans = Jabatan::all();
        return view('pegawai.edit', compact('pegawai', 'jabatans'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'ID_JABATAN' => 'required|exists:jabatans,ID_JABATAN',
            'NAMA_PEGAWAI' => 'required|string|max:255',
            'PROFILE_PEGAWAI' => 'nullable|string|max:255',
            'NO_TELP_PEGAWAI' => 'required|string|max:25',
            'EMAIL_PEGAWAI' => 'required|email|unique:pegawais,EMAIL_PEGAWAI,' . $pegawai->ID_PEGAWAI . ',ID_PEGAWAI',
            'PASSWORD_PEGAWAI' => 'nullable|string|min:8|confirmed',
            'TGL_LAHIR_PEGAWAI' => 'required|date',
        ]);

        if ($request->filled('PASSWORD_PEGAWAI')) {
            if (!password_get_info($validated['PASSWORD_PEGAWAI'])['algo']) {
                $validated['PASSWORD_PEGAWAI'] = bcrypt($validated['PASSWORD_PEGAWAI']);
            }
        } else {
            unset($validated['PASSWORD_PEGAWAI']);
        }

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai updated successfully.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->route('pegawai.index')->with('success', 'Pegawai deleted successfully.');
    }

    public function calculateKomisi(Pegawai $pegawai)
    {
        if (strtolower($pegawai->jabatans->NAMA_JABATAN) === 'hunter') {
            $komisi = $pegawai->komisis()->sum('NOMINAL_KOMISI');
            $pegawai->update(['KOMISI_PEGAWAI' => $komisi]);
            return $komisi;
        }

        return 0;
    }

    public function showKurir($id)
    {
        $pegawai = Pegawai::with('jabatans')->findOrFail($id);
        return response()->json($pegawai);
    }

    public function getKurirTugas($id)
    {
        $tugas = TransaksiPembelianBarang::whereHas('pegawaiTransaksiPembelians', function ($query) use ($id) {
            $query->where('ID_PEGAWAI', $id);
        })
            ->where('STATUS_TRANSAKSI', 'Dikirim')
            ->where('STATUS_BARANG', 'Dalam Pengiriman')
            ->get();

        return response()->json($tugas);
    }

    public function updateStatusPengiriman(Request $request, $id)
    {
        $tugas = TransaksiPembelianBarang::findOrFail($id);
        $pegawaiId = $request->user()->ID_PEGAWAI;

        // Tambahkan relasi ke pegawai_transaksi_pembelians
        $tugas->pegawaiTransaksiPembelians()->updateOrCreate(
            ['ID_TRANSAKSI_PEMBELIAN' => $id, 'ID_PEGAWAI' => $pegawaiId],
            []
        );

        // Update status dan TGL_AMBIL_KIRIM
        $tugas->update([
            'STATUS_BARANG' => 'Terkirim',
            'STATUS_TRANSAKSI' => 'Selesai',
            'TGL_AMBIL_KIRIM' => now()
        ]);

        return response()->json(['message' => 'Status pengiriman berhasil diperbarui.']);
    }

    public function getKurirTugasHistory($id)
    {
        $tugas = TransaksiPembelianBarang::whereHas('pegawaiTransaksiPembelians', function ($query) use ($id) {
            $query->where('ID_PEGAWAI', $id);
        })
            ->where('STATUS_TRANSAKSI', 'Selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tugas);
    }

    public function getHunterKomisi($id)
    {
        $komisis = Komisi::where('ID_PEGAWAI', $id)
                    ->with(['transaksiPembelian']) // eager load relasi transaksi pembelian
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Format respons JSON agar status barang ikut muncul
        $result = $komisis->map(function ($komisi) {
            return [
                'ID_KOMISI' => $komisi->ID_KOMISI,
                'NOMINAL_KOMISI' => $komisi->NOMINAL_KOMISI,
                'TGL_KOMISI' => $komisi->created_at,
                'STATUS_BARANG' => optional($komisi->transaksiPembelian)->STATUS_BARANG ?? 'Unknown',
            ];
        });

        return response()->json($result);
    }

    /**
     * Get the authenticated Pegawai's profile data.
     */
    public function me(Request $request)
    {
        $pegawai = $request->user();

        if (!$pegawai || !$pegawai instanceof Pegawai) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or not a Pegawai.'], 401);
        }

        $pegawai->load('jabatans'); // Eager load jabatan

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai yang sedang login',
            'data' => [
                'ID_PEGAWAI' => $pegawai->ID_PEGAWAI,
                'NAMA_PEGAWAI' => $pegawai->NAMA_PEGAWAI,
                'EMAIL_PEGAWAI' => $pegawai->EMAIL_PEGAWAI,
                'NO_TELP_PEGAWAI' => $pegawai->NO_TELP_PEGAWAI,
                'TGL_LAHIR_PEGAWAI' => $pegawai->TGL_LAHIR_PEGAWAI,
                'PROFILE_PEGAWAI' => $pegawai->PROFILE_PEGAWAI,
                'ID_JABATAN' => $pegawai->ID_JABATAN,
                'jabatan' => $pegawai->jabatans ? $pegawai->jabatans->NAMA_JABATAN : null,
            ]
        ]);
    }

    /**
     * Update the authenticated Pegawai's profile.
     */
    public function updateMe(Request $request)
    {
        $pegawai = $request->user();

        if (!$pegawai || !$pegawai instanceof Pegawai) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or not a Pegawai.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'NAMA_PEGAWAI' => 'sometimes|required|string|max:255',
            'NO_TELP_PEGAWAI' => 'sometimes|required|string|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $pegawai->update($validator->validated());

        // Return fresh data, including jabatan
        return response()->json(['success' => true, 'message' => 'Profil pegawai berhasil diperbarui', 'data' => $pegawai->fresh()->load('jabatans')]);
    }
}
