<?php
// app/Http/Controllers/Api/AlamatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\DesaKelurahan;
use App\Models\Pembeli;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    // In your controller
    public function index()
    {
        $alamats = auth()->user()->alamats()->with([
            'provinsiData:id_provinsi,nama_provinsi',
            'kabupatenData:id_kabupaten_kota,nama_kabupaten_kota',
            'kecamatanData:id_kecamatan,nama_kecamatan',
            'desaData:id_desa_kelurahan,nama_desa_kelurahan'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $alamats->map(function ($alamat) {
                return [
                    'ID_ALAMAT' => $alamat->ID_ALAMAT,
                    'JUDUL' => $alamat->JUDUL,
                    'NAMA_JALAN' => $alamat->NAMA_JALAN,
                    'PROVINSI' => $alamat->provinsiData->nama_provinsi,
                    'KABUPATEN' => $alamat->kabupatenData->nama_kabupaten_kota,
                    'KECAMATAN' => $alamat->kecamatanData->nama_kecamatan,
                    'DESA_KELURAHAN' => $alamat->desaData->nama_desa_kelurahan
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'JUDUL' => 'required|string|max:100',
            'NAMA_JALAN' => 'required|string|max:255',
            'PROVINSI' => 'required|exists:provinsis,id_provinsi',
            'KABUPATEN' => 'required|exists:kabupatens,id_kabupaten_kota,id_provinsi,' . $request->PROVINSI,
            'KECAMATAN' => 'required|exists:kecamatans,id_kecamatan,id_kabupaten_kota,' . $request->KABUPATEN,
            'DESA_KELURAHAN' => 'required|exists:desa_kelurahans,id_desa_kelurahan,id_kecamatan,' . $request->KECAMATAN
        ]);

        // Set ID_PEMBELI from authenticated user - IMPORTANT FIX
        $validated['ID_PEMBELI'] = $request->user()->ID_PEMBELI; // Use the correct primary key

        $alamat = Alamat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil ditambahkan',
            'data' => $alamat->load(['provinsiData', 'kabupatenData', 'kecamatanData', 'desaData'])
        ], 201);
    }

    public function update(Request $request, Alamat $alamat)
    {
        $validated = $request->validate([
            'JUDUL' => 'required|string|max:100',
            'NAMA_JALAN' => 'required|string|max:255',
            'PROVINSI' => 'required|exists:provinsis,id_provinsi',
            'KABUPATEN' => 'required|exists:kabupatens,id_kabupaten_kota,id_provinsi,' . $request->PROVINSI,
            'KECAMATAN' => 'required|exists:kecamatans,id_kecamatan,id_kabupaten_kota,' . $request->KABUPATEN,
            'DESA_KELURAHAN' => 'required|exists:desa_kelurahans,id_desa_kelurahan,id_kecamatan,' . $request->KECAMATAN
        ]);

        $validated['ID_PEMBELI'] = $request->user()->ID_PEMBELI;

        $alamat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui',
            'data' => $alamat->load(['provinsiData', 'kabupatenData', 'kecamatanData', 'desaData'])
        ], 200);
    }

    public function destroy(Alamat $alamat)
    {
        $this->authorize('delete', $alamat);
        $alamat->delete();
        return response()->json([
            'success' => true,
            'message' => 'Alamat deleted successfully.'
        ]);
    }

    public function getProvinsi()
    {
        $provinsi = Provinsi::all();
        return response()->json($provinsi);
    }

    public function getKabupaten($provinsiId)
    {
        $kabupaten = Kabupaten::where('id_provinsi', $provinsiId)->get();
        return response()->json($kabupaten);
    }

    public function getKecamatan($kabupatenId)
    {
        $kecamatan = Kecamatan::where('id_kabupaten_kota', $kabupatenId)->get();
        return response()->json($kecamatan);
    }

    public function getDesa($kecamatanId)
    {
        $desa = DesaKelurahan::where('id_kecamatan', $kecamatanId)->get();
        return response()->json($desa);
    }

    public function alamatSaya(Request $request)
    {
        $user = $request->user(); // pastikan ini Pembeli
        if (!$user instanceof Pembeli) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alamats = $user->alamats; // akan pakai relasi di model
        return response()->json($alamats);
    }

    public function setDefault($id)
    {
        $user = auth()->user();

        // Reset semua alamat user jadi bukan default
        $user->alamat()->update(['is_default' => false]);

        // Set alamat yang dipilih jadi default
        $alamat = $user->alamat()->where('ID_ALAMAT', $id)->first();
        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        $alamat->is_default = true;
        $alamat->save();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dijadikan default',
            'data' => $alamat,
        ]);
    }
}
