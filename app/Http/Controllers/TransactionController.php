<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return response($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $transaksi = Transaction::find($request->id);
        $transaksi->update($request->all());
        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function dataTable()
    {
        $query = Transaction::query();
        $query->with(['barang', 'suratJalan'])->orderBy('created_at', 'desc');
    
        // Filter berdasarkan tarif dan non-tarif
        if (request('tarif')) {
            $query->where('harga_jual', '>', 0)
                  ->where('harga_beli', '>', 0);
        }
        if (request('non_tarif')) {
            $query->where(function($query) {
                $query->where('harga_jual', 0)
                      ->orWhere('harga_beli', 0);
            });
        }
    
        // Tambahkan parameter pencarian jika ada
        if ($searchTerm = request('searchString')) {
            $query->where(function($query) use ($searchTerm) {
                $query->whereHas('barang', function($q) use ($searchTerm) {
                    $q->where('nama', 'like', "%{$searchTerm}%");
                })
                ->orWhere('no_pol', 'like', "%{$searchTerm}%")
                ->orWhere('no_cont', 'like', "%{$searchTerm}%")
                ->orWhere('no_seal', 'like', "%{$searchTerm}%");
            });
        }   
        $transactions = $query->get(); // Ambil semua data tanpa pagination untuk diolah

        // Hitung total record sebelum pagination
        $totalRecords = $transactions->count();
    
        // Pagination
        $perPage = request('per_page', 20); // Jumlah item per halaman, default 20
        $currentPage = request('page', 1); // Halaman saat ini, default 1
    
        // Hitung indeks untuk mengambil data yang benar
        $index = ($currentPage - 1) * $perPage; // Indeks awal
    
        // Slice data untuk pagination
        $paginatedData = $transactions->slice($index, $perPage)->values(); // Mengambil data untuk halaman ini
    
      
        return response()->json([
            'current_page' => $currentPage,
              'last_page' => ceil($totalRecords / $perPage), // Total halaman
        'total' => $totalRecords, // Total records
            'data' => $transactions->map(function ($row) use (&$index) {
                $index++;
                return [
                    'DT_RowIndex' => $index,
                    'id' => $row->id,
                    'aksi' => $this->getActionButton($row),
                    'barang' => $row->barang->nama,
                    'profit' => $row->margin ? number_format($row->margin) : '-',
                    'jumlah_jual' => $row->jumlah_jual ? number_format($row->jumlah_jual) : '-',
                    'harga_jual' => $row->harga_jual ? $row->harga_jual : '-',
                    'satuan_jual' => $row->satuan_jual,
                    'harga_beli' => $row->harga_beli ? $row->harga_beli : '-',
                    'jumlah_beli' => $row->jumlah_beli ? number_format($row->jumlah_beli) : '-',
                    'satuan_beli' => $row->satuan_beli,
                    'nomor_surat' => $row->suratJalan->nomor_surat ?? '-',
                    'nama_kapal' => $row->suratJalan->nama_kapal ?? '-',
                    'no_cont' => $row->suratJalan->no_cont ?? '-',
                    'no_seal' => $row->suratJalan->no_seal ?? '-',
                    'no_pol' => $row->suratJalan->no_pol ?? '-',
                ];
            }),
        ]);
    }
    
    

    private function getActionButton($row)
{
    // Cek apakah transaksi ini terhubung ke Invoice
    $invoiceExists = Invoice::where('id_transaksi', $row->id)->exists();

    if (!$invoiceExists) {
        // Encode nilai teks yang rawan karakter khusus
        $id = $row->id;
        $hargaJual = $row->harga_jual;
        $hargaBeli = $row->harga_beli;
        $margin = $row->margin;
        $jumlahJual = $row->jumlah_jual;
        $namaBarang = urlencode(addslashes($row->barang->nama));  // Encode untuk nama barang
        $satuanJual = urlencode(addslashes($row->satuan_jual));    // Encode untuk satuan jual

        return '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-green-700 m-1" ' .
               'onclick="inputTarif(' . $id . ',' . $hargaJual . ',' . $hargaBeli . ',' . $margin . ',' . $jumlahJual . ', \'' . $namaBarang . '\', \'' . $satuanJual . '\')">' .
               'Edit Harga</button>';
    }

    return "-";
}

}
