<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangApiController extends Controller
{
    public function cetakNota(Barang $barang)
    {
        $pdf = Pdf::loadView('pdf.nota_barang', compact('barang'));
        return $pdf->download('Nota_Barang_' . $barang->KODE_BARANG . '.pdf');
    }
}
