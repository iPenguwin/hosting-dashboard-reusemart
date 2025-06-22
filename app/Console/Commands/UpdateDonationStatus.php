<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use Carbon\Carbon;

class UpdateDonationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barang:update-donation-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status barang yang tidak diambil >7 hari setelah TGL_KELUAR menjadi Barang Untuk Donasi';

    public function handle()
    {
        $cutoff = Carbon::today()->subDays(7)->toDateString();

        $updated = Barang::where('TGL_KELUAR', '<', $cutoff)
            ->where('STATUS_BARANG', '!=', 'Barang Untuk Donasi')
            ->update(['STATUS_BARANG' => 'Barang Untuk Donasi']);

        $this->info("Updated {$updated} barang(s) to \"Barang Untuk Donasi\".");
    }
}