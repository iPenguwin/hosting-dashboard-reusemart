<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Penitip;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Cek di table pegawai
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

        // 2. Cek di table pembeli
        $pembeli = Pembeli::where('EMAIL_PEMBELI', $request->email)->first();
        if ($pembeli && Hash::check($request->password, $pembeli->PASSWORD_PEMBELI)) {
            $token = $pembeli->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $pembeli,
                'token' => $token,
                'role' => 'pembeli',
            ]);
        }

        // 3. Cek di table penitip (tambah ini)
        $penitip = Penitip::where('EMAIL_PENITIP', $request->email)->first();
        if ($penitip && Hash::check($request->password, $penitip->PASSWORD_PENITIP)) {
            $token = $penitip->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $penitip,
                'token' => $token,
                'role' => 'penitip',
            ]);
        }

        // 4. Jika tidak ada yang cocok
        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }
}

