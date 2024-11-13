<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Coa;
use Carbon\Carbon;
class LaporanController extends Controller
{
    public function dataLHV(){
        $jurnals = Jurnal::where('coa_id', 5)
        ->whereNotNull('invoice_external')
        ->whereNull('deleted_at')
        ->orderByRaw('MONTH(tgl)')
        ->get();
        $j_list = $jurnals->pluck('id_transaksi');

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
             i.id_transaksi')  // Menambahkan id_transaksi
        ->from('invoice as i')
        ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
        ->join('barang as b', 't.id_barang', '=', 'b.id')
        ->where('i.tgl_invoice', '>', '2024-08-01')
        ->whereNull('deleted_at')
        ->groupBy('year', 'month') // Mengelompokkan berdasarkan tahun, bulan, dan id_transaksi
        ->orderBy('year', 'asc')
        ->orderByRaw('MONTH(i.tgl_invoice) ASC')
        ->get();

        $i_list = $invoices->pluck('id_transaksi');
        

    $jurnals = Jurnal::withTrashed()
    ->selectRaw('DATE_FORMAT(i.tgl_invoice, "%M") as month, 
                 YEAR(i.tgl_invoice) as year, 
                 SUM(j.kredit) as total_lunas,
                 kredit')
    ->from('jurnal as j')
    ->join('invoice as i', 'j.id_transaksi', '=', 'i.id_transaksi')
    ->where(function($query) {
        $query->where('j.coa_id', 5)
              ->orWhere('j.coa_id', 32); // Menambahkan kondisi coa_id 5 atau 32
    })
    ->where('i.tgl_invoice', '>', '2024-08-01')
    ->whereNotNull('invoice_external')
     ->whereNull('deleted_at')
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(i.tgl_invoice)')
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
        $mergedResults[$jurnal->year][$jurnal->month]['total_lunas'] = $jurnal->total_lunas / 1000;
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


public function dataLapPiutang(Request $request)
{
    // Ambil parameter untuk pagination dari request
    $page = $request->input('page', 1); // Halaman saat ini, default 1
    $rows = $request->input('rows', 20); // Jumlah baris per halaman, default 20

    // Ambil data dari tabel Jurnals dengan pagination, urutkan berdasarkan 'tgl' descending
    $jurnals = Jurnal::withTrashed()
        ->where('tipe', 'BBM')
        ->whereNull('deleted_at')
        ->where('debit', '!=', 0)
        ->select('debit', 'tgl', 'invoice')
        ->orderBy('tgl', 'desc')
        ->get();

    // Ambil data dari tabel Invoices, urutkan berdasarkan 'tgl_invoice' descending
    $invoices = Invoice::with([
        'transaksi.suratJalan.customer' => function($query) {
            $query->select('id', 'nama');
        },
        'transaksi.barang' // Menambahkan relasi transaksi.barang
    ])
    ->orderBy('created_at', 'desc')
    ->get();

    // Mengelompokkan dan menghitung subtotal untuk setiap invoice
    $data = $invoices->groupBy('invoice')->map(function($group) {
        $subtotal = $group->sum('subtotal'); // Jumlahkan subtotal untuk setiap invoice
        $ppn = 0; // Inisialisasi PPN

        // Menghitung PPN jika ada barang dengan status_ppn == 'ya'
        foreach ($group as $invoice) {
            foreach ($invoice->transaksi->barang as $barang) {
                if (optional($barang)->status_ppn == 'ya') {
                    $ppn += $barang->subtotal * ($barang->value_ppn / 100);
                }
            }
        }

        // Total harga dengan PPN
        $jumlah_harga = $subtotal + $ppn;


        return [
            'invoice' => $group->first()->invoice,
            'customer' => $group->first()->transaksi->suratJalan->customer->nama,
            'jumlah_harga' => $jumlah_harga,
            'ditagih_tgl' => $group->first()->tgl_invoice,
            'tempo' => Carbon::parse($group->first()->tgl_invoice)->addDays(60),
            'dibayar_tgl' => null,
            'sebesar' => 0,
            'kurang_bayar' => $jumlah_harga,
        ];
    });

    // Menggabungkan data jurnal dengan data invoice
    foreach ($jurnals as $jurnal) {
        if ($data->has($jurnal->invoice)) {
            $currentData = $data->get($jurnal->invoice);

            // Hitung subtotal dan PPN untuk invoice yang sesuai
            $subtotal = $invoices->where('invoice', $jurnal->invoice)->sum('subtotal');
            $ppn = 0;
            foreach ($invoices->where('invoice', $jurnal->invoice) as $invoice) {
                $barang= $invoice->first()->transaksi->barang ?? null;
                    if ($barang && $barang->status_ppn === 'ya') {
                        $ppn = $barang->subtotal * ($barang->value_ppn / 100);
                    }
            }

            $totalHarga = $subtotal + $ppn;
            $total = $totalHarga - $jurnal->debit;

            $currentData['dibayar_tgl'] = $jurnal->tgl;
            $currentData['sebesar'] = $jurnal->debit;
            $currentData['kurang_bayar'] = $totalHarga - $jurnal->debit;
            $data->put($jurnal->invoice, $currentData);
        } else {
            $invoicesForJurnal = $invoices->firstWhere('invoice', $jurnal->invoice);

            if ($invoicesForJurnal) {
                $subtotal = $invoices->where('invoice', $jurnal->invoice)->sum('subtotal');
                $ppn = 0;
                $barang= $invoice->first()->transaksi->barang ?? null;
                if ($barang && $barang->status_ppn == 'ya') {
                        $ppn += $barang->subtotal * ($barang->value_ppn / 100);
                    }
                

                $totalHarga = $subtotal + $ppn;
                $total = $totalHarga- $jurnal->debit;

                $data->put($jurnal->invoice, [
                    'invoice' => $invoicesForJurnal->invoice,
                    'customer' => $invoicesForJurnal->transaksi->suratJalan->customer->nama,
                    'jumlah_harga' => $totalHarga,
                    'ditagih_tgl' => $invoicesForJurnal->tgl_invoice,
                    'tempo' => Carbon::parse($invoicesForJurnal->tgl_invoice)->addDays(60),
                    'dibayar_tgl' => $jurnal->tgl,
                    'sebesar' => $jurnal->debit,
                    'kurang_bayar' => $total,
                ]);
            }
        }
    }

    // Menambahkan nomor urut
    $result = [];
    $index = 1;
    foreach ($data as $item) {
        $item['no'] = $index++;
        $result[] = $item;
    }

    // Pagination
    $indexStart = ($page - 1) * $rows;
    $paginatedData = collect($result)->slice($indexStart, $rows)->values();

    $totalRecords = count($result);
    $totalPages = ceil($totalRecords / $rows);

    // Mengembalikan data dalam format yang sesuai dengan jqGrid
    return response()->json([
        'data' => $paginatedData,
        'page' => $page,
        'total' => $totalPages,
        'records' => $totalRecords,
        'rows' => $paginatedData,
    ]);
}




    public function lapPiutang()
    {
        return view('jurnal.lap-piutang');
    }

    }
