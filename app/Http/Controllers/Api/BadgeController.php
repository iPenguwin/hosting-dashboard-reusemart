<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\JsonResponse;

class BadgeController extends Controller
{
    /**
     * Ambil Top Seller terakhir (periode paling baru),
     * tanpa mem-filter berdasarkan tanggal hari ini.
     */
    public function current(): JsonResponse
    {
        // Ambil badge dengan START_DATE paling besar (periode paling baru)
        $badge = Badge::with('penitip')
            ->orderByDesc('START_DATE')
            ->first();

        if (! $badge) {
            // Belum ada badge sama sekali
            return response()->json([
                'success' => true,
                'data'    => [],
            ]);
        }

        // Bentuk response array (meski hanya satu elemen)
        $data = [
            [
                'penitip_id'   => $badge->ID_PENITIP,
                'penitip_name' => $badge->penitip->NAMA_PENITIP,
                'from'         => $badge->START_DATE,
                'to'           => $badge->END_DATE,
            ]
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
