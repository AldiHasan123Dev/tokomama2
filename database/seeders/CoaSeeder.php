<?php

namespace Database\Seeders;

use App\Models\Coa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'no_akun' => '1.1',
                'nama_akun' => 'Aktiva Lancar',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.1',
                'nama_akun' => 'Kas',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.1.1',
                'nama_akun' => 'Kas Fiskal',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.2',
                'nama_akun' => 'Bank',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.2.1',
                'nama_akun' => 'Bank Mandiri 1400045006005',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.2.2',
                'nama_akun' => 'Bank OCBC 556800001738',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.3',
                'nama_akun' => 'Piutang Usaha ( Sudah Terbit Invoice )',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.3.1',
                'nama_akun' => 'Piutang Usaha Lain-lain',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.4',
                'nama_akun' => 'Estimasi Piutang Usaha ( Belum Terbit Invoice)',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.5',
                'nama_akun' => 'PPN Masukan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.6',
                'nama_akun' => 'PPN Keluaran',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.7',
                'nama_akun' => 'PPH Pasal 25 Di Bayar Di Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.8',
                'nama_akun' => 'Penundaan Pengkreditan Faktur Pajak Masukan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.9',
                'nama_akun' => 'Biaya Di Bayar DI Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.9.1',
                'nama_akun' => 'Asuransi Di Bayar Di Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.1.9.2',
                'nama_akun' => 'Sewa Di Bayar DI Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.0',
                'nama_akun' => 'PPH Pasal 23 Di Bayar Di Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2',
                'nama_akun' => 'Harta Tetap & Harta Tidak Berwujud',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1',
                'nama_akun' => 'Harta Tetap',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.1',
                'nama_akun' => 'Telephone',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.1.1',
                'nama_akun' => 'Akumulasi Penyusutan Telephone',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.2',
                'nama_akun' => 'Komputer & Hardware',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.2.1',
                'nama_akun' => 'Akumulasi Penyusutan Komputer & Hardware',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.3',
                'nama_akun' => 'Office Equipment',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.3.1',
                'nama_akun' => 'Akumulasi Penyusutan Office Equipment',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.4',
                'nama_akun' => 'Kendaraan / Vehicle',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.2.1.4.1',
                'nama_akun' => 'Akumulasi Penyusutan Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.3',
                'nama_akun' => 'Biaya Talangan (Di Tagihkan Ke Customer)',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.4',
                'nama_akun' => 'Uang Muka / Pembelian',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.5',
                'nama_akun' => 'Piutang Sementara',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '1.6',
                'nama_akun' => 'Persediaan Barang / Stock',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1',
                'nama_akun' => 'Kewajiban Lancar',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.1',
                'nama_akun' => 'Hutang Usaha (Sudah Terbit Invoice)',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.1.1',
                'nama_akun' => 'Hutang Biaya Ekspedisi',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.1.2',
                'nama_akun' => 'Hutang Biaya Trucking',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.1.3',
                'nama_akun' => 'Hutang Biaya Pembelian Barang',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.3',
                'nama_akun' => 'Hutang Lain - Lain',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.3.1',
                'nama_akun' => 'Hutang Biaya Sewa',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.4',
                'nama_akun' => 'Hutang Pajak',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.4.1',
                'nama_akun' => 'Hutang PPN',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.4.2',
                'nama_akun' => 'Hutang PPH 25',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.4.3',
                'nama_akun' => 'Hutang PPH 29',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.4.4',
                'nama_akun' => 'Hutang PPH 23',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.5',
                'nama_akun' => 'Hutang Pembelian Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.6',
                'nama_akun' => 'Uang Muka',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '2.1.7',
                'nama_akun' => 'Hutang Biaya Komisi Marketing',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '3.1',
                'nama_akun' => 'Modal',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '3.2',
                'nama_akun' => 'Laba / Rugi Di Tahan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '3.3',
                'nama_akun' => 'Laba / Rugi Tahun Berjalan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '5.1',
                'nama_akun' => 'Pendapatan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '5.2',
                'nama_akun' => 'Pendapatan Bunga',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '5.3',
                'nama_akun' => 'Pendapatan Lain - Lain & Lebih Bayar Tagihan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '5.3.1',
                'nama_akun' => 'Pendapatan Forklift',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1x',
                'nama_akun' => 'Biaya Operasional Perusahaan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1.1',
                'nama_akun' => 'Biaya Gaji',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1.2',
                'nama_akun' => 'Biaya Penyusutan Telephone',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1.3',
                'nama_akun' => 'Biaya Penyusutan Komputer & Hardware',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1.4',
                'nama_akun' => 'Biaya Penyusutan Office Equipment',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.1.5',
                'nama_akun' => 'Biaya Penyusutan Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.2',
                'nama_akun' => 'Biaya Operasional Trading',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.2.1',
                'nama_akun' => 'Biaya Operasional Trading Bulan Berjalan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.2.2',
                'nama_akun' => 'Biaya Operasional Trading',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.3',
                'nama_akun' => 'Biaya Administrasi dan Umum',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.4',
                'nama_akun' => 'Biaya Transport',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.5',
                'nama_akun' => 'Biaya Sparepart Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.6',
                'nama_akun' => 'Biaya ATK & Perlengkapan Kantor',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.7',
                'nama_akun' => 'Biaya Admin Bank',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.8',
                'nama_akun' => 'Biaya Klaim Customer',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.9',
                'nama_akun' => 'Biaya Parcel',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.10',
                'nama_akun' => 'Biaya Pajak',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.11',
                'nama_akun' => 'Biaya Asuransi Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.12',
                'nama_akun' => 'Biaya Sewa Gedung',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.13',
                'nama_akun' => 'Bunga Pinjaman',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.14',
                'nama_akun' => 'Biaya Sparepart Forklift',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.15',
                'nama_akun' => 'Potongan Penjualan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.16',
                'nama_akun' => 'Biaya Pajak Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.17',
                'nama_akun' => 'Biaya Konsumsi Karyawan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.18',
                'nama_akun' => 'Biaya Balik Nama Kendaraan',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.19',
                'nama_akun' => 'Biaya Leasing',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '6.20',
                'nama_akun' => 'Biaya Perbaikan Kantor',
                'status' => 'non-aktif'
            ],
            [
                'no_akun' => '7.1',
                'nama_akun' => 'Pembulatan',
                'status' => 'non-aktif'
            ],
        ];

        Coa::insert($data);
    }
}
