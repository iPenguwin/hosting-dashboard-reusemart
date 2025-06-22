<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel; // Alias to avoid conflict with Illuminate\Http\Request
use App\Models\Organisasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated organization.
     */
    public function index(Request $illuminateRequest)
    {
        $user = $illuminateRequest->user();

        if (!$user || !$user instanceof Organisasi) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Fetch requests for the authenticated user's organization
        $requests = RequestModel::where('ID_ORGANISASI', $user->ID_ORGANISASI)
            ->get()
            ->map(function ($request) {
                return [
                    'ID_REQUEST' => $request->ID_REQUEST,
                    'NAMA_BARANG_REQUEST' => $request->NAMA_BARANG_REQUEST,
                    'DESKRIPSI_REQUEST' => $request->DESKRIPSI_REQUEST,
                    'STATUS_REQUEST' => $request->STATUS_REQUEST,
                    'created_at' => $request->created_at->toDateTimeString(),
                    'updated_at' => $request->updated_at->toDateTimeString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $requests]);
    }

    /**
     * Store a newly created resource in storage for the authenticated organization.
     */
    public function store(Request $illuminateRequest)
    {
        $user = $illuminateRequest->user();

        if (!$user || !$user instanceof Organisasi) {
            return response()->json(['message' => 'Unauthorized or not an organization'], 401);
        }

        $validator = Validator::make($illuminateRequest->all(), [
            'NAMA_BARANG_REQUEST' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user) {
                    $existingRequest = RequestModel::where('NAMA_BARANG_REQUEST', $value)
                        ->where('ID_ORGANISASI', $user->ID_ORGANISASI)
                        ->where('STATUS_REQUEST', 'Menunggu')
                        ->exists();
                    if ($existingRequest) {
                        $fail("Barang '$value' sudah ada dalam antrian request organisasi Anda. Silakan pilih nama barang lainnya atau tunggu hingga request sebelumnya diproses.");
                    }
                },
            ],
            'DESKRIPSI_REQUEST' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['ID_ORGANISASI'] = $user->ID_ORGANISASI;
        $data['STATUS_REQUEST'] = 'Menunggu'; // Default status

        $requestModel = RequestModel::create($data);

        return response()->json(['success' => true, 'message' => 'Request berhasil dibuat', 'data' => $requestModel], 201);
    }

    /**
     * Display the specified resource if it belongs to the authenticated organization.
     */
    public function show(Request $illuminateRequest, RequestModel $requestModel) // Route model binding
    {
        $user = $illuminateRequest->user();

        if (!$user || !$user instanceof Organisasi || $requestModel->ID_ORGANISASI !== $user->ID_ORGANISASI) {
            return response()->json(['message' => 'Not Found or Unauthorized'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'ID_REQUEST' => $requestModel->ID_REQUEST,
            'NAMA_BARANG_REQUEST' => $requestModel->NAMA_BARANG_REQUEST,
            'DESKRIPSI_REQUEST' => $requestModel->DESKRIPSI_REQUEST,
            'STATUS_REQUEST' => $requestModel->STATUS_REQUEST,
            'created_at' => $requestModel->created_at->toDateTimeString(),
            'updated_at' => $requestModel->updated_at->toDateTimeString(),
        ]]);
    }

    /**
     * Update the specified resource in storage if it belongs to the authenticated organization and status is 'Menunggu'.
     */
    public function update(Request $illuminateRequest, RequestModel $requestModel) // Route model binding
    {
        $user = $illuminateRequest->user();

        if (!$user || !$user instanceof Organisasi || $requestModel->ID_ORGANISASI !== $user->ID_ORGANISASI) {
            return response()->json(['message' => 'Not Found or Unauthorized'], 404);
        }

        if ($requestModel->STATUS_REQUEST !== 'Menunggu') {
            return response()->json(['success' => false, 'message' => 'Request tidak dapat diubah karena sudah diproses.'], 403);
        }

        $validator = Validator::make($illuminateRequest->all(), [
            'NAMA_BARANG_REQUEST' => [
                'sometimes', // only validate if present
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user, $requestModel) {
                    if (strtolower($value) !== strtolower($requestModel->NAMA_BARANG_REQUEST)) { // Only check if name is changed
                        $existingRequest = RequestModel::where('NAMA_BARANG_REQUEST', $value)
                            ->where('ID_ORGANISASI', $user->ID_ORGANISASI)
                            ->where('STATUS_REQUEST', 'Menunggu')
                            ->where('ID_REQUEST', '!=', $requestModel->ID_REQUEST) // Exclude self
                            ->exists();
                        if ($existingRequest) {
                            $fail("Barang '$value' sudah ada dalam antrian request organisasi Anda.");
                        }
                    }
                },
            ],
            'DESKRIPSI_REQUEST' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $requestModel->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Request berhasil diperbarui', 'data' => $requestModel->fresh()]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user instanceof Organisasi) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'NAMA_ORGANISASI' => $user->NAMA_ORGANISASI,
                'EMAIL_ORGANISASI' => $user->EMAIL_ORGANISASI,
                'NO_TELP_ORGANISASI' => $user->NO_TELP_ORGANISASI,
                'ALAMAT_ORGANISASI' => $user->ALAMAT_ORGANISASI,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage if it belongs to the authenticated organization and status is 'Menunggu'.
     */
    public function destroy(Request $illuminateRequest, RequestModel $requestModel) // Route model binding
    {
        $user = $illuminateRequest->user();

        if (!$user || !$user instanceof Organisasi || $requestModel->ID_ORGANISASI !== $user->ID_ORGANISASI) {
            return response()->json(['message' => 'Not Found or Unauthorized'], 404);
        }

        if ($requestModel->STATUS_REQUEST !== 'Menunggu') {
            return response()->json(['success' => false, 'message' => 'Request tidak dapat dihapus karena sudah diproses.'], 403);
        }

        $requestModel->delete();

        return response()->json(['success' => true, 'message' => 'Request berhasil dihapus'], 200); // Or 204 with no content
    }
}

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Models\Request as RequestModel;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

// class RequestController extends Controller
// {
//     public function __construct()
//     {
//         // Pastikan user sudah login dan gunakan policy otomatis
//         $this->middleware('auth:sanctum');
//         $this->authorizeResource(RequestModel::class, 'request', [
//             'except' => ['store'], // store harus manual authorize karena belum ada model instance
//         ]);
//     }

//     // public function index(Request $request)
//     // {
//     //     $user = $request->user();

//     //     if ($user instanceof \App\Models\Organisasi) {
//     //         $requests = RequestModel::where('ID_ORGANISASI', $user->ID_ORGANISASI)->get();
//     //     } else {
//     //         $requests = RequestModel::all();
//     //     }

//     //     return response()->json([
//     //         'success' => true,
//     //         'message' => 'Daftar request',
//     //         'data' => $requests
//     //     ]);
//     // }

//     public function organisasiRequests()
//     {
//         // Ambil organisasi yang sedang login
//         $organisasiId = Auth::user()->ID_ORGANISASI; // Pastikan ID_ORGANISASI tersedia di model User

//         // Ambil hanya request dengan status 'Menunggu' untuk organisasi yang login
//         $requests = Request::where('ID_ORGANISASI', $organisasiId)
//             ->where('STATUS_REQUEST', 'Menunggu')
//             ->get()
//             ->map(function ($request) {
//                 return [
//                     'ID_REQUEST' => $request->ID_REQUEST,
//                     'ID_BARANG' => $request->ID_BARANG,
//                     'DESKRIPSI_REQUEST' => $request->DESKRIPSI_REQUEST,
//                     'STATUS_REQUEST' => $request->STATUS_REQUEST,
//                 ];
//             });

//         return response()->json($requests);
//     }

//     public function index(Request $request)
//     {
//         $user = $request->user();

//         if ($user instanceof \App\Models\Organisasi) {
//             $requests = RequestModel::with('barang')
//                 ->where('ID_ORGANISASI', $user->ID_ORGANISASI)
//                 ->get();
//         } else {
//             $requests = RequestModel::with('barang')->get();
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Daftar request',
//             'data' => $requests
//         ]);
//     }

//     public function store(Request $request)
//     {
//         $user = $request->user();

//         if (!$user->can('create', RequestModel::class)) {
//             return response()->json(['message' => 'Unauthorized'], 403);
//         }

//         $rules = [
//             'ID_BARANG' => 'required|exists:barangs,ID_BARANG',
//             'DESKRIPSI_REQUEST' => 'required|string',
//             'STATUS_REQUEST' => 'nullable|string',
//         ];

//         if ($user instanceof \App\Models\Pegawai) {
//             $rules['ID_ORGANISASI'] = 'required|exists:organisasis,ID_ORGANISASI';
//         }

//         $validator = Validator::make($request->all(), $rules);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validasi gagal',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         $data = $validator->validated();

//         if ($user instanceof \App\Models\Organisasi) {
//             $data['ID_ORGANISASI'] = $user->ID_ORGANISASI;
//         }

//         $requestModel = RequestModel::create($data);

//         return response()->json([
//             'success' => true,
//             'message' => 'Request berhasil dibuat',
//             'data' => $requestModel
//         ], 201);
//     }

//     public function show(Request $request, RequestModel $requestModel)
//     {
//         $this->authorize('view', $requestModel);

//         return response()->json([
//             'success' => true,
//             'message' => 'Detail request',
//             'data' => $requestModel
//         ]);
//     }

//     public function update(Request $request, RequestModel $requestModel)
//     {
//         $this->authorize('update', $requestModel);

//         $rules = [
//             'ID_ORGANISASI' => 'sometimes|exists:organisasis,ID_ORGANISASI',
//             'ID_BARANG' => 'sometimes|exists:barangs,ID_BARANG',
//             'DESKRIPSI_REQUEST' => 'sometimes|string',
//             'STATUS_REQUEST' => 'sometimes|string',
//         ];

//         $validator = Validator::make($request->all(), $rules);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validasi gagal',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         $requestModel->update($validator->validated());

//         return response()->json([
//             'success' => true,
//             'message' => 'Request berhasil diperbarui',
//             'data' => $requestModel
//         ]);
//     }

//     public function destroy(Request $request, RequestModel $requestModel)
//     {
//         $user = $request->user();
//         \Log::info('Delete request attempt', [
//             'user' => $user,
//             'request_id' => $requestModel->ID_REQUEST,
//             'user_type' => get_class($user),
//             'can_delete' => $user->can('delete', $requestModel),
//         ]);

//         $this->authorize('delete', $requestModel);

//         $requestModel->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Request berhasil dihapus',
//             'data' => null
//         ], 204);
//     }

//     public function updateStatus($id, Request $request)
//     {
//         $requestData = Request::findOrFail($id);
//         $requestData->STATUS_REQUEST = $request->input('status'); // Misalnya 'Ditolak' atau 'Dihapus'
//         $requestData->save();

//         return response()->json([
//             'success' => true,
//             'message' => 'Status request berhasil diperbarui',
//         ]);
//     }

// }
