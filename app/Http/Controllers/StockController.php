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
use App\Models\Jurnal;
use App\Models\Satuan;
use Illuminate\Support\Collection;
use App\Models\Transaction;
use Carbon\Carbon;

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

  public function dataStock2(Request $request)
{
    $perPage = $request->input('rows', 20);
    $currentPage = $request->input('page', 1);
    $offset = ($currentPage - 1) * $perPage;
    $barang = $request->barang_nama;
    $barang = $request->barang_nama;
    $invoice_external = $request->invoice_external;
    $jurnal = $request->jurnal;
    $no_bm = $request->no_bm;
    $tgl_jurnal = $request->tgl_jurnal;

    // Query builder awal
    $query = Transaction::selectRaw('transaksi.*')
        ->with('jurnals', 'barang', 'suppliers')
        ->whereNull('id_surat_jalan')
        ->where('harga_beli', '>', 0);

    if ($barang) {
    $query->whereHas('barang', function ($q) use ($barang) {
        $q->where('nama', 'like', '%' . $barang . '%');
    });
    }

    if($invoice_external){
        $query->where('invoice_external', 'like', '%' . $invoice_external . '%');
    }

     if($jurnal){
        $query->whereHas('jurnals', function ($q) use ($jurnal) {
        $q->where('nomor', 'like', '%' . $jurnal. '%');
    });
    }

    if($tgl_jurnal){
        $query->whereHas('jurnals', function ($q) use ($tgl_jurnal) {
        $q->where('tgl', 'like', '%' . $tgl_jurnal. '%');
    });
    }

     if($no_bm){
        $query->where('no_bm', 'like', '%' . $no_bm . '%');
    }

    if ($request->input('sisa_asc')) {
        $query->orderBy('sisa', 'asc');
    } elseif ($request->input('sisa_desc')) {
        $query->orderBy('sisa', 'desc');
    } else {
        $query->orderBy('no_bm', 'desc');
    }

    // Total data untuk pagination (tanpa limit)
    $totalRecords = $query->count();

    // Ambil data sesuai page & limit
    $data = $query->skip($offset)->take($perPage)->get();

    // Proses mapping data seperti gayamu
    $paginated = $data->values()->map(function ($row, $idx) use ($offset) {
        return [
            'id' => $row->transaksi_ids,
            'satuans' => $row->satuans,
            'barangs' => $row->barangs,
            'jumlah_belis' => $row->jumlah_belis,
            'lock' => $row->stts ?? $row->jumlah_beli,
            'status' => $row->stts ?? '-',
            'tgl_jurnal' => optional($row->jurnals->firstWhere('coa_id', 89))->tgl ?? '-',
            'jurnal' => optional($row->jurnals->firstWhere('coa_id', 89))->nomor ?? '-',
            'invoice_external' => $row->invoice_external,
            'no_bm' => $row->no_bm,
            'gudang' => $row->stts === null ? '-' : $row->gudang,
            'satuan_beli' => $row->satuan_beli,
            'satuan_jual' => $row->satuan_jual,
            'supplier' => $row->suppliers->nama ?? '-',
            'barang_nama' => $row->barang->nama ?? '-',
            'total_beli' => $row->jumlah_beli,
            'total_jual' => $row->jumlah_jual,
            'total_harga_beli' => $row->harga_beli,
            'total_harga_jual' => $row->harga_jual,
            'total_profit' => $row->total_profit,
            'sisa' => $row->sisa,
            'index' => $offset + $idx + 1 // sesuai nomor urut global
        ];
    });

    // Total halaman
    $totalPages = ceil($totalRecords / $perPage);

    return response()->json([
        'page' => $currentPage,
        'total' => $totalPages,
        'records' => $totalRecords,
        'rows' => $paginated
    ]);
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

   $barang = Barang::with('satuan')
    ->where('status', 'aktif')
    ->orderBy('nama', 'asc')
    ->get();

$minimumStocks = $barang->pluck('minimum_stock', 'id')->toArray();

// Ambil total sisa per id_barang
$stockQuantities = Transaction::selectRaw('id_barang, SUM(sisa) as total_sisa')
    ->whereNull('id_surat_jalan')
    ->whereNotNull('no_bm')
    ->where('harga_beli', '>', 0)
    ->groupBy('id_barang')
    ->pluck('total_sisa', 'id_barang')
    ->toArray();

// Ambil no_bm terakhir per id_barang

$combinedData = [];

foreach ($barang as $barangItem) {
    $id_barang = $barangItem->id;
    $minStock = (int) ($minimumStocks[$id_barang] ?? 0);
    $sisa = (int) ($stockQuantities[$id_barang] ?? 0);

    // Abaikan jika minimum stock 0
    if ($minStock === 0) {
        continue;
    }

    // Tampilkan jika sisa kurang dari atau sama dengan minimum stock
    if ($sisa <= $minStock) {
        $combinedData[$id_barang] = [
            'nama' => $barangItem->nama,
            'minimum_stock' => $minStock,
            'sisa' => $sisa,
        ];
    }
}





    // Ambil parameter dari request
    $barang_id = $request->get('barang') ?? null;
    $month = $request->get('month') ?? date('m'); // Default ke bulan sekarang jika null
    $year = $request->get('year', date('Y'));

    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    $tglAwal = Carbon::createFromDate($year, 1, 1)->startOfDay();
    $tglAkhir = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

    if ($barang_id === null) {
        $data = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereNull('id_surat_jalan')
            ->whereNotNull('no_bm')
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();

        $data2 = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereNull('id_surat_jalan')
            ->whereNotNull('no_bm')
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();

        $data1 = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereNotNull('no_bm')
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();

        $data3 = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereNotNull('no_bm')
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();
    } else {
        $data = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereNull('id_surat_jalan')
            ->whereNotNull('stts')
            ->whereHas('jurnals', function ($q) use ($tglAwal, $tglAkhir) {
                $q->whereBetween('tgl', [$tglAwal, $tglAkhir]);
            })
            ->whereNotNull('no_bm')
            ->when($barang_id, fn($q) => $q->where('id_barang', $barang_id))
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();

       $data2 = Transaction::with([
    'barang', 
    'suratJalan', 
    'invoices', 
    'jurnals' => function ($q) use ($year, $month) {
        $q->whereYear('tgl', $year)
          ->whereMonth('tgl', $month);
    }
])
->whereNull('id_surat_jalan')
->whereNotNull('stts')
->whereNotNull('no_bm')
->whereHas('jurnals', function ($q) use ($year, $month) {
    $q->whereYear('tgl', $year)
      ->whereMonth('tgl', $month);
})
->when($barang_id, fn($q) => $q->where('id_barang', $barang_id))
->orderBy('created_at')
->orderBy('no_bm')
->get();



        $data1 = Transaction::with(['barang', 'suratJalan', 'jurnals'])
            ->whereHas('suratJalan', function ($q) use ($tglAwal, $tglAkhir) {
                $q->whereBetween('tgl_sj', [$tglAwal, $tglAkhir]);
            })
            ->whereNotNull('no_bm')
            ->whereNotNull('stts')
            ->when($barang_id, fn($q) => $q->where('id_barang', $barang_id))
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();

        $data3 = Transaction::with(['barang', 'suratJalan', 'invoices', 'jurnals'])
            ->whereHas('suratJalan', function ($q) use ($year, $month) {
                $q->whereYear('tgl_sj', $year)
                ->whereMonth('tgl_sj', $month);
            })
            ->whereNotNull('no_bm')
            ->whereNotNull('stts')
            ->when($barang_id, fn($q) => $q->where('id_barang', $barang_id))
            ->orderBy('created_at')
            ->orderBy('no_bm')
            ->get();
    }
    $hasil = [];

// Barang masuk (data)
foreach ($data as $item) {
    if (!optional($item->jurnals->firstWhere('coa_id', 89))->tgl) continue;

    $id = $item->id_barang;
    $tgl = optional($item->jurnals->firstWhere('coa_id', 89))->tgl;
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
    if (!optional($item->jurnals->firstWhere('coa_id', 89))->tgl) continue;

    $gabungan[] = [
        'tipe' => 'beli',
        'id_barang' => $item->id_barang,
        'barang' => $item->barang->nama ?? '-',
        'no' => $item->no_bm,
        'tgl' => optional($item->jurnals->firstWhere('coa_id', 89))->tgl,
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

    return view('toko.monitor-stock', compact('combinedData','data', 'hasil1','data1','hasil', 'barang_id','jayapura','barang', 'month', 'year', 'perjalanan','perjalanan1','perjalanan2'));
    }

    public function stocks(){
        $barangs = Barang::where('status', 'aktif')->get();
        $suppliers = Supplier::all();
        return view('toko.stocks', compact('barangs','suppliers'));
    }

     public function lapQty(){
        return view('jurnal.lap-qty');
    }

public function qty()
{
    $page   = request('page', 1);
    $limit  = request('rows', 10);
    $sidx   = request('sidx', 'transaksi.id');
    $sord   = request('sord', 'asc');
    $search = request('_search') === 'true';

    // Query utama
    $query = Transaction::with(['jurnals', 'barang.satuan', 'suppliers']) // Pastikan relasi ini ada di model
        ->whereNotNull('stts')
        ->orderBy('no_bm', 'desc');

    // Filter tambahan berdasarkan tgl_pembayar dan invoice (jika Anda punya relasi atau join)
    if ((request('periode') !== null && request('periode') !== '') || request('invx') !== null && request('invx') !== '') {
    $query->when(request('periode') !== null && request('periode') !== '', function ($q) {
        $periode = request('periode'); // format: Y-m
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);
       $tglAwal = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
$tglAkhir = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay();
       $q->whereHas('jurnal', function ($query) use ($tglAwal, $tglAkhir) {
    $query->whereBetween('tgl', [$tglAwal, $tglAkhir]);
});

    });

    $query->when(request('invx') !== null && request('invx') !== '', function ($q) {
        $q->where('invoice_external', 'like', '%' . request('invx') . '%');
    });
}


$transaksis = $query->get();

$keluar = [];
$grouped = [];

foreach ($transaksis as $item) {
    $cloned = clone $item;

    if (empty($cloned->tgl_bm)) {
        $cloned->jumlah_beli = 0;
        $key = $cloned->invoice_external;
        $keluar[$key][] = $cloned; // Note: array of items untuk konsistensi
    } else {
        $cloned->jumlah_jual = 0;
        $key = $cloned->no_bm . '||' . $cloned->invoice_external;
        $grouped[$key][] = $cloned;
    }
}

// Gabungkan grouped dan keluar
$combined = collect(array_merge($grouped, $keluar));

// $keluar dan $grouped tetap seperti yang kamu punya

// Gabungkan $keluar dan $grouped ke dalam satu array besar

// Kumpulkan data per id_barang
$byInvoice = [];

foreach ($combined as $key => $items) {
    foreach ($items as $item) {
        $invx = $item->invoice_external ?? null;
        $idBarang = $item->barang->id ?? null;
        $no_bm = $item->no_bm?? null;

        if ($invx === null || $idBarang === null) continue;

        $groupKey = $invx . '||' . $idBarang . '||' . $no_bm;

        if (!isset($byInvoice[$groupKey])) {
            $byInvoice[$groupKey] = [
                'invoice_external' => $invx,
                'id_barang'        => $idBarang,
                'nama_barang'      => $item->barang->nama ?? '-',
                'supplier'         => $item->suppliers->nama ?? '-',
                'jumlah_beli'      => 0,
                'jumlah_jual'      => 0,
                'harga_beli'       => $item->harga_beli ?? 0,
                'total_harga_beli' => 0,
                'sisa'             => 0,
                'sisa1'            => 0,
                'status'           => $item->stts ?? '-',
                'no_bm'            => $item->no_bm,
                'satuan'           => $item->barang->satuan->nama_satuan ?? '-',
                'total_nilai'      => 0,
                'tgl_jurnal'       => optional($item->jurnals->firstWhere('coa_id', 89))->tgl ?? '-', // ⬅️ tambahkan ini
            ];
        }

        $byInvoice[$groupKey]['jumlah_beli'] += $item->jumlah_beli ?? 0;
        $byInvoice[$groupKey]['jumlah_jual'] += $item->jumlah_jual ?? 0;
    }
}

// Hitung ulang sisa1 dan total_nilai
foreach ($byInvoice as &$data) {
    $data['sisa'] = $data['jumlah_beli'] - $data['jumlah_jual'];
    $data['total_nilai'] = $data['sisa'] * $data['harga_beli'];
    $data['total_harga_beli'] = $data['jumlah_beli'] * $data['harga_beli'];
}
unset($data);

// Convert ke collection
$byInvoiceCollection = collect($byInvoice);
$byInvoiceCollection = $byInvoiceCollection->sortBy('nama_barang')->values();


// Ambil request
$periode = request('periode');
$invx    = request('invx');

if (($periode !== null && $periode !== '') || ($invx !== null && $invx !== '')) {
    $byInvoiceCollection = $byInvoiceCollection->filter(function ($item) use ($periode, $invx) {

        $filterPeriode = true;
        $filterInvx = true;

        // Filter berdasarkan periode (filter pada tgl_jurnal)
        if ($periode !== null && $periode !== '') {
            $tahun = substr($periode, 0, 4);
            $bulan = substr($periode, 5, 2);

            $tglAwal = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $tglAkhir = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay();

            if ($item['tgl_jurnal'] && $item['tgl_jurnal'] !== '-') {
                $tglJurnal = Carbon::parse($item['tgl_jurnal']);
                $filterPeriode = $tglJurnal->between($tglAwal, $tglAkhir);
            } else {
                $filterPeriode = false;
            }
        }

        // Filter berdasarkan invoice_external (filter like)
        if ($invx !== null && $invx !== '') {
            $filterInvx = str_contains($item['invoice_external'], $invx);
        }

        return $filterPeriode && $filterInvx;
    });
}


// Total keseluruhan nilai
$totalNilai = $byInvoiceCollection->sum('total_nilai');
$totalNilaiJB = $byInvoiceCollection->sum('jumlah_beli');
$totalNilaiJJ = $byInvoiceCollection->sum('jumlah_jual');
$totalNilaiSisa = $byInvoiceCollection->sum('sisa');
$totalNilaiHB = $byInvoiceCollection->sum('harga_beli');



// Hitung total & paginasi
$totalRecords = $byInvoiceCollection->count();
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 0;
if ($page > $totalPages) $page = $totalPages;

$paginated = $byInvoiceCollection->slice(($page - 1) * $limit, $limit);

$index = 1;
$rows = $paginated->map(function ($item) use (&$index) {
    return [
        'index' => $index++,
        'invoice_external' => $item['invoice_external'],
        'total_harga_beli' => $item['harga_beli'],
        'barang.nama' => $item['nama_barang'],
        'tgl' => $item['tgl_jurnal'],
        'supplier' => $item['supplier'],
        'id_barang' => $item['id_barang'],
        'satuan' => $item['satuan'],
        'status' => $item['status'],
        'no_bm' => $item['no_bm'],
        'total_beli' => $item['jumlah_beli'],
        'total_jual' => $item['jumlah_jual'],
        'sisa' => $item['sisa'],
        'total_nilai' => $item['total_nilai'],
    ];
})->values();


// Response
return response()->json([
    'page'    => $page,
    'total'   => $totalPages,
    'records' => $totalRecords,
    'rows'    => $rows,
    'userdata' => [
        'total_nilai' => $totalNilai,
        'total_beli' => $totalNilaiJB,
        'total_jual' => $totalNilaiJJ,
        'sisa' => $totalNilaiSisa,
        'total_harga_beli' => $totalNilaiHB
    ]
]);

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
