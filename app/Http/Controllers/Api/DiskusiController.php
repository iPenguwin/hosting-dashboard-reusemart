<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Diskusi;
use App\Models\Barang;
use App\Models\Pembeli;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DiskusiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diskusis = Diskusi::with(['barang', 'pembeli', 'pegawai'])->get();

        return response()->json($diskusis);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kosongkan jika hanya digunakan untuk API
        return response()->json(['message' => 'Form create tidak diperlukan untuk API']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ID_PEMBELI' => 'required|exists:pembelis,ID_PEMBELI',
            'PERTANYAAN' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $diskusi = Diskusi::create([
            'ID_BARANG' => $id, // gunakan id dari parameter route
            'ID_PEMBELI' => $request->ID_PEMBELI,
            'PERTANYAAN' => $request->PERTANYAAN,
            'CREATE_AT' => Carbon::today(),
        ]);

        return response()->json(['message' => 'Diskusi berhasil dibuat', 'data' => $diskusi], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $diskusi = Diskusi::with(['barang', 'pembeli', 'pegawai'])->find($id);

        if (!$diskusi) {
            return response()->json(['message' => 'Diskusi tidak ditemukan'], 404);
        }

        return response()->json($diskusi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Kosongkan jika hanya digunakan untuk API
        return response()->json(['message' => 'Form edit tidak diperlukan untuk API']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $diskusi = Diskusi::find($id);

        if (!$diskusi) {
            return response()->json(['message' => 'Diskusi tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'PERTANYAAN' => 'sometimes|required|string|max:1000',
            'JAWABAN' => 'nullable|string',
            'ID_PEGAWAI' => 'nullable|exists:pegawais,ID_PEGAWAI',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $diskusi->update($request->only(['PERTANYAAN', 'JAWABAN', 'ID_PEGAWAI']));

        return response()->json(['message' => 'Diskusi berhasil diperbarui', 'data' => $diskusi]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diskusi = Diskusi::find($id);

        if (!$diskusi) {
            return response()->json(['message' => 'Diskusi tidak ditemukan'], 404);
        }

        $diskusi->delete();

        return response()->json(['message' => 'Diskusi berhasil dihapus']);
    }

    public function getDiskusi($id)
    {
        $diskusi = Diskusi::where('ID_BARANG', $id)
            ->with(['pembeli', 'jawaban']) // atau ambil langsung kolom jawaban
            ->get()
            ->map(function ($d) {
                return [
                    'ID_DISKUSI' => $d->id,
                    'PERTANYAAN' => $d->PERTANYAAN,
                    'CREATE_AT' => $d->created_at,
                    'JAWABAN' => $d->jawaban?->ISI_JAWABAN ?? null, // jika pakai relasi
                    'pembeli' => [
                        'NAMA_PEMBELI' => $d->pembeli->NAMA_PEMBELI ?? 'Pengguna',
                    ],
                ];
            });

        return response()->json($diskusi);
    }
}
