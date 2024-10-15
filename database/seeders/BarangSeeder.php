<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode_objek' => 'AQ5',
                'nama' => "AQUA 1500 ML",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'AQUA 1500'
            ],

            [
                'kode_objek' => 'AQ4',
                'nama' => "AQUA 220 ML",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'AQUA 220'
            ],

            [
                'kode_objek' => 'AQ1',
                'nama' => "AQUA 240 ML",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'AQUA 240'
            ],

            [
                'kode_objek' => 'AQ2',
                'nama' => "AQUA 330 ML",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'AQUA 330'
            ],

            [
                'kode_objek' => 'AQ3',
                'nama' => "AQUA 600 ML",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'AQUA 600'
            ],

            [
                'kode_objek' => 'BERAS99-2',
                'nama' => "BERAS 99 @10 KG",
                'value' => "10",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS 99 @10'
            ],

            [
                'kode_objek' => 'BERAS99-3',
                'nama' => "BERAS 99 @20 KG",
                'value' => "20",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS 99 @20'
            ],

            [
                'kode_objek' => 'BERAS99-4',
                'nama' => "BERAS 99 @25 KG",
                'value' => "25",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS 99 @25'
            ],

            [
                'kode_objek' => 'BERAS99-1',
                'nama' => "BERAS 99 @5 KG",
                'value' => "5",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS 99 @5'
            ],

            [
                'kode_objek' => 'BERASMAWAR1',
                'nama' => "BERAS MAWAR @10 KG",
                'value' => "10",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS MAWAR @10'
            ],

            [
                'kode_objek' => 'BERASMAWAR2',
                'nama' => "BERAS MAWAR @20 KG",
                'value' => "20",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS MAWAR @20'
            ],

            [
                'kode_objek' => 'BERASMAWAR3',
                'nama' => "BERAS MAWAR @40 KG",
                'value' => "40",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS MAWAR @40'
            ],

            [
                'kode_objek' => 'BERASRAJA1',
                'nama' => "BERAS RAJA ANGSA @10 KG",
                'value' => "10",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS RAJA ANGSA @10'
            ],

            [
                'kode_objek' => 'BERASRAJA2',
                'nama' => "BERAS RAJA ANGSA @20 KG",
                'value' => "20",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS RAJA ANGSA @20'
            ],

            [
                'kode_objek' => 'BERASRAJA3',
                'nama' => "BERAS RAJA ANGSA @25 KG",
                'value' => "25",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'BERAS RAJA ANGSA @25'
            ],

            [
                'kode_objek' => 'BIHUN001',
                'nama' => "BIHUN JAGUNG PADAMU",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'BIHUN JAGUNG PADAMU'
            ],

            [
                'kode_objek' => 'GULAKBA',
                'nama' => "GULA KBA @50 KG",
                'value' => "50",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'GULA KBA @50'
            ],

            [
                'kode_objek' => 'GULAKTM',
                'nama' => "GULA KTM @50 KG",
                'value' => "50",
                // 'status_ppn' => 'Tidak',
                'created_at' => now(),
                'nama_singkat' => 'GULA KTM @50'
            ],

            [
                'kode_objek' => 'MIE001',
                'nama' => "MIE ATOM BULAN",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE ATOM BULAN'
            ],

            [
                'kode_objek' => 'MIE-1',
                'nama' => "MIE GULUNG KUDA MENJANGAN ISI 12 BKS",
                'value' => "12",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE GULUNG KUDA MENJANGAN ISI 12'
            ],

            [
                'kode_objek' => 'SDP02',
                'nama' => "MIE SEDAP GORENG 90 GR",
                'value' => "90",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE SEDAP GORENG 90'
            ],

            [
                'kode_objek' => 'MIE-2',
                'nama' => "MIE TELOR ASLI KUDA MENJANGAN ISI 20 BKS ( KECIL )",
                'value' => "20",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE TELOR ASLI KUDA MENJANGAN KECIL'
            ],

            [
                'kode_objek' => 'MIE-3',
                'nama' => "MIE TELOR ASLI KUDA MENJANGAN ISI 20 BKS ( LEBAR )",
                'value' => "20",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE TELOR ASLI KUDA MENJANGAN LEBAR'
            ],

            [
                'kode_objek' => 'MIE-4',
                'nama' => "MIE TELOR KERIITNG KUDA MENJANGAN ISI 20 BKS",
                'value' => "20",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'MIE TELOR KERIITNG KUDA MENJANGAN ISI 20'
            ],

            [
                'kode_objek' => 'TAS',
                'nama' => "TAS SPOUNBOND",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'TAS SPOUNBOND'
            ],

            [
                'kode_objek' => 'TPG03',
                'nama' => "TEPUNG MILA @1 KG",
                'value' => "1",
                // 'status_ppn' => 'Ya',
                'created_at' => now(),
                'nama_singkat' => 'TEPUNG MILA @1'
            ],
        ];

        Barang::insert($data);
    }
}
