<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPenitipanBarang;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiPenitipanApiController extends Controller
{
    public function cetakNota(TransaksiPenitipanBarang $transaksi)
    {
        $pdf = Pdf::loadView('pdf.nota_penitipan', compact('transaksi'));
        return $pdf->download('Nota_Penitipan_' . $transaksi->ID_TRANSAKSI_PENITIPAN . '.pdf');
    }
}
