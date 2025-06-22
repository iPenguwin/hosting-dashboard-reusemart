<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransaksiPembelianBarang;
use App\Models\Badge;
use App\Models\Komisi;
use App\Models\Penitip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComputeTopSeller extends Command
{
    protected $signature   = 'compute:topseller';
    protected $description = 'Hitung TOP SELLER bulan lalu, upsert badge, dan record bonus komisi';

    public function handle()
    {
        // 1) Periode bulan lalu
        $start = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $end   = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // 2) Hitung total per penitip & ambil satu sample transaksi terakhir
        $totals = TransaksiPembelianBarang::select(
                'barangs.ID_PENITIP',
                DB::raw('SUM(TOT_HARGA_PEMBELIAN) as total'),
                DB::raw('MAX(transaksi_pembelian_barangs.ID_TRANSAKSI_PEMBELIAN) as sample_txn_id')
            )
            ->join('barangs','transaksi_pembelian_barangs.ID_BARANG','=','barangs.ID_BARANG')
            ->whereBetween('TGL_PESAN_PEMBELIAN', [$start, $end])
            ->where('STATUS_TRANSAKSI','Selesai')
            ->groupBy('barangs.ID_PENITIP')
            ->orderByDesc('total')
            ->get();

        if ($totals->isEmpty()) {
            $this->info('Tidak ada transaksi berhasil di bulan lalu.');
            return;
        }

        // 3) Top Seller = yang paling besar total
        $top     = $totals->first();
        $penId   = $top->ID_PENITIP;
        $nominal = $top->total;
        $txnId   = $top->sample_txn_id;

        // 4) Upsert badge “Top Seller” untuk periode itu
        Badge::updateOrCreate(
            ['ID_PENITIP' => $penId, 'NAMA_BADGE' => 'Top Seller'],
            ['START_DATE' => $start, 'END_DATE'   => $end]
        );

        // 5) Hitung bonus 1%
        $bonus = round($nominal * 0.01);

        // 6) Berikan bonus poin ke akun penitip
        /** @var Penitip $penitip */
        $penitip = Penitip::find($penId);
        $penitip->increment('POINT_LOYALITAS_PENITIP', $bonus);

        // 7) Buat entry di tabel komisis
        Komisi::create([
            'JENIS_KOMISI'           => 'Penitip',
            'ID_PENITIP'             => $penId,
            'ID_PEGAWAI'             => null,
            'ID_TRANSAKSI_PEMBELIAN' => $txnId,
            'NOMINAL_KOMISI'         => $bonus,
        ]);

        $this->info("Top Seller: Penitip #{$penId}  — Total: Rp{$nominal}  — Bonus: Rp{$bonus} dimasukkan ke komisi (txn {$txnId}).");
    }
}
