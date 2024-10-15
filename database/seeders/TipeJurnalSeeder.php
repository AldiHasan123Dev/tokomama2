<?php

namespace Database\Seeders;

use App\Models\TipeJurnal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipeJurnalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['tipe_jurnal'=>'JNL','nama_tipe'=>'Jurnal','no'=>0],
            ['tipe_jurnal'=>'BKK','nama_tipe'=>'Kas Keluar','no'=>0],
            ['tipe_jurnal'=>'BKM','nama_tipe'=>'Kas Masuk','no'=>0],
            ['tipe_jurnal'=>'BBK','nama_tipe'=>'Bank Keluar','no'=>0],
            ['tipe_jurnal'=>'BBM','nama_tipe'=>'Bank Masuk','no'=>0],
        ];

        TipeJurnal::insert($data);
    }
}
