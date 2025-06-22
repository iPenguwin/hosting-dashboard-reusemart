<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Menampilkan semua barang yang tersedia.
     */
    public function index()
    {
        $data = Barang::with('kategoribarang')
            ->where('STATUS_BARANG', 'Tersedia')
            ->get()
            ->map(function ($barang) {
                $images = $barang->FOTO_BARANG && is_array($barang->FOTO_BARANG)
                    ? collect($barang->FOTO_BARANG)
                          ->map(fn($file) => asset('storage/' . $file))
                          ->all()
                    : [asset('images/default.jpg')];

                return [
                    'id'       => $barang->ID_BARANG,
                    'name'     => $barang->NAMA_BARANG,
                    'price'    => 'Rp' . number_format($barang->HARGA_BARANG, 0, ',', '.'),
                    'category' => $barang->kategoribarang->NAMA_KATEGORI ?? '-',
                    'image'    => $images[0] ?? asset('images/default.jpg'),
                    'images'   => $images,
                ];
            });

        return response()->json($data);
    }

    /**
     * Menampilkan semua barang yang berstatus "Tidak Terjual".
     */
    public function forRequest()
    {
        $data = Barang::with('kategoribarang')
            ->where('STATUS_BARANG', 'Tidak Terjual')
            ->get()
            ->map(function ($barang) {
                $images = $barang->FOTO_BARANG && is_array($barang->FOTO_BARANG)
                    ? collect($barang->FOTO_BARANG)
                          ->map(fn($file) => asset('storage/' . $file))
                          ->all()
                    : [asset('images/default.jpg')];

                return [
                    'id'       => $barang->ID_BARANG,
                    'name'     => $barang->NAMA_BARANG,
                    'price'    => 'Rp' . number_format($barang->HARGA_BARANG, 0, ',', '.'),
                    'category' => $barang->kategoribarang->NAMA_KATEGORI ?? '-',
                    'image'    => $images[0] ?? asset('images/default.jpg'),
                    'images'   => $images,
                ];
            });

        return response()->json($data);
    }

    /**
     * Menampilkan detail barang berdasarkan ID,
     * sekaligus menghitung rata-rata `penitip_rating` sebagai:
     *   (jumlah RATING semua barang milik penitip yang STATUS_BARANG != "Tersedia") ÷ (jumlah barang milik penitip dengan STATUS_BARANG != "Tersedia")
     */
    public function show($id)
    {
        // Cari barang beserta relasi kategoribarang dan penitip
        $barang = Barang::with(['kategoribarang', 'penitip'])->findOrFail($id);

        // Ambil ID_PENITIP dari barang ini
        $penitipId = $barang->ID_PENITIP;

        // 1) Hitung total RATING (sum) untuk semua barang milik penitip ini yang STATUS_BARANG != "Tersedia"
        $sumRating = Barang::where('ID_PENITIP', $penitipId)
            ->where('STATUS_BARANG', '!=', 'Tersedia')
            ->whereNotNull('RATING')
            ->sum('RATING');

        // 2) Hitung jumlah barang milik penitip ini yang STATUS_BARANG != "Tersedia" dan sudah punya RATING
        $countBarang = Barang::where('ID_PENITIP', $penitipId)
            ->where('STATUS_BARANG', '!=', 'Tersedia')
            ->whereNotNull('RATING')
            ->count();

        // 3) Jika ada minimal satu, rata-ratakan; jika tidak, hasilnya 0
        $avgRating = ($countBarang > 0)
            ? round($sumRating / $countBarang, 1)
            : 0;

        return response()->json([
            'id'             => $barang->ID_BARANG,
            'name'           => $barang->NAMA_BARANG,
            'price'          => 'Rp. ' . number_format($barang->HARGA_BARANG, 0, ',', '.'),
            'category'       => optional($barang->kategoribarang)->NAMA_KATEGORI,
            'image'          => $barang->getFotoBarangUrlAttribute() ?? null,
            'images'         => $barang->getFotoBarangUrlsAttribute(),
            'garansi'        => $barang->GARANSI?->format('d-m-Y') ?? '-',
            'berat'          => $barang->BERAT,
            'deskripsi'      => $barang->DESKRIPSI,
            'penitip_name'   => optional($barang->penitip)->NAMA_PENITIP ?? '-',
            'penitip_since'  => optional($barang->penitip)->created_at?->format('Y-m-d') ?? '—',
            'penitip_rating' => $avgRating,
            'rating'         => $barang->RATING !== null ? (int)$barang->RATING : 0,
            'status'         => $barang->STATUS_BARANG,
        ]);
    }

    /**
     * Menyimpan rating untuk satu barang oleh pembeli,
     * kemudian recalculate rata-rata rating penitip.
     */
    public function rate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Rating harus berupa integer antara 1 sampai 5.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (!isset($user->ID_PEMBELI)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Anda bukan pembeli.',
            ], 403);
        }

        // 1) Temukan barang, simpan rating-nya
        $barang = Barang::findOrFail($id);
        $barang->RATING = $request->rating;
        $barang->save();

        // 2) Hitung rata-rata RATING semua barang milik penitip ini (status apapun)
        $penitipId = $barang->ID_PENITIP;

        $query = Barang::where('ID_PENITIP', $penitipId)
                    ->whereNotNull('RATING')
                    ->where('STATUS_BARANG', '!=', 'Tersedia');

        $sumRating  = $query->sum('RATING');
        $countRated = $query->count();

        $avgRating = $countRated > 0
            ? round($sumRating / $countRated, 1)
            : 0;

        // 3) Simpan rata-rata itu ke kolom RATING_PENITIP
        $penitip = $barang->penitip;       // relasi belongsTo Penitip::class
        $penitip->RATING_PENITIP = $avgRating;
        $penitip->save();

        // 4) Kembalikan response termasuk rating baru penitip
        return response()->json([
            'success' => true,
            'message' => "Rating barang dan penitip berhasil diperbarui.",
            'data'    => [
                'barang_id'       => $barang->ID_BARANG,
                'barang_rating'   => $barang->RATING,
                'penitip_id'      => $penitip->ID_PENITIP,
                'penitip_rating'  => $penitip->RATING_PENITIP,
            ],
        ]);
    }

    public function myBarangs(Request $request)
    {
        $penitipId = $request->user()->ID_PENITIP;

        $barangs = Barang::with('kategoribarang')
            ->where('ID_PENITIP', $penitipId)
            ->orderBy('TGL_MASUK', 'desc')
            ->get()
            ->map(fn($b) => [
                'id'       => $b->ID_BARANG,
                'nama'     => $b->NAMA_BARANG,
                'kategori' => $b->kategoribarang->NAMA_KATEGORI ?? '-',
                'harga'    => $b->HARGA_BARANG,
                'tgl_masuk'=> $b->TGL_MASUK->format('Y-m-d'),
                'tgl_keluar'=> $b->TGL_KELUAR?->format('Y-m-d'),
                'status'   => $b->STATUS_BARANG,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $barangs,
        ]);
    }
}
