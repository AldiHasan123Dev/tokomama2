<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_satuan' => 'KG'
            ],
            [
                'nama_satuan' => 'DOS'
            ],
            [
                'nama_satuan' => 'CRT'
            ],
            [
                'nama_satuan' => 'BOX'
            ],
            [
                'nama_satuan' => 'ZAK'
            ],
            [
                'nama_satuan' => 'CTN'
            ],
            [
                'nama_satuan' => 'PCS'
            ],
            [
                'nama_satuan' => 'KWT'
            ],
            [
                'nama_satuan' => 'BALL'
            ],
        ];

        Satuan::insert($data);
    }
}
