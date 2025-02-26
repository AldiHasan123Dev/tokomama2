<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Supplier;
use PDF;
use App\Models\Invoice;
use App\Models\Barang;
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
        // Ambil parameter pagination
        
        // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
        $stocks = Transaction::selectRaw(
            'transaksi.*'
        )
        ->with('jurnals')
        ->whereNull('id_surat_jalan')
        ->where('harga_beli', '>', 0) // Pastikan harga_beli lebih dari 0 // Grup berdasarkan kondisi
        ->orderBy('no_bm', 'desc') // Urutkan berdasarkan created_at
        ->get();
    
    
    
    
    //     // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
    //     $stocks = Transaction::selectRaw("
    //     transaksi.id, 
    //     transaksi.*, 
    //     id_barang, 
    //     SUM(jumlah_beli) as total_beli, 
    //     SUM(jumlah_jual) as total_jual, 
    //     SUM(sisa) as sisa,
    //     SUM(harga_beli) as total_harga_beli,
    //     SUM(harga_jual) as total_harga_jual,
    //     (SUM(harga_jual) - SUM(harga_beli)) as total_profit,
    //    GROUP_CONCAT(barang.nama ORDER BY barang.nama ASC SEPARATOR '\n') as barang_list
    // ")
    // ->join('barang', 'barang.id', '=', 'transaksi.id_barang')
    // ->whereNotNull('no_bm')
    // ->groupByRaw("
    //     CASE 
    //         WHEN stts IS NOT NULL THEN no_bm 
    //         ELSE COALESCE(invoice_external, transaksi.id) 
    //     END
    // ") // Grup berdasarkan status atau invoice_external/no_bm
    // ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at
    // ->get();


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

    public function dataStock2()
    {
        // Ambil parameter pagination
        
        // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
        $stocks = Transaction::select('id_barang', 'transaksi.*')  // Seleksi kolom dengan jelas
    ->whereNull('id_surat_jalan')  // Pastikan id_surat_jalan bernilai null
    ->where('harga_beli', '>', 0)  // Pastikan harga_beli lebih dari 0
    ->with('jurnals')  // Memuat relasi jurnals
    ->orderBy('created_at', 'desc')  // Urutkan berdasarkan created_at
    ->get();

    
    
    
    
    //     // Query dengan groupBy untuk mengelompokkan data berdasarkan barang
    //     $stocks = Transaction::selectRaw("
    //     transaksi.id, 
    //     transaksi.*, 
    //     id_barang, 
    //     SUM(jumlah_beli) as total_beli, 
    //     SUM(jumlah_jual) as total_jual, 
    //     SUM(sisa) as sisa,
    //     SUM(harga_beli) as total_harga_beli,
    //     SUM(harga_jual) as total_harga_jual,
    //     (SUM(harga_jual) - SUM(harga_beli)) as total_profit,
    //    GROUP_CONCAT(barang.nama ORDER BY barang.nama ASC SEPARATOR '\n') as barang_list
    // ")
    // ->join('barang', 'barang.id', '=', 'transaksi.id_barang')
    // ->whereNotNull('no_bm')
    // ->groupByRaw("
    //     CASE 
    //         WHEN stts IS NOT NULL THEN no_bm 
    //         ELSE COALESCE(invoice_external, transaksi.id) 
    //     END
    // ") // Grup berdasarkan status atau invoice_external/no_bm
    // ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at
    // ->get();


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
                'jurnal' => $stock->jurnals->first()->nomor ?? '-',
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

    public function cetak_nota(){
        return view('toko.list-barang');
    }
    public function monitor_stock(){
        $jayapura = Transaction::whereNotNull('stts')
        ->whereNull('id_surat_jalan')
        ->get()
        ->sum(function ($transaction) {
            $multiplier = ($transaction->barang->status_ppn ?? 'tidak') == 'ya' ? 1.11 : 1;
            return round($transaction->harga_beli * $transaction->sisa * $multiplier);
        });
    
    $perjalanan = Transaction::whereNull('stts')
        ->whereNull('id_surat_jalan')
        ->get()
        ->sum(function ($transaction) {
            $multiplier = ($transaction->barang->status_ppn ?? 'tidak') == 'ya' ? 1.11 : 1;
            return round($transaction->harga_beli * $transaction->sisa * $multiplier);
        });
    
    return view('toko.monitor-stock', compact('jayapura', 'perjalanan'));
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
