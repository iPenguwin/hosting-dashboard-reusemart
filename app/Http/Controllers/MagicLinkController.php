<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\Pegawai;
use App\Notifications\MagicLinkLogin;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class MagicLinkController extends Controller
{
    // For Penitip
    public function create()
    {
        return view('auth.magic-link-login'); // View for Penitip
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Penitip::where('EMAIL_PENITIP', $request->email)->first();

        if (!$user) {
            Log::error('Email tidak ditemukan untuk Penitip: ' . $request->email);
            return response()->json(['error' => 'Account with matching email address not found.'], 404);
        }

        $url = URL::temporarySignedRoute(
            'login.token',
            now()->addMinutes(30),
            ['user' => $user->ID_PENITIP]
        );

        try {
            $user->notify(new MagicLinkLogin($url));
            Log::info('Magic link dikirim ke Penitip: ' . $request->email, ['url' => $url]);

            return response()->json([
                'message' => 'Magic link dikirim ke: ' . $request->email,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim magic link untuk Penitip: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengirim magic link.'], 500);
        }
    }

    public function loginViaToken(Request $request)
    {
        $user = Penitip::findOrFail($request->user);

        Auth::guard('penitip')->login($user);

        Log::info('Penitip logged in via token.', ['email' => $user->EMAIL_PENITIP]);

        $request->session()->regenerate();

        return redirect()->intended(route('filament.penitip.pages.dashboard', absolute: false));
    }

    // For Pegawai
    public function createPegawai()
    {
        return view('auth.pegawai-magic-link-login'); // Create a new view for Pegawai if needed
    }

    public function storePegawai(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Pegawai::where('EMAIL_PEGAWAI', $request->email)->first();

        if (!$user) {
            Log::error('Email tidak ditemukan untuk Pegawai: ' . $request->email);
            return response()->json(['error' => 'Account with matching email address not found.'], 404);
        }

        $url = URL::temporarySignedRoute(
            'pegawai.login.token',
            now()->addMinutes(30),
            ['user' => $user->ID_PEGAWAI]
        );

        try {
            $user->notify(new MagicLinkLogin($url));
            Log::info('Magic link dikirim ke Pegawai: ' . $request->email, ['url' => $url]);

            return response()->json([
                'message' => 'Magic link dikirim ke: ' . $request->email,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim magic link untuk Pegawai: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengirim magic link.'], 500);
        }
    }

    public function loginPegawaiViaToken(Request $request)
    {
        $user = Pegawai::findOrFail($request->user);

        // Log in the user using the 'pegawai' guard
        Auth::guard('pegawai')->login($user);

        Log::info('Pegawai logged in via token.', [
            'email' => $user->EMAIL_PEGAWAI,
            'jabatan' => $user->jabatan
        ]);

        // Regenerate the session to prevent session fixation
        $request->session()->regenerate();

        // Check the user's Jabatan (role) to determine the redirect destination
        $jabatan = strtolower($user->jabatan);
        $panel = Filament::getPanel('pegawai');

        // Verify if the user can access the 'pegawai' panel
        if ($user->canAccessPanel($panel)) {
            if (in_array($jabatan, ['admin', 'owner'])) {
                return redirect()->intended(route('filament.admin.pages.dashboard', absolute: false));
            }
            return redirect()->intended(route('filament.pegawai.pages.dashboard', absolute: false));
        }

        // If the user cannot access the panel, log the issue and redirect to login
        Log::warning('Pegawai failed to access panel.', [
            'email' => $user->EMAIL_PEGAWAI,
            'jabatan' => $jabatan
        ]);
        return redirect()->route('filament.pegawai.auth.login')->withErrors([
            'access' => 'You do not have access to the Pegawai panel.'
        ]);
    }
}
