<?php

namespace Database\Seeders;

use App\Models\Ekspedisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EkspedisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'CITRA IRIAN KARYA NUSANTARA, PT',
                'pic' => 'ANDI PANGERAN',
                'alamat' => 'JL. BUDI UTOMO',
                'kota' => 'TIMIKA',
                'no_telp' => null,
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'DELTA MITRA, EKSPEDISI',
                'pic' => 'AINI, IBU',
                'alamat' => 'JL.SEMUT BARU, KOMP.PENGAMPON SQUARE H-8',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3574524',
                'fax' => '031 3536146',
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'EKSPEDISI PERMATA SAMUDERA',
                'pic' => 'EDDY, BPK.',
                'alamat' => 'JL. IKAN MUNGSING GANG 5 NO. 97',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3571531',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'JASA SURABAYA MANDIRI, PT',
                'pic' => 'INDRA, IBU ; IMA, IBU',
                'alamat' => 'JL. KALIANAK NO.51 O',
                'kota' => 'SURABAYA',
                'no_telp' => '031 7496037',
                'fax' => '031 7481748',
                'email' => 'stevenwinardi@hotmail.com',
                'created_at' => now()
            ],
            [
                'nama' => 'KI TRANS SURABAYA',
                'pic' => null,
                'alamat' => 'JL. KALIANGET NO.80, TJ. PERAK',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3281133',
                'fax' => '031-3294703',
                'email' => 'surabaya@kitransnet.com',
                'created_at' => now()
            ],
            [
                'nama' => 'LINTAS SAMUDERA JAYA, PT.',
                'pic' => 'DARWANTI, IBU',
                'alamat' => 'JL.RAYA BRIGJEN KATAMSO NO.6 WARU',
                'kota' => 'SIDOARJO',
                'no_telp' => '031 8670776; 70347676; 71700709',
                'fax' => '0318671576',
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'MERANTI MANDIRI, PT.',
                'pic' => 'NINIK, IBU.',
                'alamat' => 'JL.KREMBANGAN MAKAM NO.23',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3522887',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'PERMATA SAMUDERA JAYA, PT.',
                'pic' => null,
                'alamat' => 'JL. IKAN MUNGSING NO.89',
                'kota' => 'SURABAYA',
                'no_telp' => '031 571531 - 3532862',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'SAM BARU, PT.',
                'pic' => 'NANIK, IBU.',
                'alamat' => 'JL. KALIMAS BARU NO.71',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3291985',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'TIRTA MAS, PT.',
                'pic' => 'IKA, IBU.',
                'alamat' => 'JL. INDRAPURA BARU 351 C',
                'kota' => 'SURABAYA',
                'no_telp' => '031 8586180',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'TRI DELTA MUTIARA',
                'pic' => null,
                'alamat' => 'JL. KALIMAS BARU NO.89',
                'kota' => 'SURABAYA',
                'no_telp' => '031 3282707/3294287/3291627',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],
            [
                'nama' => 'EKSPEDISI RAS',
                'pic' => 'Dwi, Bpk',
                'alamat' => 'Jl. Kalianak 55 G',
                'kota' => 'SURABAYA',
                'no_telp' => '031 7495507',
                'fax' => null,
                'email' => null,
                'created_at' => now()
            ],

        ];

        Ekspedisi::insert($data);
    }
}
