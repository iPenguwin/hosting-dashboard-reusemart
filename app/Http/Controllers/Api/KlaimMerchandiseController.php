<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KlaimMerchandise;
use App\Models\Merchandise;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KlaimMerchandiseController extends Controller
{
    /**
     * Fetch claim history for a specific buyer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ID_PEMBELI' => 'required|exists:pembelis,ID_PEMBELI',
            ]);

            // Fetch claim history with related merchandise data
            $claims = KlaimMerchandise::where('ID_PEMBELI', $validated['ID_PEMBELI'])
                ->with('merchandise') // Eager load the merchandise relationship
                ->get();

            $response = $claims->map(function ($claim) {
                return [
                    'ID_KLAIM' => $claim->ID_KLAIM,
                    'ID_MERCHANDISE' => $claim->ID_MERCHANDISE,
                    'ID_PEMBELI' => $claim->ID_PEMBELI,
                    'TGL_KLAIM' => $claim->TGL_KLAIM,
                    'TGL_PENGAMBILAN' => $claim->TGL_PENGAMBILAN,
                    'merchandise' => $claim->merchandise ? [
                        'ID_MERCHANDISE' => $claim->merchandise->ID_MERCHANDISE,
                        'NAMA_MERCHANDISE' => $claim->merchandise->NAMA_MERCHANDISE,
                        'POIN_DIBUTUHKAN' => $claim->merchandise->POIN_DIBUTUHKAN,
                        'JUMLAH' => $claim->merchandise->JUMLAH,
                        'GAMBAR' => $claim->merchandise->GAMBAR,
                    ] : null,
                ];
            });

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a new merchandise claim.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ID_MERCHANDISE' => 'required|exists:merchandises,ID_MERCHANDISE',
            'ID_PEMBELI' => 'required|exists:pembelis,ID_PEMBELI',
            'TGL_KLAIM' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $merchandise = Merchandise::findOrFail($validated['ID_MERCHANDISE']);
            if ($merchandise->JUMLAH <= 0) {
                throw new \Exception('This merchandise is out of stock');
            }

            $pembeli = Pembeli::findOrFail($validated['ID_PEMBELI']);
            if ($pembeli->POINT_LOYALITAS_PEMBELI < $merchandise->POIN_DIBUTUHKAN) {
                throw new \Exception('Not enough points to claim this merchandise');
            }

            $pembeli->POINT_LOYALITAS_PEMBELI -= $merchandise->POIN_DIBUTUHKAN;
            $pembeli->save();

            $klaim = KlaimMerchandise::create([
                'ID_MERCHANDISE' => $validated['ID_MERCHANDISE'],
                'ID_PEMBELI' => $validated['ID_PEMBELI'],
                'TGL_KLAIM' => $validated['TGL_KLAIM'],
            ]);

            $response = [
                'ID_KLAIM' => $klaim->ID_KLAIM,
                'ID_MERCHANDISE' => $klaim->ID_MERCHANDISE,
                'ID_PEMBELI' => $klaim->ID_PEMBELI,
                'TGL_KLAIM' => $klaim->TGL_KLAIM,
                'TGL_PENGAMBILAN' => $klaim->TGL_PENGAMBILAN,
                'merchandise' => [
                    'ID_MERCHANDISE' => $merchandise->ID_MERCHANDISE,
                    'NAMA_MERCHANDISE' => $merchandise->NAMA_MERCHANDISE,
                    'POIN_DIBUTUHKAN' => $merchandise->POIN_DIBUTUHKAN,
                    'JUMLAH' => $merchandise->JUMLAH,
                    'GAMBAR' => $merchandise->GAMBAR,
                ],
            ];

            DB::commit();

            return response()->json($response, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
