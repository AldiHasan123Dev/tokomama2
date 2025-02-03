<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id_barang' => rand(1, 10), // Sesuaikan dengan ID di tabel barang
                'id_supplier' => rand(1, 5), // Sesuaikan dengan ID di tabel supplier
                'tgl_beli' => now()->subDays(rand(10, 100)),
                'tgl_jual' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                'is_active' => rand(0, 1),
                'vol_bm' => rand(50, 500),
                'vol_bk' => rand(20, 400),
                'sisa' => rand(10, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('stocks')->insert($data);
    }
}
