<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use App\Models\Invoice;
use App\Models\Transaksi;
use App\Models\Coa;

class LaporanController extends Controller
{
    public function dataLHV(){
        $invoices = Invoice::selectRaw('
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        COUNT(DISTINCT i.invoice) as invoice_count,  
        SUM(
            CASE 
                WHEN b.status_ppn = "ya" THEN t.harga_beli * t.jumlah_beli * 1.11
                ELSE t.harga_beli * t.jumlah_beli
            END
        ) as total_hutang,
        GROUP_CONCAT(DISTINCT t.invoice_external) as invoice_externals') // Menggunakan GROUP_CONCAT
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
    ->join('barang as b', 't.id_barang', '=', 'b.id')
    ->where('i.tgl_invoice', '>', '2024-08-01')
    ->groupBy('year', 'month') // Mengelompokkan berdasarkan tahun dan bulan
    ->orderBy('year', 'asc')
    ->orderByRaw('MONTH(i.tgl_invoice) ASC')
    ->get();

// Ubah hasil `invoice_list` menjadi array
$invoices->map(function ($invoice) {
    $invoice->invoice_externals = explode(',', $invoice->invoice_externals);
    return $invoice;
});

// Gabungkan semua invoice_list menjadi satu array
$invoicelist = $invoices->flatMap->invoice_externals
    ->filter(function ($invoiceExternal) {
        return !empty($invoiceExternal); // Menghapus nilai kosong
    })
    ->unique()
    ->toArray();
// Menggunakan flatMap dan unique untuk mendapatkan list yang unik
// dd($invoicelist);
$jurnals = Jurnal::withTrashed() // Menyertakan data yang dihapus, namun kita akan filter dengan whereNull untuk yang belum dihapus
    ->selectRaw('DATE_FORMAT(j.tgl, "%M") as month, 
                 YEAR(j.tgl) as year, 
                 SUM(j.kredit) as total_lunas,
                 c.nama_akun')
    ->from('jurnal as j')
    ->join('coa as c', 'j.coa_id', '=', 'c.id')
    ->where('c.id', 5)
    ->whereIn('j.invoice_external', $invoicelist) // Filter berdasarkan invoice list
    ->whereNull('j.deleted_at') // Menyertakan hanya data yang tidak dihapus
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(j.tgl)')
    ->get();


// Menyusun hasil dari invoice berdasarkan tahun dan bulan
$mergedResults = [];

// Menggabungkan hasil dari invoices ke dalam array berdasarkan tahun dan bulan
foreach ($invoices as $invoice) {
    // Pastikan data tahun dan bulan ada di mergedResults
    if (!isset($mergedResults[$invoice->year])) {
        $mergedResults[$invoice->year] = []; // Membuat array kosong untuk tahun jika belum ada
    }

    // Menyimpan data invoice
    $mergedResults[$invoice->year][$invoice->month] = [
        'month' => $invoice->month,
        'year' => $invoice->year,
        'invoice_count' => $invoice->invoice_count,
        'total_hutang' => $invoice->total_hutang / 1000, // Membagi total_hutang dengan 1000
        'total_lunas' => 0, // Default jika tidak ada entri di jurnal
        'belum_lunas' => $invoice->total_hutang / 1000, // Belum lunas adalah total_hutang pada invoice dibagi 1000
    ];
}

// Menambahkan data jurnal ke dalam $mergedResults berdasarkan tahun dan bulan
foreach ($jurnals as $jurnal) {
    // Cek apakah tahun dan bulan jurnal ada di dalam mergedResults
    if (isset($mergedResults[$jurnal->year][$jurnal->month])) {
        // Update total_lunas (dibagi 1000)
        $mergedResults[$jurnal->year][$jurnal->month]['total_lunas'] += $jurnal->total_lunas / 1000;

        // Menghitung belum_lunas dengan merujuk ke data invoice (total_hutang - total_lunas)
        $mergedResults[$jurnal->year][$jurnal->month]['belum_lunas'] =
            $mergedResults[$jurnal->year][$jurnal->month]['total_hutang'] - $mergedResults[$jurnal->year][$jurnal->month]['total_lunas'];
    } else {
        // Jika tidak ada, buat entri baru dengan data default
        $mergedResults[$jurnal->year][$jurnal->month] = [
            'month' => $jurnal->month,
            'year' => $jurnal->year,
            'invoice_count' => 0, // Default jika tidak ada invoice
            'total_hutang' => 0, // Default jika tidak ada invoice
            'total_lunas' => $jurnal->total_lunas / 1000, // Total lunas dibagi 1000
            'belum_lunas' => 0, // Tidak ada hutang jika tidak ada invoice
        ];
    }
}

// Jika Anda ingin hasilnya dalam urutan bulan, Anda bisa melakukan sorting
foreach ($mergedResults as $year => $months) {
    ksort($mergedResults[$year]); // Mengurutkan berdasarkan bulan tanpa menggunakan referensi
}

// Hasil yang sudah digabungkan dan diurutkan

$summaryData = [];
foreach ($mergedResults as $year => $dataPerYear) {
    // Inisialisasi summary untuk setiap tahun
    $summaryData[$year] = [
        'total_invoice_count' => 0,
        'total_hutang' => 0,
        'total_lunas' => 0,
        'total_belum_lunas' => 0, // Menambahkan kolom untuk belum lunas
    ];

    foreach ($dataPerYear as $data) {
        // Menambahkan total per tahun
        $summaryData[$year]['total_invoice_count'] += $data['invoice_count'];
        $summaryData[$year]['total_hutang'] += $data['total_hutang'];
        $summaryData[$year]['total_lunas'] += $data['total_lunas'];
        $summaryData[$year]['total_belum_lunas'] += $data['total_hutang'] - $data['total_lunas']; // Menghitung belum lunas
    }
}

// dd($summaryData, $mergedResults);

    $months = [
        'January', 'February', 'March', 'April', 'May', 
        'June', 'July', 'August', 'September', 
        'October', 'November', 'December'
    ];
        return view('laporan.lap-hutang-vendor',compact('mergedResults', 'data' ,'months', 'summaryData'));
}

public function dataLPC(){;
// Mengambil daftar invoice dari query $invoices
    $invoices = Invoice::selectRaw('
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        COUNT(DISTINCT i.invoice) as invoice_count,
        GROUP_CONCAT(DISTINCT i.invoice) as invoice_list, 
        SUM(
            CASE 
                WHEN b.status_ppn = "ya" THEN t.harga_jual * t.jumlah_jual * 1.11  -- Menghitung PPN untuk harga jual
                ELSE t.harga_jual * t.jumlah_jual
            END
        ) as total_piutang,
        i.invoice') // Menambahkan kolom invoice
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
    ->join('barang as b', 't.id_barang', '=', 'b.id')
    ->where('i.tgl_invoice', '>', '2024-08-01') // Bergabung dengan tabel barang untuk mendapatkan status_ppn
    ->groupBy('year', 'month') // Grup berdasarkan tahun, bulan, dan invoice
    ->orderBy('year', 'asc') // Urutkan berdasarkan tahun secara ascending
    ->orderByRaw('MONTH(i.tgl_invoice) ASC') // Urutkan berdasarkan bulan secara ascending
    ->get();
    $invoices->map(function ($invoice) {
        $invoice->invoice_list = explode(',', $invoice->invoice_list);
        return $invoice;
    });
    
    // Gabungkan semua invoice_list menjadi satu array
    $invoicelist = $invoices->flatMap->invoice_list
        ->filter(function ($invoiceExternal) {
            return !empty($invoiceExternal); // Menghapus nilai kosong
        })
        ->unique()
        ->toArray();

    $jurnals = Jurnal::withTrashed() // Menyertakan data yang dihapus
    ->selectRaw('DATE_FORMAT(j.tgl, "%M") as month, 
                 YEAR(j.tgl) as year, 
                 SUM(j.debit) as total_lunas,
                 c.nama_akun')
    ->from('jurnal as j')
    ->join('coa as c', 'j.coa_id', '=', 'c.id')
    ->where('c.id', 5) // Menambahkan filter untuk coa_id = 5
    ->whereIn('j.invoice', $invoicelist) // Ganti filter dengan invoice
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(j.tgl)')
    ->get();
    
    $mergedResults = [];

    // Menggabungkan hasil dari invoices ke dalam array berdasarkan tahun dan bulan
    foreach ($invoices as $invoice) {
        // Pastikan data tahun dan bulan ada di mergedResults
        if (!isset($mergedResults[$invoice->year])) {
            $mergedResults[$invoice->year] = []; // Membuat array kosong untuk tahun jika belum ada
        }
    
        // Menyimpan data invoice
        $mergedResults[$invoice->year][$invoice->month] = [
            'month' => $invoice->month,
            'year' => $invoice->year,
            'invoice_count' => $invoice->invoice_count,
            'total_piutang' => $invoice->total_piutang / 1000, // Membagi total_hutang dengan 1000
            'total_lunas' => 0, // Default jika tidak ada entri di jurnal
            'belum_lunas' => $invoice->total_piutang / 1000, // Belum lunas adalah total_hutang pada invoice dibagi 1000
        ];
    }
    
    // Menambahkan data jurnal ke dalam $mergedResults berdasarkan tahun dan bulan
    foreach ($jurnals as $jurnal) {
        // Cek apakah tahun dan bulan jurnal ada di dalam mergedResults
        if (isset($mergedResults[$jurnal->year][$jurnal->month])) {
            // Update total_lunas (dibagi 1000)
            $mergedResults[$jurnal->year][$jurnal->month]['total_lunas'] += $jurnal->total_lunas / 1000;
    
            // Menghitung belum_lunas dengan merujuk ke data invoice (total_hutang - total_lunas)
            $mergedResults[$jurnal->year][$jurnal->month]['belum_lunas'] =
                $mergedResults[$jurnal->year][$jurnal->month]['total_piutang'] - $mergedResults[$jurnal->year][$jurnal->month]['total_lunas'];
        } else {
            // Jika tidak ada, buat entri baru dengan data default
            $mergedResults[$jurnal->year][$jurnal->month] = [
                'month' => $jurnal->month,
                'year' => $jurnal->year,
                'invoice_count' => 0, // Default jika tidak ada invoice
                'total_piutang' => 0, // Default jika tidak ada invoice
                'total_lunas' => $jurnal->total_lunas / 1000, // Total lunas dibagi 1000
                'belum_lunas' => 0, // Tidak ada hutang jika tidak ada invoice
            ];
        }
    }
    
    // Jika Anda ingin hasilnya dalam urutan bulan, Anda bisa melakukan sorting
    foreach ($mergedResults as $year => $months) {
        ksort($mergedResults[$year]); // Mengurutkan berdasarkan bulan tanpa menggunakan referensi
    }
$summaryData = [];
foreach ($mergedResults as $year => $dataPerYear) {
    // Inisialisasi summary untuk setiap tahun
    $summaryData[$year] = [
        'total_invoice_count' => 0,
        'total_piutang' => 0,
        'total_lunas' => 0,
        'total_belum_lunas' => 0, // Menambahkan kolom untuk belum lunas
    ];

    foreach ($dataPerYear as $data) {
        // Menambahkan total per tahun
        $summaryData[$year]['total_invoice_count'] += $data['invoice_count'];
        $summaryData[$year]['total_piutang'] += $data['total_piutang'];
        $summaryData[$year]['total_lunas'] += $data['total_lunas'];
        $summaryData[$year]['total_belum_lunas'] += $data['total_piutang'] - $data['total_lunas']; // Menghitung belum lunas
    }
}
$months = [
    'January', 'February', 'March', 'April', 'May', 
    'June', 'July', 'August', 'September', 
    'October', 'November', 'December'
];
    return view('laporan.lap-piutang-customer',compact('mergedResults', 'data' ,'months', 'summaryData'));
}
}
