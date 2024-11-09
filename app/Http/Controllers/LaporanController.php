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
$jurnals = Jurnal::withTrashed() // Menyertakan data yang dihapus
    ->selectRaw('DATE_FORMAT(j.tgl, "%M") as month, 
                 YEAR(j.tgl) as year, 
                 SUM(j.kredit) as total_lunas,
                 c.nama_akun')
    ->from('jurnal as j')
    ->join('coa as c', 'j.coa_id', '=', 'c.id')
    ->where('c.id', 5) // Menambahkan filter untuk coa_id = 5
    ->whereIn('j.invoice_external', $invoicelist) // Ganti filter dengan invoice
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(j.tgl)')
    ->get();

    // dd($jurnals);
// Menyusun hasil dari invoice berdasarkan tahun dan bulan
$mergedResults = [];
// Menggabungkan hasil dari invoices ke dalam array berdasarkan tahun
foreach ($invoices as $invoice) {
    $mergedResults[$invoice->year][] = [
        'month' => $invoice->month,
        'year' => $invoice->year,
        'invoice_count' => $invoice->invoice_count,
        'total_hutang' => $invoice->total_hutang,
        'total_lunas' => 0, // Default jika tidak ada entri di jurnal
        'belum_lunas' => $invoice->total_hutang, // Belum lunas adalah total_hutang pada invoice
    ];
}

// Menambahkan hasil dari jurnal ke dalam array, mencocokkan berdasarkan bulan dan tahun
foreach ($jurnals as $jurnal) {
    $found = false;
    // Memeriksa setiap tahun dan bulan dalam mergedResults
    foreach ($mergedResults as $year => &$months) {
        foreach ($months as &$result) {
            if ($result['month'] == $jurnal->month && $result['year'] == $jurnal->year) {
                // Menambahkan total_lunas dari jurnal ke dalam hasil yang cocok
                $result['total_lunas'] = $jurnal->total_lunas;
                // Menghitung sisa pembayaran (belum_lunas)
                $result['belum_lunas'] = $result['total_hutang'] - $result['total_lunas'];
                $found = true;
                break;
            }
        }
    }

    // Jika tidak ditemukan, tambahkan data jurnal sebagai entry baru
    if (!$found) {
        $mergedResults[$jurnal->year][] = [
            'month' => $jurnal->month,
            'year' => $jurnal->year,
            'invoice_count' => 0,
            'total_hutang' => 0,
            'total_lunas' => $jurnal->total_lunas,
            'belum_lunas' => 0, // Jika tidak ada invoice, tidak ada sisa pembayaran
        ];
    }
}

// Mengurutkan data berdasarkan tahun dan bulan (bulan numerik)
foreach ($mergedResults as $year => &$months) {
    // Mengurutkan bulan dalam setiap tahun berdasarkan urutan bulan numerik
    usort($months, function ($a, $b) {
        return date('m', strtotime($a['month'])) - date('m', strtotime($b['month']));
    });
}

// Output hasil
// dd($mergedResults);


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

// dd($summaryData);

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

    $jurnals = Jurnal::withTrashed() // Menyertakan data yang dihapus
    ->selectRaw('DATE_FORMAT(j.tgl, "%M") as month, 
                 YEAR(j.tgl) as year, 
                 SUM(j.kredit) as total_lunas,
                 c.nama_akun')
    ->from('jurnal as j')
    ->join('coa as c', 'j.coa_id', '=', 'c.id')
    ->where('c.id', 5) // Menambahkan filter untuk coa_id = 5
    ->whereIn('j.invoice', $invoicelist) // Ganti filter dengan invoice
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(j.tgl)')
    ->get();

$invoiceData = [];
foreach ($invoices as $invoice){
    foreach ($invoices as $invoice) {

        // Hitung persentase profit
        $total_profit_percentage = 0;
        if ($invoice->total_harga_beli > 0) {
            $total_profit_percentage = round(($total_profit / $invoice->total_harga_beli) * 100, 2);
        }
    $invoiceData [$invoice->year][] = [
        'month' => $invoice->month,
        'invoice_count' => $invoice->invoice_count,
        'total_piutang' => $invoice->total_piutang /1000,
    ];
}

$summaryData = [];
foreach ($invoiceData as $year => $dataPerYear) {
    $summaryData[$year] = [
        'total_invoice_count' => 0,
        'total_piutang' => 0,
    ];
    foreach ($dataPerYear as $data) {
        // Menambahkan total per tahun
        $summaryData[$year]['total_invoice_count'] += $data['invoice_count'];
        $summaryData[$year]['total_piutang'] += $data['total_piutang'];
    }
}
$months = [
    'January', 'February', 'March', 'April', 'May', 
    'June', 'July', 'August', 'September', 
    'October', 'November', 'December'
];
    return view('laporan.lap-piutang-customer',compact('invoiceData', 'months', 'summaryData'));
}
}
}
