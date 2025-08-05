<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use App\Models\Invoice;
use App\Models\BiayaInv;
use App\Models\Transaction;
use App\Models\Coa;
use App\Models\Customer;
use App\Http\Resources\BiayaInvResource;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Satuan;
use Carbon\Carbon;
class LaporanController extends Controller
{
    public function dataLOC() {
        // Mengambil tahun yang unik dari data invoice
        $years = Invoice::selectRaw('YEAR(tgl_invoice) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Query untuk mendapatkan data laporan omzet
        $invoices = Invoice::selectRaw('
            c.id as customer_id,
            c.nama as customer_nama,
            DATE_FORMAT(i.tgl_invoice, "%M") as month, 
            YEAR(i.tgl_invoice) as year, 
            COUNT(DISTINCT i.invoice) as invoice_count,  
            SUM(
                CASE 
                    WHEN b.status_ppn = "ya" THEN t.harga_jual * t.jumlah_jual * 1.11
                    ELSE t.harga_jual * t.jumlah_jual
                END
            ) as omzet
        ')
        ->from('invoice as i')
        ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
        ->join('surat_jalan as sj', 't.id_surat_jalan', '=', 'sj.id')
        ->join('customer as c', 'sj.id_customer', '=', 'c.id')
        ->join('barang as b', 't.id_barang', '=', 'b.id')
        ->whereYear('i.tgl_invoice', 2025) // Filter tahun default
        ->groupBy('c.id', 'year', 'month')
        ->orderBy('omzet', 'desc') // Urutkan berdasarkan omzet terbesar
        ->orderBy('year', 'asc')
        ->orderByRaw('MONTH(i.tgl_invoice) ASC')
        ->get();

    
        $mergedResults = [];
$monthlyTotals = [];
$totalOmzetPerCustomer = [];

foreach ($invoices as $invoice) {
    // Inisialisasi customer
    if (!isset($mergedResults[$invoice->customer_id])) {
        $mergedResults[$invoice->customer_id] = [
            'customer_name' => $invoice->customer_nama,
            'years' => []
        ];
        $totalOmzetPerCustomer[$invoice->customer_id] = 0;
    }

    // Inisialisasi tahun
    if (!isset($mergedResults[$invoice->customer_id]['years'][$invoice->year])) {
        $mergedResults[$invoice->customer_id]['years'][$invoice->year] = [];
    }

    // Masukkan data bulan
    $omzetRibuan = $invoice->omzet / 1000;
    $mergedResults[$invoice->customer_id]['years'][$invoice->year][$invoice->month] = [
        'month' => $invoice->month,
        'year' => $invoice->year,
        'invoice_count' => $invoice->invoice_count,
        'omzet' => $omzetRibuan
    ];

    // Hitung total omzet per customer
    $totalOmzetPerCustomer[$invoice->customer_id] += $omzetRibuan;

    // Hitung total omzet bulanan (jika masih ingin dipakai)
    if (!isset($monthlyTotals[$invoice->year][$invoice->month])) {
        $monthlyTotals[$invoice->year][$invoice->month] = 0;
    }
    $monthlyTotals[$invoice->year][$invoice->month] += $omzetRibuan;
}

// 👉 Urutkan hasil berdasarkan total omzet customer (descending)
uksort($mergedResults, function($a, $b) use ($totalOmzetPerCustomer) {
    return $totalOmzetPerCustomer[$b] <=> $totalOmzetPerCustomer[$a];
});

// (Opsional) nama-nama bulan
$months = [
    'January', 'February', 'March', 'April', 'May',
    'June', 'July', 'August', 'September',
    'October', 'November', 'December'
];

    
        return view('laporan.lap-omzet-customer', compact('mergedResults', 'months', 'years','monthlyTotals'));
    }

    public function dataLS() {
        // Mengambil tahun yang unik dari data invoice
        $years = Invoice::selectRaw('YEAR(tgl_invoice) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Query untuk mendapatkan data laporan omzet
        $invoices = Invoice::selectRaw('
            c.id as customer_id,
            c.sales as customer_sales,
            DATE_FORMAT(i.tgl_invoice, "%M") as month, 
            YEAR(i.tgl_invoice) as year, 
            COUNT(DISTINCT i.invoice) as invoice_count,  
            SUM(
                CASE 
                    WHEN b.status_ppn = "ya" THEN t.harga_jual * t.jumlah_jual * 1.11
                    ELSE t.harga_jual * t.jumlah_jual
                END
            ) as omzet
        ')
        ->from('invoice as i')
        ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
        ->join('surat_jalan as sj', 't.id_surat_jalan', '=', 'sj.id')
        ->join('customer as c', 'sj.id_customer', '=', 'c.id')
        ->join('barang as b', 't.id_barang', '=', 'b.id')
        ->whereYear('i.tgl_invoice', 2025) // Filter tahun default
        ->groupBy('c.sales', 'year', 'month')
        ->orderBy('omzet', 'desc') // Urutkan berdasarkan omzet terbesar
        ->orderBy('year', 'asc')
        ->orderByRaw('MONTH(i.tgl_invoice) ASC')
        ->get();
    
        $mergedResults = [];
        $monthlyTotals = [];
        
        foreach ($invoices as $invoice) {
            if (!isset($mergedResults[$invoice->customer_sales])) {
                $mergedResults[$invoice->customer_sales] = [
                    'sales_name' => $invoice->customer_sales,
                    'years' => []
                ];
            }
    
            if (!isset($mergedResults[$invoice->customer_sales]['years'][$invoice->year])) {
                $mergedResults[$invoice->customer_sales]['years'][$invoice->year] = [];
            }
    
            $mergedResults[$invoice->customer_sales]['years'][$invoice->year][$invoice->month] = [
                'month' => $invoice->month,
                'year' => $invoice->year,
                'invoice_count' => $invoice->invoice_count,
                'omzet' => $invoice->omzet / 1000
            ];
    
            // Menambahkan total omzet per bulan
            if (!isset($monthlyTotals[$invoice->year][$invoice->month])) {
                $monthlyTotals[$invoice->year][$invoice->month] = 0;
            }
            $monthlyTotals[$invoice->year][$invoice->month] += $invoice->omzet / 1000;
        }
        
        $months = [
            'January', 'February', 'March', 'April', 'May', 
            'June', 'July', 'August', 'September', 
            'October', 'November', 'December'
        ];
    
        return view('laporan.lap-sales', compact('mergedResults', 'months', 'years', 'monthlyTotals'));
    }

    public function dataFLS(Request $request) {
        // Mengambil tahun unik dari data invoice
        $years = Invoice::selectRaw('YEAR(tgl_invoice) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Mengambil tahun dari request atau gunakan tahun berjalan
        $selectedYear = $request->input('year', date('Y'));
    
        // Daftar bulan
        $months = [
            'January', 'February', 'March', 'April', 'May', 
            'June', 'July', 'August', 'September', 
            'October', 'November', 'December'
        ];
    
        // Query untuk laporan omzet
         $invoices = Invoice::selectRaw('
        c.id as customer_id,
        c.sales as customer_sales,
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        SUM(t.jumlah_jual) as jumlah_juals,
        t.satuan_jual as satuan_barang,
        SUM(CASE 
            WHEN t.satuan_jual = "KG" THEN t.jumlah_jual * b.value 
            ELSE t.jumlah_jual 
        END) as jumlah_kg_to_value
    ')
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
    ->join('surat_jalan as sj', 't.id_surat_jalan', '=', 'sj.id')
    ->join('customer as c', 'sj.id_customer', '=', 'c.id')
    ->join('barang as b', 't.id_barang', '=', 'b.id')
    ->whereYear('i.tgl_invoice', $selectedYear)
    ->groupBy('c.sales', 'year', 'month', 'satuan_barang')
    ->orderBy('year', 'asc')
    ->orderByRaw('MONTH(i.tgl_invoice) ASC')
    ->get();
        // Ambil daftar satuan barang
        $satuan = Satuan::pluck('nama_satuan');
    
        // Struktur data untuk tampilan Blade
        $mergedResults = [];
        $monthlyTotals = [];
    
        foreach ($invoices as $invoice) {
            $salesName = $invoice->customer_sales;
            $month = $invoice->month;
            $satuanName = $invoice->satuan_barang;
            if (strtoupper($invoice->satuan_barang) === 'KG') {
                $totalValue = $invoice->jumlah_juals . ' KG (' . $invoice->jumlah_kg_to_value . ' ZAK)';
            } else {
                $totalValue = $invoice->jumlah_juals;
            }
        

            // Inisialisasi jika belum ada data
            if (!isset($mergedResults[$salesName])) {
                $mergedResults[$salesName] = [
                    'sales_name' => $salesName,
                    'years' => []
                ];
            }

            if (!isset($mergedResults[$salesName]['years'][$selectedYear])) {
                $mergedResults[$salesName]['years'][$selectedYear] = [];
            }

            if (!isset($mergedResults[$salesName]['years'][$selectedYear][$month])) {
                $mergedResults[$salesName]['years'][$selectedYear][$month] = [];
            }

            // Simpan data omzet berdasarkan bulan dan satuan
            $mergedResults[$salesName]['years'][$selectedYear][$month][$satuanName] = $totalValue;
        }

        return view('laporan.lap-fee-sales', compact('mergedResults', 'satuan', 'months', 'years', 'selectedYear', 'monthlyTotals'));
    }
    private function cleanNumber($value) {
        return (float) preg_replace('/[^0-9.]/', '', $value);
    } 
    
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
        public function monitoring_invoice(){
            $invoices = Invoice::from('invoice as i')
            ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
            ->join('barang as b', 't.id_barang', '=', 'b.id')
            ->join('surat_jalan as sj', 't.id_surat_jalan', '=', 'sj.id') // Join surat_jalan
            ->join('customer as c', 'sj.id_customer', '=', 'c.id')       // Join customer
            ->where('i.tgl_invoice', '>', '2025-01-01')
            ->groupBy('i.invoice') // jangan lupa group kolom yg dipilih
            ->selectRaw('
                i.id,
                i.invoice,
                c.nama as customer,
                SUM(i.subtotal + 
                    CASE 
                        WHEN b.status_ppn = "ya" THEN i.subtotal * (b.value_ppn / 100)
                        ELSE 0
                    END
                ) as total_subtotal
            ')
            ->get();

$invoiceIDs = $invoices->pluck('id');
$biayaInvAll = BiayaInv::whereIn('id_inv', $invoiceIDs)->get()->groupBy('id_inv');

$invoiceLebihBayar = [];

foreach ($invoices as $inv) {
    $biayaInvList = $biayaInvAll->get($inv->id, collect());

    $totalBayar = $biayaInvList->sum('nominal');
    $tglPembayar = $biayaInvList->pluck('tgl_pembayar');
$selisih = (int)$totalBayar - (int)$inv->total_subtotal;

if ($selisih > 1) {
    $invoiceLebihBayar[] = [
        'invoice'       => $inv->invoice,
        'tgl_pembayar'  => $tglPembayar,
        'total_tagihan' => number_format($inv->total_subtotal, 0, ',', '.'),
        'total_bayar'   => number_format($totalBayar, 0, ',', '.'),
        'selisih'       => number_format($selisih, 0, ',', '.'),
    ];
}


}
        
            return view('laporan.lap-monitor-invoice',compact('invoices','invoiceLebihBayar'));
        }

        public function dashMonitor(){
    $currentMonth = now()->month;
    $currentYear = now()->year;

    // Get the month and year from the request, or use the current month and year as defaults
    $bulan = date('m');
    $tahun = date('Y');

    // Define the start date as January 2023
    $startDate = '2023-01-01';
    $tahunCo = $tahun;
    $dateCo = now()->create($tahunCo . '-' . '01' . '-01')->startOfMonth()->toDateString();
    // Define the end date based on the selected month and year
    $endDate = now()->create($tahun . '-' . $bulan . '-01')->endOfMonth()->toDateString();

    // Get COA data based on account numbers
    $coa1 = Coa::whereIn('id',[8,89,91,90])->orderBy('no_akun')->get();
    $coa2 = Coa::whereIn('id',[35,98])->orderBy('no_akun')->get();

    // Initialize totals array
    $totals = [];
    $coaId1 = $coa1->pluck('id')->toArray();
    $coaId2 = $coa2->pluck('id')->toArray();

    // Merge all COA IDs into a single array
    $allCoaIds = array_merge($coaId1, $coaId2);

    foreach ($allCoaIds as $coaId) {
        // Calculate debit and credit totals within the date range
        $debit = Jurnal::where('coa_id', $coaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->sum('debit');

        $kredit = Jurnal::where('coa_id', $coaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->sum('kredit');
        if (in_array($coaId, $coaId1)) {
            $selisih = $debit - $kredit;
        } else {
            $selisih = $kredit - $debit;
        }
        // Store the totals in the array
        $totals[$coaId] = [
            'debit' => $debit,
            'kredit' => $kredit,
            'selisih' => $selisih,
        ];
        }
    return view('laporan.ds-monitor', compact(
        'coa1', 'coa2', 
        'totals',
        'bulan', 'tahun', 'startDate', 'endDate'
    ));
    }

        public function listInv(Request $request)
        {
            $searchTerm   = $request->get('searchString', '');
            $currentPage  = (int) $request->get('page', 1);
            $perPage      = (int) $request->get('rows', 10);
        
            // Ambil semua invoice dengan relasi
            $query = Invoice::with(['biaya_inv', 'transaksi.suratJalan.customer'])
                ->where('invoice.tgl_invoice', '>', '2025-01-01')
                ->orderBy('invoice', 'desc');
        
            // Filter customer
            if ($request->filled('customer')) {
                $query->whereHas('transaksi.suratJalan.customer', function ($q) use ($request) {
                    $q->where('nama', 'LIKE', '%' . $request->customer . '%');
                });
            }
        
            // Filter invoice
            if ($request->filled('invoice')) {
                $query->where('invoice', 'LIKE', '%' . $request->invoice . '%');
            }
        
            // Filter tgl_invoice
            if ($request->filled('tgl_inv')) {
                $query->where('tgl_invoice', 'LIKE', '%' . $request->tgl_inv . '%');
            }
        
            // Filter tgl_pembayar (dari relasi biaya_inv)
            if ($request->filled('tgl_pembayar')) {
                $query->whereHas('biaya_inv', function ($q) use ($request) {
                    $q->where('tgl_pembayar', 'LIKE', '%' . $request->tgl_pembayar . '%');
                });
            }
        
            // Ambil semua data (belum dipaginasi)
            $invoices = $query->get();
        
            // Kelompokkan berdasarkan kode invoice
            $grouped = $invoices->groupBy('invoice');
        
            // Ambil hanya grup dengan total nominal biaya_inv > 0
            $filtered = $grouped->filter(function ($groupItems) {
                return $groupItems->flatMap->biaya_inv->sum('nominal') > 0;
            });
        
            $totalRecords = $filtered->count();
        
            // Pagination manual
            $paginated = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
            $result = $paginated->map(function ($groupItems, $idx) use ($currentPage, $perPage) {
                $first = $groupItems->first();
        
                $biayaInv = $groupItems->flatMap->biaya_inv;
                $totalNominal = $biayaInv->sum('nominal');
        
                return [
                    'DT_RowIndex'  => ($currentPage - 1) * $perPage + $idx + 1,
                    'customer'     => optional(optional($first->transaksi)->suratJalan)->customer->nama ?? '-',
                    'tgl_inv'      => $first->tgl_invoice ?? '-',
                    'tgl_pembayar' => $biayaInv->pluck('tgl_pembayar')->implode(', ') ?: '-',
                    'invoice'      => $first->invoice ?? '-',
                    'nominal'      => number_format($totalNominal, 0, ',', '.'),
                    'total'        => $this->calculateTOTAL($groupItems),
                    'sisa'         => $this->calculateSisa($groupItems),
                ];
            });
        
            return response()->json([
                'page'    => $currentPage,
                'total'   => ceil($totalRecords / $perPage),
                'records' => $totalRecords,
                'rows'    => $result,
            ]);
        }
        

private function calculateTOTAL($row)
{
    $subtotal = $row->sum('subtotal');
    $barang = optional(optional($row->first()->transaksi)->barang);

    if ($barang->status_ppn === 'ya') {
        $ppnRate = (float) $barang->value_ppn;
        $ppn = $subtotal * ($ppnRate / 100);
        $total = $subtotal + $ppn;
    } else {
        $total = $subtotal;
    }

    return number_format($total, 0, ',', '.');
}


private function calculateSisa($row)
{
    $subtotal = $row->sum('subtotal');
    $barang = optional(optional($row->first()->transaksi)->barang);

    if ($barang->status_ppn === 'ya') {
        $ppnRate = (float) $barang->value_ppn;
        $ppn = $subtotal * ($ppnRate / 100);
        $total = $subtotal + $ppn;
    } else {
        $total = $subtotal;
    }

    $dibayar = $row->flatMap->biaya_inv->sum('nominal');

    return number_format($total - $dibayar, 0, ',', '.');
}


    
        
        public function monitorSave(Request $request)
        {
            DB::beginTransaction();
            try {
                foreach ($request->invoice as $key => $invoiceId) {
                    if (!$invoiceId) continue;

                    // Ambil invoice terkait
                    $invoice = Invoice::with('transaksi')->find($invoiceId);
                    if (!$invoice || !$invoice->id_transaksi) {
                        throw new \Exception("Invoice ID $invoiceId tidak memiliki transaksi terkait.");
                    }

                    $nominal = preg_replace('/[^0-9]/', '', $request->nominal[$key]);

                    BiayaInv::create([
                        'id_inv'       => $invoiceId,
                        'id_trans'     => $invoice->id_transaksi, // ✅ pastikan field ini dikirim
                        'tgl_pembayar' => $request->tanggal_bayar,
                        'nominal'      => $nominal,
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'Data pembayaran berhasil disimpan.');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
            }
        }
        public function jqgrid1()
        {
            $page    = request('page', 1);
            $limit   = request('rows', 10);
            $sidx    = request('sidx', 'biaya_inv.id');
            $sord    = request('sord', 'asc');
            $search  = request('_search') === 'true';
            $tipe_null = request('tipe_null');
            $tipe_bbmn = request('tipe_bbmn');
            $tipe_bbm = request('tipe_bbm');
        
$query = BiayaInv::with(['invoice', 'transaksi.suratJalan.customer'])
    ->join('invoice', 'biaya_inv.id_inv', '=', 'invoice.id')
    ->select('biaya_inv.*')
    ->where('biaya_inv.nominal', '>', 0);


// Filter tgl_pembayar (wajib / selalu ada)
if ($tipe_null){
if (request()->filled('tgl_pembayar') || request()->filled('tgl_pembayar') && request()->filled('inv')) {
    $query->whereDate('biaya_inv.tgl_pembayar', request('tgl_pembayar'))->where('invoice.invoice', 'LIKE', '%' . request('inv') . '%');
}
}

if (request()->filled('tgl_pembayar3') || request()->filled('tgl_pembayar3') && request()->filled('inv3')) {
    $query->whereDate('biaya_inv.tgl_pembayar', request('tgl_pembayar3'))->where('invoice.invoice', 'LIKE', '%' . request('inv3') . '%');
}

// Filter invoice
// Filter customer
if (request()->filled('customer')) {
    $query->whereHas('transaksi.suratJalan.customer', function ($q) {
        $q->where('nama', 'LIKE', '%' . request('customer') . '%');
    });
}

// Filter tanggal invoice

if ($tipe_bbmn){
    $query->where('tipe','BBMN');
if (request()->filled('tgl_pembayar1') || request()->filled('tgl_pembayar1') && request()->filled('inv1')) {
    $query->whereDate('biaya_inv.tgl_pembayar', request('tgl_pembayar1'))->where('invoice.invoice', 'LIKE', '%' . request('inv1') . '%');
}

}

if ($tipe_bbm){
    $query->where('tipe','BBM');
if (request()->filled('tgl_pembayar2') || request()->filled('tgl_pembayar2') && request()->filled('inv2')) {
    $query->whereDate('biaya_inv.tgl_pembayar', request('tgl_pembayar2'))->where('invoice.invoice', 'LIKE', '%' . request('inv2') . '%');
}

}

// Ambil semua data
$invoices = $query->get();

        
            // Group berdasarkan tgl_pembayar
            $grouped = $invoices->groupBy(function ($item) {
                return $item->tgl_pembayar . '||' . $item->invoice->invoice;
            });
            
        
            $totalRecords = $grouped->count();
            $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 0;
            if ($page > $totalPages) $page = $totalPages;
        
            $paginated = $grouped->slice(($page - 1) * $limit, $limit);
        
            // Format data baris
            $rows = $paginated->map(function ($items, $key) {
                [$tgl_pembayar, $invoice] = explode('||', $key);
                $first = $items->first();
                $totalNominal = $items->sum('nominal');
                $ids = $items->pluck('id')->implode(',');
                $nominal = $items->pluck('nominal')->implode(',');
            
                return [
                    'id'           => $ids,
                    'tipe'         => $first->tipe,
                    'jurnal'       => $first->jurnal ?? 'Belum Terjurnal',
                    'tgl_pembayar' => $tgl_pembayar,
                    'customer'     => optional(optional($first->transaksi)->suratJalan)->customer->nama ?? '-',
                    'invoice'      => $invoice,
                    'bayar'        => $totalNominal,
                    'list_nominal' => $nominal,
                ];
            })->values();
            
        
            // Total semua nominal (dari semua grup, bukan hanya paginasi)
            $grandTotalNominal = $invoices->sum('nominal');
        
            // Return ke jqGrid
            return response()->json([
                'page'    => $page,
                'total'   => $totalPages,
                'records' => $totalRecords,
                'rows'    => $rows,
                'userdata' => [
                    'bayar'        => $grandTotalNominal,
                ]
            ]);
        }
        
        

public function destroy($id)
{
    $biaya = BiayaInv::findOrFail($id);
    $biaya->delete();

    return response()->json(['message' => 'Data berhasil dihapus.']);
}


        public function dataLPC(){;

        $invoices = Invoice::selectRaw('
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        COUNT(DISTINCT i.invoice) as invoice_count,  
        SUM(
            CASE 
                WHEN b.status_ppn = "ya" THEN t.harga_jual * t.jumlah_jual * 1.11
                ELSE t.harga_jual * t.jumlah_jual
            END
        ) as total_piutang,
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

    $i_list = $invoices->pluck('invoice');
    

    $jurnals = Jurnal::withTrashed()
    ->selectRaw('DATE_FORMAT(i.tgl_invoice, "%M") as month, 
                 YEAR(i.tgl_invoice) as year, 
                 SUM(j.debit) as total_lunas, 
                 GROUP_CONCAT(j.debit) as debit_list')  // Aliasing untuk GROUP_CONCAT
    ->from('jurnal as j')
    ->join('invoice as i', 'j.id_transaksi', '=', 'i.id_transaksi')
    ->where(function($query) {
        $query->where('j.coa_id', 5)
              ->orWhere('j.coa_id', 2); // Kondisi coa_id 5 atau 2
    })
    ->where('i.tgl_invoice', '>', '2024-08-01')
    ->whereNotNull('j.invoice') // Mengambil hanya data yang memiliki nilai invoice
    ->groupBy('month', 'year')
    ->orderByRaw('MONTH(i.tgl_invoice)') // Mengurutkan berdasarkan bulan
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

// Hasil yang sudah digabungkan dan diurutkan

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

// dd($summaryData, $mergedResults);

$months = [
    'January', 'February', 'March', 'April', 'May', 
    'June', 'July', 'August', 'September', 
    'October', 'November', 'December'
];
    return view('laporan.lap-piutang-customer',compact('mergedResults', 'data' ,'months', 'summaryData'));
        }


        public function dataLapPiutang(Request $request)
{
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');
    $nominalInput = $request->input('nominal');
    $inputVal = preg_replace('/[^\d]/', '', $nominalInput ?? '');

    $tahunIni = Carbon::now()->startOfYear()->format('Y-m-d');

    // Ambil jurnal BBM
    $jurnals = Jurnal::withTrashed()
        ->whereIn('tipe', ['BBM','BBMN'])
        ->whereNull('deleted_at')
        ->where('debit', '!=', 0)
        ->where('tgl', '>', '2024-08-01')
        ->select(
            'invoice',
            \DB::raw('SUM(debit) as total_debit'),
            \DB::raw("GROUP_CONCAT(CONCAT('<div style=\"margin-bottom: 5px; margin-top:5px;\">', DATE_FORMAT(tgl, '%Y-%m-%d'), '</div>') ORDER BY tgl ASC SEPARATOR '') as daftar_tanggal"),
            "jurnal.*"
        )
        ->groupBy('invoice')
        ->orderByDesc('invoice')
        ->get();

    // Ambil invoice dengan relasi
    $invoices = Invoice::with([
        'transaksi.suratJalan.customer:id,nama',
        'transaksi.barang'
    ]);

    // Filter berdasarkan request
    if ($request->filled('tgl_inv') || $request->filled('inv') || $request->filled('customer')) {
      $invoices = Invoice::where('tgl_invoice', 'like', '%' . $request->tgl_inv . '%')
    ->where('invoice', 'like', '%' . $request->inv . '%');
    
    if ($request->filled('customer')) {
        $invoices = $invoices->whereHas('transaksi.suratJalan.customer', function ($query) use ($request) {
            $query->where('id', $request->customer);
        });
    }

    } elseif ($inputVal !== '') {
        // Jika hanya nominal yang diisi → batasi tgl_invoice ke tahun ini
        $invoices->where('tgl_invoice', '>=', $tahunIni);
    } else {
        // Tidak ada filter sama sekali, kosongkan
        return response()->json([
            'rows' => [],
            'current_page' => 1,
            'last_page' => 0,
            'total' => 0,
            'records' => 0,
            'debug_warna' => [],
        ]);
    }

    $invoices = $invoices->orderBy('created_at', 'desc')->get();

    // Group dan hitung per invoice
    $data = $invoices->groupBy('invoice')->map(function ($group) {
        $subtotal = $group->sum('subtotal');
        $ppn = 0;
        foreach ($group as $invoice) {
            $barang = $invoice->transaksi->barang;
            if ($barang && $barang->status_ppn === 'ya') {
                $ppn += $invoice->subtotal * ($barang->value_ppn / 100);
            }
        }

        $jumlah_harga = round($subtotal + $ppn);
        $first = $group->first();
        $customer = $first->transaksi->suratJalan->customer->nama ?? null;
        $top = Customer::where('nama', $customer)->pluck('top')->first();

        return [
            'tanggal' => now()->format('Y-m-d'),
            'invoice' => $first->invoice,
            'customer' => $customer,
            'jumlah_harga' => $jumlah_harga,
            'top' => $top,
            'ditagih_tgl' => $first->tgl_invoice,
            'tempo' => Carbon::parse($first->tgl_invoice)->addDays((int) $top)->format('Y-m-d'),
            'hitung_tempo' => Carbon::parse($first->tgl_invoice)->addDays((int) $top),
            'dibayar_tgl' => null,
            'sebesar' => 0,
            'kurang_bayar' => (int) $jumlah_harga,
        ];
    });

    // Gabungkan data jurnal ke invoice
    foreach ($jurnals as $jurnal) {
        if ($data->has($jurnal->invoice)) {
            $current = $data->get($jurnal->invoice);
            $subtotal = $invoices->where('invoice', $jurnal->invoice)->sum('subtotal');
            $ppn = 0;
            foreach ($invoices->where('invoice', $jurnal->invoice) as $invoice) {
                $barang = $invoice->transaksi->barang;
                if ($barang && $barang->status_ppn === 'ya') {
                    $ppn += $invoice->subtotal * ($barang->value_ppn / 100);
                }
            }

            $total = round($subtotal + $ppn);
            $current['dibayar_tgl'] = $jurnal->daftar_tanggal;
            $current['sebesar'] = $jurnal->total_debit;
            $current['kurang_bayar'] = (int) ($total - $jurnal->total_debit);
            $data->put($jurnal->invoice, $current);
        }
    }

    // Tambahkan nomor urut
    $result = [];
    $i = 1;
    foreach ($data as $item) {
        $item['no'] = $i++;
        $result[] = $item;
    }

    $data = collect($result)->sortBy('customer')->values();

    // Filter ditagih_tgl
    if ($request->filled('ditagih_tgl')) {
        $data = $data->filter(fn($row) => Str::contains($row['ditagih_tgl'], $request->ditagih_tgl))->values();
    }

    // Tambahkan warna_status
    $data = $data->map(function ($row) {
        $today = now()->format('Y-m-d');
        $tempoDate = Carbon::parse($row['tempo'])->format('Y-m-d');
        $daysDiff = Carbon::parse($row['tempo'])->diffInDays($today, true);

        $warna = '';
        if ((float) $row['kurang_bayar'] == 0) {
            $warna = 'hijau';
        } elseif ((int) $row['top'] === 0 && $tempoDate === $today) {
            $warna = '';
        } elseif (Carbon::parse($row['tempo'])->isFuture()) {
            if ($daysDiff > 0 && $daysDiff <= 4) {
                $warna = 'kuning';
            }
        } elseif ($daysDiff > 0) {
            $warna = 'merah';
        }

        $row['warna_status'] = $warna;
        return $row;
    });

    // Filter warna_status dari jqGrid
    if ($request->filled('filters')) {
        $filterRules = json_decode($request->filters, true)['rules'] ?? [];
        foreach ($filterRules as $rule) {
            if ($rule['field'] === 'warna_status') {
                $value = $rule['data'];
                $data = $data->filter(fn($row) => $row['warna_status'] === $value)->values();
            }
        }
    }

    // 🔍 Filter nominal jika ada
    if ($inputVal !== '') {
        $data = $data->filter(function ($row) use ($inputVal) {
            return (int) $row['kurang_bayar'] == (int) $inputVal;
        })->values();
    }

    // Pagination
    $currentPage = $request->input('page', 1);
    $perPage = $request->input('rows', 20);
    $totalRecords = $data->count();
    $indexStart = ($currentPage - 1) * $perPage;

    $paginated = $data->slice($indexStart, $perPage)->values()->map(function ($row, $idx) use ($indexStart) {
        $row['no'] = $indexStart + $idx + 1;
        return $row;
    });

    return response()->json([
        'rows' => $paginated,
        'current_page' => $currentPage,
        'last_page' => ceil($totalRecords / $perPage),
        'total' => $totalRecords,
        'records' => $totalRecords,
        'debug_warna' => $paginated->pluck('warna_status'),
    ]);
}



public function dataLapPiutangTotal(Request $request)
{
    $page = $request->input('page', 1);
    $rows = $request->input('rows', 20);
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');

    // Ambil data invoice
    $invoiceQuery = Invoice::with([
        'transaksi.suratJalan.customer:id,nama',
        'transaksi.barang'
    ])->where('tgl_invoice', '>', '2025-01-01');

    if ($searchField && $searchString) {
        $invoiceQuery->where($searchField, 'like', "%$searchString%");
    }

    $invoices = $invoiceQuery->orderBy('created_at', 'desc')->get();

    // Ambil jurnal invoice
    $jurnals = Jurnal::withTrashed()
        ->whereIn('tipe', ['BBM','BBMN'])
        ->whereNull('deleted_at')
        ->where('debit', '!=', 0)
        ->where('tgl', '>', '2025-01-01')
        ->whereNotNull('invoice')
        ->orderBy('tgl', 'desc')
        ->get()
        ->groupBy('invoice'); // Dikelompokkan berdasarkan invoice

    // Group invoice berdasarkan bulan
    $data = $invoices->groupBy(function ($invoice) {
        return Carbon::parse($invoice->tgl_invoice)->format('Y-m');
    })->map(function ($group) use ($jurnals) {
        $subtotal = $group->sum('subtotal');
        $ppn = $group->sum(function ($invoice) {
            $barang = $invoice->transaksi->barang;
            return ($barang && $barang->status_ppn === 'ya') 
                ? $invoice->subtotal * ($barang->value_ppn / 100) 
                : 0;
        });

        $jumlah_harga = round($subtotal + $ppn);

        // Ambil list invoice yang unik untuk menghitung jurnalnya
        $invoiceNos = $group->pluck('invoice')->filter()->unique();
        $telah_bayar = $invoiceNos->sum(function ($inv) use ($jurnals) {
            return $jurnals->get($inv)?->sum('debit') ?? 0;
        });

        $belum_dibayar = $jumlah_harga - $telah_bayar;

        return [
            'bulan' => Carbon::parse($group->first()->tgl_invoice)->format('Y-m'),
            'nilai_invoice' => $jumlah_harga,
            'total_invoice' => $invoiceNos->count(),
            'telah_bayar' => $telah_bayar,
            'belum_dibayar' => $belum_dibayar,
        ];
    });

    // Menambahkan nomor urut + summary
    $result = [];
    $index = 1;
    $totalTelahBayar = 0;
    $totalBelumBayar = 0;
    $totalInvoice = 0;
    $nilaiInvoice = 0;

    foreach ($data as $item) {
        $item['no'] = $index++;
        $totalTelahBayar += $item['telah_bayar'];
        $totalBelumBayar += $item['belum_dibayar'];
        $totalInvoice += $item['total_invoice'];
        $nilaiInvoice += $item['nilai_invoice'];
        $result[] = $item;
    }

    // Pagination
    $indexStart = ($page - 1) * $rows;
    $paginatedData = collect($result)->slice($indexStart, $rows)->values();
    $totalRecords = count($result);
    $totalPages = ceil($totalRecords / $rows);

    return response()->json([
        'rows' => $paginatedData,
        'current_page' => $page,
        'last_page' => $totalPages,
        'total' => $totalPages,
        'records' => $totalRecords,
        'sum_telah_bayar' => $totalTelahBayar,
        'sum_belum_bayar' => $totalBelumBayar,
        'count_invoice' => $totalInvoice,
        'sum_nilai_invoice' => $nilaiInvoice
    ]);
}



public function lapPiutang()
{
    // Ambil tahun unik dari invoice
    $customers = Customer::all();
    $years = Invoice::selectRaw('YEAR(tgl_invoice) as year')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->pluck('year');

    // Ambil data invoice tahun tertentu
   $invoiceData = Invoice::selectRaw('
        i.invoice,
        c.id as customer_id,
        c.nama as customer_nama,
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        COUNT(DISTINCT i.invoice) as invoice_count,  
        SUM(
            CASE 
                WHEN b.status_ppn = "ya" THEN i.subtotal * 1.11
                ELSE i.subtotal
            END
        ) as total_omzet
    ')
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
    ->join('surat_jalan as sj', 't.id_surat_jalan', '=', 'sj.id')
    ->join('customer as c', 'sj.id_customer', '=', 'c.id')
    ->join('barang as b', 't.id_barang', '=', 'b.id') 
    ->whereYear('i.tgl_invoice', request('year') ?? 2025)
    ->when(request('customers'), function ($query) {
        $query->where('c.id', request('customers'));
    })
    ->groupBy('i.invoice', 'c.id', 'c.nama', 'year', 'month')
    ->get();

$jurnals = Jurnal::withTrashed()
    ->whereIn('tipe', ['BBM','BBMN'])
    ->whereNull('deleted_at')
    ->where('debit', '!=', 0)
    ->where('tgl', '>', (request('year') ?? 2025) . '-01-01')
    ->whereNotNull('invoice')
    ->when(request('customers'), function ($query) {
        $query->whereHas('transaksi.suratJalan.customer', function ($q) {
            $q->where('id', request('customers'));
        });
    })
    ->orderBy('tgl', 'desc')
    ->get()
    ->groupBy('invoice');

    $mergedResults = [];
$monthlyTotals = [];
$monthlyInvoiceCounts = [];
$monthlySelisihInvoice = [];

foreach ($invoiceData as $invoice) {
    $invoiceNumber = $invoice->invoice;
    $omzet = $invoice->total_omzet;

    // Jumlah jurnal yang cocok
    $jurnalInvoiceCount = $jurnals->has($invoiceNumber) ? 1 : 0;
    $selisihInvoice = $invoice->invoice_count - $jurnalInvoiceCount;

    $debitValue = $jurnals->has($invoiceNumber) ? $jurnals[$invoiceNumber]->sum('debit') : 0;
    $netOmzet = ($omzet - $debitValue) / 1000;

    // Buat struktur data
    $custId = $invoice->customer_id;
    $year = $invoice->year;
    $month = $invoice->month;

    if (!isset($mergedResults[$custId])) {
        $mergedResults[$custId] = [
            'customer_name' => $invoice->customer_nama,
            'years' => []
        ];
    }

    if (!isset($mergedResults[$custId]['years'][$year])) {
        $mergedResults[$custId]['years'][$year] = [];
    }

    if (!isset($mergedResults[$custId]['years'][$year][$month])) {
        $mergedResults[$custId]['years'][$year][$month] = [
            'month' => $month,
            'year' => $year,
            'invoice_count' => 0,
            'omzet' => 0,
            'selisih_invoice' => 0,
        ];
    }

    // Tambahkan data per customer
    $mergedResults[$custId]['years'][$year][$month]['omzet'] += $netOmzet;
    $mergedResults[$custId]['years'][$year][$month]['invoice_count'] += $invoice->invoice_count;
    $mergedResults[$custId]['years'][$year][$month]['selisih_invoice'] += $selisihInvoice;

    // Akumulasi total per bulan
    if (!isset($monthlyTotals[$year][$month])) {
        $monthlyTotals[$year][$month] = 0;
    }
    $monthlyTotals[$year][$month] += $netOmzet;

    if (!isset($monthlyInvoiceCounts[$year][$month])) {
        $monthlyInvoiceCounts[$year][$month] = 0;
    }
    $monthlyInvoiceCounts[$year][$month] += $invoice->invoice_count;

    if (!isset($monthlySelisihInvoice[$year][$month])) {
        $monthlySelisihInvoice[$year][$month] = 0;
    }
    $monthlySelisihInvoice[$year][$month] += $selisihInvoice;
}

// 🔽 Hitung total omzet per customer
$customerOmzetTotals = [];

foreach ($mergedResults as $custId => $data) {
    $totalOmzet = 0;
    foreach ($data['years'] as $yearData) {
        foreach ($yearData as $monthData) {
            $totalOmzet += $monthData['omzet'];
        }
    }
    $customerOmzetTotals[$custId] = $totalOmzet;
}

// 🔽 Urutkan berdasarkan total omzet (descending)
uksort($mergedResults, function ($a, $b) use ($customerOmzetTotals) {
    return $customerOmzetTotals[$b] <=> $customerOmzetTotals[$a];
});


$months = [
    'January', 'February', 'March', 'April', 'May',
    'June', 'July', 'August', 'September',
    'October', 'November', 'December'
];
    if (request()->ajax()) {
        $html = view('jurnal.partials.lap-piutang-table', [
            'mergedResults' => $mergedResults,
            'monthlyInvoiceCounts' => $monthlyInvoiceCounts,
            'monthlySelisihInvoice' => $monthlySelisihInvoice,
            'monthlyTotals' => $monthlyTotals,
            'months' => $months,
            'year' => request('year') ?? 2025
        ])->render();

        return response()->json(['tableHtml' => $html]);
    }

    return view('jurnal.lap-piutang', compact(
        'customers',
        'mergedResults',
        'monthlyInvoiceCounts',
        'monthlySelisihInvoice',
        'monthlyTotals',
        'months',
        'years'
    ));
}



    public function lapPenjualan()
    {
        return view('laporan.lap-penjualan-harian');
    }

//     public function dataPenjualanHarian(Request $request)
// {
//     // Ambil parameter untuk pagination dari request
//       // Filter berdasarkan kolom pencarian
//       $searchField = $request->input('searchField');
//       $searchString = $request->input('searchString');
  
//       // Query data berdasarkan filter dan pagination
//       $query = Invoice::query();
  
//       if ($searchField && $searchString) {
//           $query->where($searchField, 'like', "%$searchString%");
//       }
//     // Ambil data dari tabel Jurnals dengan pagination, urutkan berdasarkan 'tgl' descending

//     // Ambil data dari tabel Invoices, urutkan berdasarkan 'created_at' descending
//     $invoices = Invoice::with([
//         'transaksi.suratJalan.customer' => function($query) {
//             $query->select('id', 'nama');
//         },
//         'transaksi.barang' // Menambahkan relasi transaksi.barang
//     ])
//     ->where('tgl_invoice', '>', '2025-01-01')
//     ->orderBy('created_at', 'desc')
//     ->get();

//     // Mengelompokkan dan menghitung subtotal untuk setiap invoice
//     $data = $invoices->groupBy('tgl_invoice')->map(function($group) {
//         $subtotal = $group->sum('subtotal'); // Jumlahkan subtotal untuk setiap invoice
//         $ppn = 0; // Inisialisasi PPN

//         // Menghitung PPN jika ada barang dengan status_ppn == 'ya'
//         foreach ($group as $invoice) {
//             $barang = $invoice->transaksi->barang;
//             $nama_barang = $invoice->transaksi->barang->nama;
//             if ($barang && $barang->status_ppn == 'ya') {
//                 $ppn += $invoice->subtotal * ($barang->value_ppn / 100); // Menghitung PPN
//             }
//         }
//         $jumlah_harga = round($subtotal + $ppn);
//         $customer = $group->first()->transaksi->suratJalan->customer->nama;
//         $top = Customer::where('nama', $customer)->pluck('top')->first();
//         return [
//             'tgl_invoice' => $group->first()->tgl_invoice,
//             'invoice' => implode('', array_map(fn($inv) => "<div style='border: 1px solid #ccc; padding: 5px; margin-bottom: 2px;'>$inv</div>", $group->pluck('invoice')->unique()->toArray())),
//            'tagihan' => implode('', array_map(fn($inv) => 
//     "<div style='border: 1px solid #ccc; padding: 5px; margin-bottom: 2px;'>" . 
//     number_format((float) $inv, 2, ',', '.') . 
//     "</div>", 
//     array_filter($group->pluck('subtotal')->unique()->toArray(), fn($val) => is_numeric($val)))
// ),

//             'barang' => implode('', array_map(fn($item) => "<div style='border: 1px solid #ccc; padding: 5px; margin-bottom: 2px;'>$item</div>", $group->pluck('transaksi.barang.nama')->toArray())),
//             'customer' => implode('', array_map(fn($name) => "<div style='border: 1px solid #ccc; padding: 5px; margin-bottom: 2px;'>$name</div>", $group->pluck('transaksi.suratJalan.customer.nama')->unique()->toArray())),
//             'jumlah_harga' => $jumlah_harga,
//         ];
//     });

//     // Menambahkan nomor urut
//     $result = [];
//     $index = 1;
//     foreach ($data as $item) {
//         $item['no'] = $index++;
//         $result[] = $item;
//     }
    
//     // Pagination
//     $currentPage = $request->input('page', 1); // Halaman saat ini, default 1
//     $perPage = $request->input('rows', 20); // Jumlah baris per halaman, default 10
//     $totalRecords = count($result);
//     $totalPages = ceil($totalRecords / $perPage);
//     $indexStart = ($currentPage - 1) * $perPage;
//     $paginatedData = collect($result)->slice($indexStart)->values();
//     $data = $paginatedData->map(function($row) use (&$indexStart) {
//         $indexStart++;
//         return [
//             'tgl_invoice' => $row['tgl_invoice'],
//             'tagihan' => $row['tagihan'],
//             'invoice' => $row['invoice'], // Mengakses dengan notasi array
//             'customer' => $row['customer'],
//             'jumlah_harga' => $row['jumlah_harga'],
//             'barang' => $row['barang'],
//             'no' => $indexStart, // Menggunakan nomor urut
//         ];
//     });
//     return response()->json([
//         'rows' => $data,
//         'current_page' => $currentPage, // Halaman saat ini
//         'last_page' => ceil($totalRecords / $perPage), // Total halaman
//         'total' => $totalRecords, // Total record setelah filter
//         'records' => $totalRecords,
//     ]);
// }

public function dataPenjualanHarian(Request $request)
{
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');

    $query = Invoice::with([
        'transaksi.suratJalan.customer:id,nama',
        'transaksi.barang'
    ])
    ->where('tgl_invoice', '>', '2025-01-01')
    ->orderBy('tgl_invoice', 'desc')
    ->orderBy('created_at', 'desc');

    if ($searchField && $searchString) {
        $query->where($searchField, 'like', "%$searchString%");
    }

    $invoices = $query->get();

    // Hitung jumlah invoice per tanggal dan total subtotal
    $invoiceGroups = $invoices->groupBy('tgl_invoice')->map(function ($items) {
        return [
            'count' => $items->count(),
            'total' => $items->sum(function ($item) {
                $subtotal = $item->subtotal;

                // Tambahkan PPN jika barang memiliki status_ppn "ya"
                if ($item->transaksi->barang && $item->transaksi->barang->status_ppn === 'ya') {
                    $subtotal += $subtotal * 0.11; // Tambahkan PPN 11%
                }

                return $subtotal;
            })
        ];
    });

    // Kelompokkan berdasarkan invoice & ambil distinct customer + barang
    $groupedInvoices = $invoices->groupBy('invoice')->map(function ($items) {
        $firstInvoice = $items->first();
        
        return [
            'tgl_invoice' => $firstInvoice->tgl_invoice,
            'invoice' => $firstInvoice->invoice,
            'customer' => $items->pluck('transaksi.suratJalan.customer.nama')->unique()->implode(', '),
            'tagihan' => $items->sum('subtotal'),
            'barang' => $items->pluck('transaksi.barang.nama')->unique()->implode(', '),
            'subtotal' => $firstInvoice->subtotal,
            'hasPPN' => $items->pluck('transaksi.barang.status_ppn')->contains('ya')
        ];
    })->values(); // Reset indeks array

    // Tambahkan nomor urut & hitung tagihan
    $result = [];
    $seenDates = [];
    foreach ($groupedInvoices as $index => $item) {
        // Ambil nilai asli tagihan sebelum formatting
        $tagihan = $item['tagihan'];
    
        // Jika ada barang dengan status_ppn = 'ya', tambahkan PPN 11%
        if ($item['hasPPN']) {
            $tagihan = round($tagihan * 1.11);
        }
    
        // Format tagihan setelah perhitungan
        $formattedTagihan = number_format($tagihan, 0, '.', ',');
    
        // Tampilkan jumlah_harga hanya untuk baris pertama setiap tgl_invoice
        if (!isset($seenDates[$item['tgl_invoice']])) {
            $jumlah_harga = round($invoiceGroups[$item['tgl_invoice']]['total']);
            $seenDates[$item['tgl_invoice']] = true;
        } else {
            $jumlah_harga = null;
        }
    
        $result[] = [
            'no' => $index + 1,
            'tgl_invoice' => $item['tgl_invoice'],
            'invoice' => $item['invoice'],
            'customer' => $item['customer'],
            'barang' => $item['barang'],
            'tagihan' => $formattedTagihan,
            'jumlah_harga' => $jumlah_harga // Tampilkan hanya di baris pertama
        ];
    }    

    return response()->json([
        'rows' => $result,
        'records' => count($result),
        'total' => 1, // loadonce butuh total = 1
        'page' => 1
    ]);
}


    }
