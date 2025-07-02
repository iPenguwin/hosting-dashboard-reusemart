<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KabupatenKotaSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key constraints (PostgreSQL way)
        Schema::disableForeignKeyConstraints();

        // Truncate the table
        Kabupaten::truncate();

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Disable foreign key constraints (MySQL way)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table
        Kabupaten::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kabupatens = [
            ['id_provinsi' => 14, 'id_kabupaten_kota' => 210, 'nama_kabupaten_kota' => 'SLEMAN'],
            ['id_provinsi' => 14, 'id_kabupaten_kota' => 211, 'nama_kabupaten_kota' => 'BANTUL'],
            ['id_provinsi' => 14, 'id_kabupaten_kota' => 212, 'nama_kabupaten_kota' => 'YOGYAKARTA'],
            ['id_provinsi' => 14, 'id_kabupaten_kota' => 213, 'nama_kabupaten_kota' => 'GUNUNG KIDUL'],
            ['id_provinsi' => 14, 'id_kabupaten_kota' => 214, 'nama_kabupaten_kota' => 'KULON PROGO'],
        ];

        foreach ($kabupatens as $kabupaten) {
            Kabupaten::create($kabupaten);
        }
    }
}
