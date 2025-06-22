<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MerchandiseController extends Controller
{
    /**
     * Display a listing of the merchandises.
     */
    public function index(): JsonResponse
    {
        try {
            $merchandises = Merchandise::all()->map(function ($merchandise) {
                $image = $merchandise->GAMBAR
                    ? asset('storage/' . $merchandise->GAMBAR)
                    : asset('images/default.jpg');

                return [
                    'ID_MERCHANDISE'     => $merchandise->ID_MERCHANDISE,
                    'NAMA_MERCHANDISE'   => $merchandise->NAMA_MERCHANDISE,
                    'POIN_DIBUTUHKAN'    => $merchandise->POIN_DIBUTUHKAN,
                    'JUMLAH'             => $merchandise->JUMLAH,
                    'GAMBAR'             => $image,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Merchandises retrieved successfully',
                'data' => $merchandises
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve merchandises',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created merchandise.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'NAMA_MERCHANDISE' => 'required|string|max:255',
            'POIN_DIBUTUHKAN' => 'required|integer|min:0',
            'JUMLAH' => 'required|integer|min:0',
            'GAMBAR' => 'nullable|string|max:255',
        ]);

        $merchandise = Merchandise::create($validated);
        return response()->json($merchandise, 201);
    }

    /**
     * Display the specified merchandise.
     */
    public function show($id): JsonResponse
    {
        $merchandise = Merchandise::findOrFail($id);
        return response()->json($merchandise);
    }

    /**
     * Update the specified merchandise.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $merchandise = Merchandise::findOrFail($id);

        $validated = $request->validate([
            'NAMA_MERCHANDISE' => 'sometimes|required|string|max:255',
            'POIN_DIBUTUHKAN' => 'sometimes|required|integer|min:0',
            'JUMLAH' => 'sometimes|required|integer|min:0',
            'GAMBAR' => 'nullable|string|max:255',
        ]);

        $merchandise->update($validated);
        return response()->json($merchandise);
    }

    /**
     * Remove the specified merchandise.
     */
    public function destroy($id): JsonResponse
    {
        $merchandise = Merchandise::findOrFail($id);
        $merchandise->delete();

        return response()->json(['message' => 'Merchandise deleted']);
    }
}
