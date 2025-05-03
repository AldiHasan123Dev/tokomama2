<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Supplier;
use PDF;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Support\Collection;
use App\Models\Transaction;

use App\Models\Jurnal;

class StockController extends Controller
{
    public function dataStock()
    {
        // Ambil data stok dari database dengan relasi 'barang' dan 'suppliers'
        $stocks = Transaction::selectRaw(
            'transaksi.*, 
             id_barang, 
             SUM(jumlah_beli) as total_beli, 
             SUM(jumlah_jual) as total_jual, 
             SUM(sisa) as sisa,
             SUM(harga_beli) as total_harga_beli,
             SUM(harga_jual) as total_harga_jual,
             (SUM(harga_jual) - SUM(harga_beli)) as total_profit'
        )
        ->with('barang') // Ambil relasi barang
        ->groupBy('no_bm') // Grup berdasarkan kondisi
        ->whereNull('id_surat_jalan')
        ->whereNotNull('no_bm')
        ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at
        ->get();
        
        // Mendapatkan jumlah total record
        $totalRecords = $stocks->count();
    
        // Mendapatkan parameter untuk pagination (per_page dan page)
        $perPage = request('per_page', $totalRecords);
        $currentPage = request('page', 1);
    
        // Menghitung offset berdasarkan halaman yang aktif
        $index = ($currentPage - 1) * $perPage; // Mulai dari index yang benar
        $index++; // Inisialisasi untuk mulai menghitung nomor urut dari 1 atau lebih tinggi
    
        // Menentukan data yang akan ditampilkan pada halaman ini
        $paginatedData = $stocks->forPage($currentPage, $perPage); 
    
        // Format data sesuai kebutuhan jqGrid dengan menambahkan nomor urut ($index)
        $formattedStocks = $paginatedData->map(function ($stock) use (&$index) {
            $cetakNotaUrl = route('cetak_nota.cetak', $stock->no_bm);
            return [
                'id' => $stock->id,
                'aksi' => '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-blue-700 m-1" ' .
                'onclick="window.open(\'' . $cetakNotaUrl . '\', \'_blank\')">' .
                'Cetak Nota</button>',
                'no_bm' => $stock->no_bm ?? '-',
               'vol_bm' => number_format($stock->total_beli, 0, ',', '.') ?? 0,
                'tgl_masuk' => $stock->tgl_bm?? 0,
                'harga_beli' => $stock->total_harga_beli?? 0,
                'sisa' => $stock->sisa ?? 0, // Format aktif jadi Yes/No
                'index' => $index++ // Menambahkan index nomor urut
            ];
        });
    
        // Menentukan total halaman berdasarkan total record dan per halaman
        $totalPages = ceil($totalRecords / $perPage);
    
        // Format respons JSON untuk jqGrid
        $data = [
            'page' => $currentPage, // Halaman saat ini
            'total' => $totalPages, // Total halaman
            'records' => $totalRecords, // Total jumlah data
            'rows' => $formattedStocks // Data yang diformat
        ];
    
        // Return respons JSON
        return response()->json($data);
    }
    public function cetak($no_bm)
    {
        // Ambil data berdasarkan no_bm
        $stocks = Transaction::where('no_bm', $no_bm)->whereNull('id_surat_jalan')->get();
        
        if ($stocks->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Hitung total harga beli dan volume beli
        $totalHargaBeli = $stocks->sum('harga_beli');
        $totalVolumeBeli = $stocks->sum('jumlah_beli');

        // Buat PDF
        $pdf = PDF::loadView('toko.nota', [
            'stocks' => $stocks,
            'totalHargaBeli' => $totalHargaBeli,
            'totalVolumeBeli' => $totalVolumeBeli
        ]);

        return $pdf->stream("nota_$no_bm.pdf");
    }
    

    public function dataStock1()
    {
        // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
        $stocks = Transaction::selectRaw(
            'transaksi.*'
        )
        ->with('jurnals')
        ->whereNull('id_surat_jalan')
        ->where('harga_beli', '>', 0) // Pastikan harga_beli lebih dari 0 // Grup berdasarkan kondisi
        ->orderBy('no_bm', 'desc') // Urutkan berdasarkan created_at
        ->get();

        // Hitung total records
        $totalRecords = $stocks->count();
        $perPage = request('per_page', $totalRecords);
        $currentPage = request('page', 1);
    
        // Tentukan offset untuk pagination
        $offset = ($currentPage - 1) * $perPage;
    
        // Data paginasi
        $paginatedData = $stocks->slice($offset, $perPage);
    
        // Format data untuk ditampilkan
        $index = $offset + 1; // Mulai nomor urut sesuai offset
        $formattedStocks = $paginatedData->map(function ($stock) use (&$index) {
            return [
                'id' => $stock->id,
                'satuans' => $stock->satuan_beli,
               'jurnal' => optional($stock->jurnals->firstWhere('coa_id', 35))->nomor ?? '-',
                'barangs' => $stock->barang->nama,
                'jumlah_belis' => $stock->jumlah_beli,
                'lock' => $stock->stts ?? $this->getJumlahBeli($stock),
                'status' => $stock->stts ?? '-',
                'invoice_external' => $stock->invoice_external,
                'no_bm' => $stock->no_bm,
                'satuan_beli' => $stock->satuan_beli,
                'satuan_jual' => $stock->satuan_jual,
                'supplier' => $stock->suppliers->nama ?? '-',
                'barang.nama' => $stock->barang->nama ?? '-', // Nama barang
                'total_beli' => $stock->jumlah_beli, // Total jumlah beli
                'total_jual' => $stock->jumlah_jual,
                'total_harga_beli' => $stock->harga_beli,
                'total_harga_jual' => $stock->harga_jual,
                'total_profit' => $stock->total_profit, // Total jumlah jual
                'sisa' => $stock->sisa, // Stok tersisa
                'index' => $index++ // Nomor urut
            ];
        });
    
        // Hitung total halaman
        $totalPages = ceil($totalRecords / $perPage);
    
        // Format data untuk jqGrid
        $data = [
            'page' => $currentPage,
            'total' => $totalPages,
            'records' => $totalRecords,
            'rows' => $formattedStocks
        ];
    
        // Return JSON response
        return response()->json($data);
    }


public function stockCSV19()
{
    $filename = "STOCK" . date('Ymd_His') . ".csv";

    $query = "
        SELECT 
            j.tgl AS tgl_bm,
            b.nama AS nama_barang,  
            s.nama AS nama_supplier,  
            CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END AS jumlah_beli,
            t.harga_beli,
            CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END AS jumlah_jual,
            i.tgl_invoice,  
            i.invoice,
            t.invoice_external,
            COALESCE(j.tgl, i.tgl_invoice) AS tgl_semua,  
            t.stts,
            ((CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END) * t.harga_beli) 
            - 
            ((CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END) * t.harga_beli) 
            AS nilai_persediaan
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        JOIN suppliers s ON t.id_supplier = s.id
        LEFT JOIN invoice i ON t.id = i.id_transaksi
        LEFT JOIN jurnal j ON j.id_transaksi = t.id  
            AND j.coa_id = 89  
            AND j.debit > 0  
        WHERE YEAR(COALESCE(j.tgl, i.tgl_invoice)) >= 2025  
        AND t.stts IS NOT NULL  

        UNION ALL

        SELECT 
            NULL AS tgl_bm,
            CONCAT('ZZZStock_', b.nama) AS nama_barang,
            s.nama AS nama_supplier,
            SUM(CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END) AS jumlah_beli,
            (SELECT t2.harga_beli FROM transaksi t2 WHERE t2.invoice_external = t.invoice_external AND t2.id_barang = t.id_barang LIMIT 1) AS harga_beli,
            SUM(CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END) AS jumlah_jual,
            DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m-01') AS tgl_invoice,
            t.invoice_external AS invoice,
            t.invoice_external,
            DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m-01') AS tgl_semua,
            NULL AS stts,
            NULL AS nilai_persediaan
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        JOIN suppliers s ON t.id_supplier = s.id  
        LEFT JOIN invoice i ON t.id = i.id_transaksi
        LEFT JOIN jurnal j ON j.id_transaksi = t.id  
            AND j.coa_id = 89  
            AND j.debit > 0  
        WHERE YEAR(COALESCE(j.tgl, i.tgl_invoice)) >= 2025  
        AND t.stts IS NOT NULL  
        GROUP BY DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m'), t.invoice_external, b.nama, s.nama  
        ORDER BY tgl_semua ASC, invoice_external, nama_barang, nama_supplier;
    ";

    $data = DB::select($query);

    $response = new StreamedResponse(function () use ($data) {
        $handle = fopen('php://output', 'w');

        // Tambahkan BOM agar Excel membaca UTF-8 dengan benar
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Tambahkan header kolom
        fputcsv($handle, [
            'Tanggal BM', 'Nama Barang', 'Nama Supplier', 'Jumlah Beli', 
            'Harga Beli', 'Jumlah Jual', 'Tanggal Invoice', 'Invoice', 
            'Invoice External', 'Tanggal Semua', 'Status', 'Nilai Persediaan'
        ], ';'); // Gunakan titik koma sebagai pemisah

        // Tambahkan data
        foreach ($data as $row) {
            fputcsv($handle, [
                $row->tgl_bm, $row->nama_barang, $row->nama_supplier, $row->jumlah_beli, 
                $row->harga_beli, $row->jumlah_jual, $row->tgl_invoice, $row->invoice, 
                $row->invoice_external, $row->tgl_semua, $row->stts, $row->nilai_persediaan
            ], ';'); // Gunakan titik koma sebagai pemisah
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    return $response;
}


    public function stockCSV()
    {
        $filename = "STOCK" . date('Ymd_His') . ".csv";

        $query = "
            SELECT 
                j.tgl AS tgl_bm,
                b.nama AS nama_barang,  
                s.nama AS nama_supplier,  
                CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END AS jumlah_beli,
                t.harga_beli,
                CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END AS jumlah_jual,
                i.tgl_invoice,  
                i.invoice,
                t.invoice_external,
                COALESCE(j.tgl, i.tgl_invoice) AS tgl_semua,  
                t.stts,
                ((CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END) * t.harga_beli) 
                - 
                ((CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END) * t.harga_beli) 
                AS nilai_persediaan
            FROM transaksi t
            JOIN barang b ON t.id_barang = b.id
            JOIN suppliers s ON t.id_supplier = s.id
            LEFT JOIN invoice i ON t.id = i.id_transaksi
            LEFT JOIN jurnal j ON j.id_transaksi = t.id  
                AND j.coa_id = 89  
                AND j.debit > 0  
            WHERE YEAR(COALESCE(j.tgl, i.tgl_invoice)) >= 2025  
            AND t.stts IS NOT NULL  

            UNION ALL

            SELECT 
                NULL AS tgl_bm,
                CONCAT('ZZZStock_', b.nama) AS nama_barang,
                s.nama AS nama_supplier,
                SUM(CASE WHEN j.tgl IS NULL THEN 0 ELSE t.jumlah_beli END) AS jumlah_beli,
                (SELECT t2.harga_beli FROM transaksi t2 WHERE t2.invoice_external = t.invoice_external AND t2.id_barang = t.id_barang LIMIT 1) AS harga_beli,
                SUM(CASE WHEN j.tgl IS NOT NULL THEN 0 ELSE t.jumlah_jual END) AS jumlah_jual,
                DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m-01') AS tgl_invoice,
                t.invoice_external AS invoice,
                t.invoice_external,
                DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m-01') AS tgl_semua,
                NULL AS stts,
                NULL AS nilai_persediaan
            FROM transaksi t
            JOIN barang b ON t.id_barang = b.id
            JOIN suppliers s ON t.id_supplier = s.id  
            LEFT JOIN invoice i ON t.id = i.id_transaksi
            LEFT JOIN jurnal j ON j.id_transaksi = t.id  
                AND j.coa_id = 89  
                AND j.debit > 0  
            WHERE YEAR(COALESCE(j.tgl, i.tgl_invoice)) >= 2025  
            AND t.stts IS NOT NULL  
            GROUP BY DATE_FORMAT(COALESCE(j.tgl, i.tgl_invoice), '%Y-%m'), t.invoice_external, b.nama, s.nama  
            ORDER BY tgl_semua ASC, invoice_external, nama_barang, nama_supplier;
        ";

        $data = DB::select($query);

        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Tambahkan header kolom
            fputcsv($handle, [
                'Tanggal BM', 'Nama Barang', 'Nama Supplier', 'Jumlah Beli', 
                'Harga Beli', 'Jumlah Jual', 'Tanggal Invoice', 'Invoice', 
                'Invoice External', 'Tanggal Semua', 'Status', 'Nilai Persediaan'
            ]);

            // Tambahkan data
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->tgl_bm, $row->nama_barang, $row->nama_supplier, $row->jumlah_beli, 
                    $row->harga_beli, $row->jumlah_jual, $row->tgl_invoice, $row->invoice, 
                    $row->invoice_external, $row->tgl_semua, $row->stts, $row->nilai_persediaan
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    public function dataStock2()
    {
        // Ambil parameter pagination
        
        // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
        $stocks = Transaction::selectRaw(
            'transaksi.*'
        )
        ->with('jurnals')
        ->whereNull('id_surat_jalan')
        ->where('harga_beli', '>', 0)
        ->orderBy('no_bm', 'desc') // Pastikan harga_beli lebih dari 0 // Grup berdasarkan kondisi // Urutkan berdasarkan created_at
        ->get();
        
        // Hitung total records
        $totalRecords = $stocks->count();
        $perPage = request('per_page', $totalRecords);
        $currentPage = request('page', 1);
    
        // Tentukan offset untuk pagination
        $offset = ($currentPage - 1) * $perPage;
    
        // Data paginasi
        $paginatedData = $stocks->slice($offset, $perPage);
    
        // Format data untuk ditampilkan
        $index = $offset + 1; // Mulai nomor urut sesuai offset
        $formattedStocks = $paginatedData->map(function ($stock) use (&$index) {
            return [
                'id' => $stock->transaksi_ids,
                'satuans' => $stock->satuans,
                'barangs' => $stock->barangs,
                'jumlah_belis' => $stock->jumlah_belis,
                'lock' => $stock->stts ?? $this->getJumlahBeli($stock),
                'status' => $stock->stts ?? '-',
                'tgl_jurnal' =>  optional($stock->jurnals->firstWhere('coa_id', 89))->tgl ?? '-' ?? '-',
                'jurnal' =>  optional($stock->jurnals->firstWhere('coa_id', 89))->nomor ?? '-' ?? '-',
                'invoice_external' => $stock->invoice_external,
                'no_bm' => $stock->no_bm,
                'gudang' => $stock->gudang,
                'satuan_beli' => $stock->satuan_beli,
                'satuan_jual' => $stock->satuan_jual,
                'supplier' => $stock->suppliers->nama ?? '-',
                'barang.nama' => $stock->barang->nama ?? '-', // Nama barang
                'total_beli' => $stock->jumlah_beli, // Total jumlah beli
                'total_jual' => $stock->jumlah_jual,
                'total_harga_beli' => $stock->harga_beli,
                'total_harga_jual' => $stock->harga_jual,
                'total_profit' => $stock->total_profit, // Total jumlah jual
                'sisa' => $stock->sisa, // Stok tersisa
                'index' => $index++ // Nomor urut
            ];
        });
    
        // Hitung total halaman
        $totalPages = ceil($totalRecords / $perPage);
    
        // Format data untuk jqGrid
        $data = [
            'page' => $currentPage,
            'total' => $totalPages,
            'records' => $totalRecords,
            'rows' => $formattedStocks
        ];
    
        // Return JSON response
        return response()->json($data);
    }

    public function cetak_nota(){
        return view('toko.list-barang');
    }
    public function monitor_stock(Request $request){
        $jayapura = Transaction::whereNotNull('stts')
        ->whereNull('id_surat_jalan')
    ->get()
    ->sum(function ($transaction) {
        return $transaction->harga_beli * $transaction->sisa;
    });
    $perjalanan = Transaction::whereNull('stts')
    ->whereNull('id_surat_jalan')
    ->get()
    ->sum(function ($transaction) {
        return $transaction->harga_beli * $transaction->sisa;
    });
    $perjalanan1 = Transaction::whereNull('stts')
    ->whereNull('id_surat_jalan')
    ->whereNull('invoice_external')
    ->get()
    ->sum(function ($transaction) {
        return $transaction->harga_beli * $transaction->sisa;
    });
    $perjalanan2 = Transaction::whereNull('stts')
    ->whereNull('id_surat_jalan')
    ->whereNotNull('invoice_external')
    ->get()
    ->sum(function ($transaction) {
        return $transaction->harga_beli * $transaction->sisa;
    });

    $barang = Barang::with('satuan')->where('status', 'aktif')->orderBy('nama', 'asc')->get();

    // Ambil parameter dari request
    $barang_id = $request->get('barang') ?? null;
    $month = $request->get('month') ?? null; // Default ke bulan Maret
    $year = $request->get('year', 2025);

    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    if($barang_id === null) {
        $data = Transaction::with('barang', 'suratJalan')
        ->whereNull('id_surat_jalan')
        ->whereNotNull('no_bm')
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();

        $data2 = Transaction::with('barang', 'suratJalan')
        ->whereNull('id_surat_jalan')
        ->whereNotNull('no_bm')
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();
        
        $data1 = Transaction::with('barang', 'suratJalan')
        ->whereNotNull('no_bm')
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();

        $data3 = Transaction::with('barang', 'suratJalan')
        ->whereNotNull('no_bm')
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();
    } else{

        $data = Transaction::with('barang', 'suratJalan')
        ->whereNull('id_surat_jalan')
        ->whereBetween('tgl_bm', [
           '2025-01-01',
            \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth(),
        ])
        ->whereNotNull('no_bm')
        ->when($barang_id, function ($q) use ($barang_id) {
            $q->where('id_barang', $barang_id);
        })
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();

        $data2 = Transaction::with('barang', 'suratJalan', 'invoices')
    ->whereNull('id_surat_jalan')
    ->whereYear('tgl_bm', $year)
    ->whereMonth('tgl_bm', $month)
    ->whereNotNull('no_bm')
    ->when($barang_id, function ($q) use ($barang_id) {
        $q->where('id_barang', $barang_id);
    })
    ->orderBy('created_at', 'asc')
    ->orderBy('no_bm', 'asc')
    ->get();
    
        // Query transaksi barang keluar (ada surat jalan di bulan & tahun tertentu)
        $data1 = Transaction::with('barang', 'suratJalan')
        ->whereHas('suratJalan', function ($q) use ($month, $year) {
            $q->whereBetween('tgl_sj', [
               '2025-01-01',
                \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth(),
            ]);
        })
        ->whereNotNull('no_bm')
        ->when($barang_id, function ($q) use ($barang_id) {
            $q->where('id_barang', $barang_id);
        })
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();

        $data3 = Transaction::with('barang', 'suratJalan', 'invoices')
        ->whereHas('suratJalan', function ($q) use ($year, $month) {
            $q->whereYear('tgl_sj', $year)
              ->whereMonth('tgl_sj', $month);
        })
        ->whereNotNull('no_bm')
        ->when($barang_id, function ($q) use ($barang_id) {
            $q->where('id_barang', $barang_id);
        })
        ->orderBy('created_at', 'asc')
        ->orderBy('no_bm', 'asc')
        ->get();
    }
    $hasil = [];

// Barang masuk (data)
foreach ($data as $item) {
    if (!$item->tgl_bm) continue;

    $id = $item->id_barang;
    $tgl = $item->tgl_bm;
    $bulanIndex = (int) date('n', strtotime($tgl)) - 1;

    if (!isset($hasil[$id])) {
        $hasil[$id] = [
            'barang' => $item->barang->nama ?? '-',
            'detail' => []
        ];
    }

    if (!isset($hasil[$id]['detail'][$bulanIndex])) {
        $hasil[$id]['detail'][$bulanIndex] = [
            'jumlah_beli' => 0,
            'jumlah_jual' => 0,
            'sisa_stock' => 0,
            'stock_awal' => 0,
            'tgl' => $tgl,
            'bulan' => $months[$bulanIndex] ?? '-',
            'index_bulan' => $bulanIndex
        ];
    }

    $hasil[$id]['detail'][$bulanIndex]['jumlah_beli'] += $item->jumlah_beli;

    // Update stock_awal dari bulan sebelumnya jika ada
    if ($bulanIndex > 0 && isset($hasil[$id]['detail'][$bulanIndex - 1])) {
        $hasil[$id]['detail'][$bulanIndex]['stock_awal'] = $hasil[$id]['detail'][$bulanIndex - 1]['sisa_stock'];
    }

    $hasil[$id]['detail'][$bulanIndex]['sisa_stock'] =
        $hasil[$id]['detail'][$bulanIndex]['stock_awal'] +
        $hasil[$id]['detail'][$bulanIndex]['jumlah_beli'];
}

// Barang keluar (data1)
foreach ($data1 as $item) {
    if (!$item->suratJalan || !$item->suratJalan->tgl_sj) continue;

    $id = $item->id_barang;
    $tgl = $item->suratJalan->tgl_sj;
    $bulanIndex = (int) date('n', strtotime($tgl)) - 1;

    if (!isset($hasil[$id])) {
        $hasil[$id] = [
            'barang' => $item->barang->nama ?? '-',
            'detail' => []
        ];
    }

    if (!isset($hasil[$id]['detail'][$bulanIndex])) {
        $hasil[$id]['detail'][$bulanIndex] = [
            'jumlah_beli' => 0,
            'jumlah_jual' => 0,
            'sisa_stock' => 0,
            'stock_awal' => 0,
            'tgl' => $tgl,
            'bulan' => $months[$bulanIndex] ?? '-',
            'index_bulan' => $bulanIndex
        ];
    }

    $hasil[$id]['detail'][$bulanIndex]['jumlah_jual'] += $item->jumlah_jual;

    // Update stock_awal dari bulan sebelumnya jika ada
    if ($bulanIndex > 0 && isset($hasil[$id]['detail'][$bulanIndex - 1])) {
        $hasil[$id]['detail'][$bulanIndex]['stock_awal'] =
            $hasil[$id]['detail'][$bulanIndex - 1]['sisa_stock'];
    }

    // Perbarui sisa_stock setelah pengurangan
    $hasil[$id]['detail'][$bulanIndex]['sisa_stock'] =
        $hasil[$id]['detail'][$bulanIndex]['stock_awal'] +
        $hasil[$id]['detail'][$bulanIndex]['jumlah_beli'] -
        $hasil[$id]['detail'][$bulanIndex]['jumlah_jual'];
}

$hasil1 = [];
$gabungan = [];

// Gabung data beli
foreach ($data2 as $item) {
    if (!$item->tgl_bm) continue;

    $gabungan[] = [
        'tipe' => 'beli',
        'id_barang' => $item->id_barang,
        'barang' => $item->barang->nama ?? '-',
        'no' => $item->no_bm,
        'tgl' => $item->tgl_bm,
        'jumlah' => $item->jumlah_beli,
        'stock_awal' => $hasil[$item->id_barang]['detail'][$bulanIndex]['stock_awal'] ?? 0,
        'harga' => $item->harga_beli
    ];
}

// Gabung data jual
foreach ($data3 as $item) {
    if (!$item->suratJalan || !$item->suratJalan->tgl_sj) continue;

    $gabungan[] = [
        'tipe' => 'jual',
        'id_barang' => $item->id_barang,
        'customer' => $item->suratJalan->customer->nama ?? '-',
        'invoice' => $item->invoices->first()->invoice ?? '-', // pastikan ambil invoice pertama
        'barang' => $item->barang->nama ?? '-',
        'no' => $item->no_bm,
        'tgl' => $item->suratJalan->tgl_sj,
        'tgl_sj' => $item->suratJalan->tgl_sj,
        'surat_jalan' => $item->suratJalan->nomor_surat ?? '-',
        'jumlah' => $item->jumlah_jual,
        'stock_awal' => $hasil[$item->id_barang]['detail'][$bulanIndex]['stock_awal'] ?? 0,
        'harga' => $item->harga_jual
    ];
}

// Urutkan berdasarkan tanggal
usort($gabungan, function ($a, $b) {
    return strtotime($a['tgl']) <=> strtotime($b['tgl']);
});

// Hitung stok berjalan
$stok_berjalan = [];

foreach ($gabungan as $item) {
    $id = $item['id_barang'];
    $tgl = $item['tgl'];
    $bulanIndex = (int) date('n', strtotime($tgl)) - 1;
    $jumlah = $item['jumlah'];

    // Inisialisasi jika belum ada
    if (!isset($hasil1[$id])) {
        $hasil1[$id] = [
            'barang' => $item['barang'],
            'detail' => []
        ];

        // Ambil stok awal dari transaksi pertama
        $stok_berjalan[$id] = $item['stock_awal'];
    }

    // Proses stok berjalan
    if ($item['tipe'] === 'beli') {
        $stok_berjalan[$id] += $jumlah;
    } else {
        $stok_berjalan[$id] -= $jumlah;
    }

    // Simpan hasilnya
    $hasil1[$id]['detail'][] = [
        'tipe' => $item['tipe'],
        'no' => $item['no'],
        'tgl' => $tgl,
        'tgl_sj' => $item['tgl_sj'] ?? '-',
        'surat_jalan' => $item['surat_jalan'] ?? '-',
        'invoice' => $item['invoice'] ?? '-',
        'customer' => $item['customer'] ?? '-',
        'bulan' => $months[$bulanIndex] ?? '-',
        'index_bulan' => $bulanIndex,
        'jumlah' => $jumlah,
        'harga' => $item['harga'],
        'sisa_stock' => $stok_berjalan[$id]
    ];

}

    return view('toko.monitor-stock', compact('data', 'hasil1','data1','hasil', 'barang_id','jayapura','barang', 'month', 'year', 'perjalanan','perjalanan1','perjalanan2'));
    }

    public function stocks(){
        $barangs = Barang::where('status', 'aktif')->get();
        $suppliers = Supplier::all();
        return view('toko.stocks', compact('barangs','suppliers'));
    }
    public function update_stock(Request $request)
{
    $data = $request->validate([
        'id' => 'required|exists:stocks,id',
        'id_barang' => 'required',
        'id_supplier' => 'required',
        'vol_bm' => 'required|numeric',
        'tgl_beli' => 'required|date',
    ]);

    $stock = Stock::findOrFail($data['id']);
    $stock->update($data);

    return redirect()->back()->with('success', 'Data berhasil diperbarui.');
}

public function edit_stock($id)
{
    $stock = Stock::with('barang', 'suppliers')->find($id); // Pastikan Anda mengambil data yang benar

    if (!$stock) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    return response()->json($stock);
}
private function getActionButton($stock)
{
    // Cek apakah transaksi ini terhubung ke Invoice
    $invoiceExists = Invoice::where('id_transaksi', $stock->id)->exists();

    if (!$invoiceExists) {
        // Encode nilai teks yang rawan karakter khusus
        $id = $stock->id;
        $hargaBeli = $stock->harga_beli;
        $namaBarang = urlencode(addslashes($stock->barang->nama));
        $satuanBeli = urlencode(addslashes($stock->satuan_beli));
          // Encode untuk nama barang    // Encode untuk satuan jual

        return '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-green-700 m-1" ' .
               'onclick="inputTarif(' . $id . ',' . $hargaBeli . ', \'' . $namaBarang . '\', \'' . $satuanBeli . '\')">' .
               'Edit Harga</button>';
    }

    return "-";
}

private function getJumlahBeli($stock)
{
    // Cek apakah transaksi ini terhubung ke Invoice
    $transaksiExists = Transaction::where('id', $stock->id)->whereNotNull('stts')->exists();
    
    if (!$transaksiExists) {
        // Encode nilai teks untuk menghindari karakter khusus
        $id = $stock->id;
        $jumlahBeli = $stock->jumlah_beli;
        $namaBarang = isset($stock->barang->nama) ? htmlspecialchars($stock->barang->nama, ENT_QUOTES, 'UTF-8') : '';
        $satuanBeli = isset($stock->satuan_beli) ? htmlspecialchars($stock->satuan_beli, ENT_QUOTES, 'UTF-8') : '';
        return '<input type="checkbox" class="confirm-checkbox m-1" ' .
       'onclick="inputTarif1(' . $id . ', ' . $jumlahBeli . ', \'' . $namaBarang . '\', \'' . $satuanBeli . '\')">';
    }

    return '<span class="badge bg-green-500 text-white p-2">' . htmlspecialchars($stock->stts, ENT_QUOTES, 'UTF-8') . '</span>';
}

    
}
