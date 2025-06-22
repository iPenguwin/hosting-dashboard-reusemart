<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan ini adalah model User Anda yang digunakan oleh Filament
use Laravel\Sanctum\PersonalAccessToken; // Jika Anda menggunakan Sanctum untuk token API
use Filament\Facades\Filament;

class FilamentSsoController extends Controller
{
    public function loginWithToken(Request $request)
    {
        $tokenValue = $request->query('auth_token');
        $roleFromQuery = $request->query('role');

        if (!$tokenValue) {
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'Authentication token is missing.']);
        }
        if (!$roleFromQuery) {
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'User role is missing.']);
        }

        // Asumsikan token yang dikirim adalah token Sanctum plain text.
        // Jika token Anda adalah jenis lain (misalnya JWT), Anda perlu menyesuaikan validasi ini.
        $tokenInstance = PersonalAccessToken::findToken($tokenValue);

        if (!$tokenInstance) {
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'Invalid token provided.']);
        }

        $user = $tokenInstance->tokenable; // Mengambil model User yang terkait dengan token

        if (!$user || !($user instanceof User)) {
            // Pastikan $user adalah instance dari App\Models\User atau model User Anda
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'User not found or invalid user type for the provided token.']);
        }

        // Verifikasi apakah peran pengguna dari token sesuai dengan peran yang dikirim dari frontend (opsional, untuk keamanan tambahan)
        // Anda mungkin perlu menambahkan kolom 'role' di tabel user atau menggunakan package seperti Spatie Permissions
        // Contoh:
        // if (method_exists($user, 'hasRole') && !$user->hasRole($roleFromQuery)) { // Jika menggunakan Spatie Permissions
        //     return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'User role mismatch.']);
        // }
        // Atau jika Anda memiliki kolom 'role' langsung di model User:
        // if (strtolower($user->role) !== strtolower($roleFromQuery)) {
        //     return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'User role mismatch.']);
        // }


        // Login pengguna ke sesi Laravel untuk Filament
        Auth::login($user);

        // Regenerasi ID sesi untuk keamanan
        $request->session()->regenerate();

        // Tentukan Panel ID Filament berdasarkan peran
        $panelId = null;
        switch (strtolower($roleFromQuery)) {
            case 'pegawai':
                $panelId = 'admin'; // Sesuaikan dengan ID panel Filament untuk pegawai
                break;
            case 'penitip':
                $panelId = 'penitip'; // Sesuaikan dengan ID panel Filament untuk penitip
                break;
            case 'organisasi':
                $panelId = 'organisasi'; // Sesuaikan dengan ID panel Filament untuk organisasi
                break;
            // Tambahkan case lain jika ada peran lain dengan panel Filament
            default:
                // Jika peran tidak dikenali atau tidak memiliki panel khusus,
                // coba arahkan ke panel default jika ada, atau ke halaman login.
                // Anda bisa juga mengarahkan ke panel 'admin' sebagai fallback jika sesuai.
                // $defaultPanel = Filament::getDefaultPanel();
                // if ($defaultPanel) {
                //    return redirect()->to($defaultPanel->getDashboardUrl());
                // }
                return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'No Filament panel configured for your role.']);
        }

        if (!$panelId) {
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'Filament panel ID could not be determined.']);
        }

        $panel = Filament::getPanel($panelId);

        if (!$panel) {
            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => "Filament panel '{$panelId}' not found."]);
        }

        // Arahkan ke URL dashboard dari panel yang sesuai
        return redirect()->to($panel->getDashboardUrl());
    }
}
