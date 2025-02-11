<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



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

    public function update1(Request $request)
    {
        DB::transaction(function () use ($request) {
            $year = date('y');
            $month = date('m');
            $totalDebit = 0;
            $nomor_surat = null;
            $invoice_external = null;
    
            // Ambil nomor terakhir BBK sekali saja di awal
            $lastJNL = Jurnal::where('tipe', 'JNL')
                ->whereMonth('tgl', date('m'))
                ->orderBy('no', 'desc')
                ->first();
            $no_JNL = $lastJNL ? $lastJNL->no + 1 : 1;
            // Nomor Surat BBK (tetap sama untuk semua jurnal dalam transaksi ini)
            $nomor_surat = "$month-$no_JNL/TM/$year";
            $lastInvoice = Transaction::where('invoice_external', 'like', 'InvSupp/%')
            ->orderBy('id', 'desc')
            ->value('invoice_external');


            // Pecah string invoice_external untuk mendapatkan angka terakhir
            $parts = explode('/', $lastInvoice);
            $lastNumber = is_numeric(end($parts)) ? (int) end($parts) : 0;
            $invoice_external = "InvSupp/" . ($lastNumber + 1);
            foreach ($request->id as $key => $id) {
                $transaksi = Transaction::find($id);
                if (!$transaksi) continue; // Pastikan transaksi ditemukan
    
                // Update data transaksi
                $transaksi->jumlah_beli = $request->jumlah_beli[$key] ?? 0;
                $transaksi->sisa = $request->jumlah_beli[$key] ?? 0;
                $transaksi->stts = $request->stts;
                $transaksi->invoice_external = $invoice_external;
                $transaksi->save();
    
                // Hitung total debit
                $harga_beli = $transaksi->harga_beli ?? 0;
                $jumlah_beli = $transaksi->jumlah_beli ?? 0;
                $totalDebit += $harga_beli * $jumlah_beli;
                // Persediaan (Debit)
                if (!empty($transaksi->invoice_external)){
                    Jurnal::create([
                        'coa_id' => 32,
                        'nomor' => $nomor_surat,
                        'tgl' => now()->toDateString(),
                        'keterangan' => 'Persediaan ' . (optional($transaksi->barang)->nama ?? 'Tidak Diketahui') .
                            ' ( Harga Beli ' . number_format($harga_beli, 0, ',', '.') .
                            ' Jumlah Beli ' . number_format($jumlah_beli, 0, ',', '.') . ' )',
                        'debit' => $harga_beli * $jumlah_beli,
                        'kredit' => 0,
                        'invoice' => null,
                        'invoice_external' => $invoice_external,
                        'id_transaksi' => $transaksi->id,
                        'nopol' => null,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no_JNL
                    ]);

                    if ($transaksi->barang->status_ppn == 'ya') {
                        $ppn = round($totalDebit * ($transaksi->barang->value_ppn / 100));
            
                        Jurnal::create([
                            'coa_id' => 10,
                            'nomor' => $nomor_surat,
                            'tgl' => now()->toDateString(),
                            'keterangan' => 'PPN Masukan ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                            'debit' => 0,
                            'kredit' => $ppn,
                            'invoice' => null,
                            'invoice_external' => $invoice_external,
                            'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                            'nopol' => null,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]);
            
                        Jurnal::create([
                            'coa_id' => 46,
                            'nomor' => $nomor_surat,
                            'tgl' => now()->toDateString(),
                            'keterangan' => 'Uang Muka ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                            'debit' => 0,
                            'kredit' => $totalDebit + $ppn,
                            'invoice' => null,
                            'invoice_external' => $invoice_external,
                            'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                            'nopol' => null,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]);
                    } else {
                        // Hutang (Kredit) hanya dibuat jika totalDebit lebih dari 0
                        Jurnal::create([
                            'coa_id' => 46,
                            'nomor' => $nomor_surat,
                            'tgl' => now()->toDateString(),
                            'keterangan' => 'Uang Muka ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                            'debit' => 0,
                            'kredit' => $totalDebit,
                            'invoice' => null,
                            'invoice_external' => $invoice_external,
                            'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                            'nopol' => null,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]);
                    }
                } else {
                Jurnal::create([
                    'coa_id' => 32,
                    'nomor' => $nomor_surat,
                    'tgl' => now()->toDateString(),
                    'keterangan' => 'Persediaan ' . (optional($transaksi->barang)->nama ?? 'Tidak Diketahui') .
                        ' ( Harga Beli ' . number_format($harga_beli, 0, ',', '.') .
                        ' Jumlah Beli ' . number_format($jumlah_beli, 0, ',', '.') . ' )',
                    'debit' => $harga_beli * $jumlah_beli,
                    'kredit' => 0,
                    'invoice' => null,
                    'invoice_external' => $invoice_external,
                    'id_transaksi' => $transaksi->id,
                    'nopol' => null,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $no_JNL
                ]);
    
            if ($transaksi->barang->status_ppn == 'ya') {
                $ppn = round($totalDebit * ($transaksi->barang->value_ppn / 100));
    
                Jurnal::create([
                    'coa_id' => 10,
                    'nomor' => $nomor_surat,
                    'tgl' => now()->toDateString(),
                    'keterangan' => 'PPN Masukan ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                    'debit' => 0,
                    'kredit' => $ppn,
                    'invoice' => null,
                    'invoice_external' => $invoice_external,
                    'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                    'nopol' => null,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $no_JNL
                ]);
    
                Jurnal::create([
                    'coa_id' => 35,
                    'nomor' => $nomor_surat,
                    'tgl' => now()->toDateString(),
                    'keterangan' => 'Hutang Usaha ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                    'debit' => 0,
                    'kredit' => $totalDebit + $ppn,
                    'invoice' => null,
                    'invoice_external' => $invoice_external,
                    'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                    'nopol' => null,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $no_JNL
                ]);
            } else {
                // Hutang (Kredit) hanya dibuat jika totalDebit lebih dari 0
                Jurnal::create([
                    'coa_id' => 35,
                    'nomor' => $nomor_surat,
                    'tgl' => now()->toDateString(),
                    'keterangan' => 'Hutang Usaha ' . (optional($transaksi->suppliers)->nama ?? 'Tidak Diketahui'),
                    'debit' => 0,
                    'kredit' => $totalDebit,
                    'invoice' => null,
                    'invoice_external' => $invoice_external,
                    'id_transaksi' => $request->id[0], // Ambil ID transaksi pertama
                    'nopol' => null,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $no_JNL
                ]);
            }
        }
        }
        });
    
        return response()->json(['message' => 'Barang Masuk Sudah Diterima'], 200);
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
                    'no_bm' => $row->no_bm,
                    'nomor_surat' => $row->suratJalan->nomor_surat ?? '-',
                    'nama_kapal' => $row->suratJalan->nama_kapal ?? '-',
                    'no_cont' => $row->suratJalan->no_cont ?? '-',
                    'no_seal' => $row->suratJalan->no_seal ?? '-',
                    'no_pol' => $row->suratJalan->no_pol ?? '-',
                ];
            }),
        ]);
    }

    public function dataTable1()
    {
        $query = Transaction::query();
        $query->with(['barang', 'suratJalan'])->orderBy('created_at', 'desc') ->whereNull('id_surat_jalan')->where('sisa','>',0);
    
       // Filter berdasarkan tarif dan non-tarif
// Filter berdasarkan tarif dan non-tarif
if (request('tarif') && !request('non_tarif')) {
    // Memastikan harga_beli lebih besar dari 0 untuk tarif
    $query->where('harga_beli', '>', 0);
}

if (request('non_tarif') && !request('tarif')) {
    // Memastikan harga_beli sama dengan 0 untuk non-tarif
    $query->where('harga_beli', '=', 0);
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
                    'tgl_bm' => $row->tgl_bm,
                    'profit' => $row->margin ? number_format($row->margin) : '-',
                    'jumlah_jual' => $row->jumlah_jual ? number_format($row->jumlah_jual) : '-',
                    'harga_jual' => $row->harga_jual ? $row->harga_jual : '-',
                    'satuan_jual' => $row->satuan_jual,
                    'harga_beli' => $row->harga_beli ? $row->harga_beli : '-',
                    'jumlah_beli' => $row->jumlah_beli ? number_format($row->jumlah_beli) : '-',
                    'satuan_beli' => $row->satuan_beli,
                    'no_bm' => $row->no_bm,
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
    $invoiceExists = Transaction::where('id', $row->id)->whereNotNull('stts')->exists();

    if (!$invoiceExists) {
        // Encode nilai teks yang rawan karakter khusus
        $id = $row->id;
        $hargaJual = $row->harga_jual;
        $hargaBeli = $row->harga_beli;
        $margin = $row->margin;
        $jumlahJual = $row->jumlah_jual;
        $namaBarang = urlencode(addslashes($row->barang->nama));  // Encode untuk nama barang
        $satuanJual = urlencode(addslashes($row->satuan_beli));    // Encode untuk satuan jual

        return '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-green-700 m-1" ' .
               'onclick="inputTarif(' . $id . ',' . $hargaJual . ',' . $hargaBeli . ',' . $margin . ',' . $jumlahJual . ', \'' . $namaBarang . '\', \'' . $satuanJual . '\')">' .
               'Edit Harga</button>';
    }

    return "-";
}
// private function getActionButton($row)
// {
//     // Cek apakah transaksi ini terhubung ke Invoice
//     $invoiceExists = Invoice::where('id_transaksi', $row->id)->exists();

//     if (!$invoiceExists) {
//         // Encode nilai teks yang rawan karakter khusus
//         $id = $row->id;
//         $hargaJual = $row->harga_jual;
//         $hargaBeli = $row->harga_beli;
//         $margin = $row->margin;
//         $jumlahJual = $row->jumlah_jual;
//         $namaBarang = urlencode(addslashes($row->barang->nama));  // Encode untuk nama barang
//         $satuanJual = urlencode(addslashes($row->satuan_beli));    // Encode untuk satuan jual

//         return '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-green-700 m-1" ' .
//                'onclick="inputTarif(' . $id . ',' . $hargaJual . ',' . $hargaBeli . ',' . $margin . ',' . $jumlahJual . ', \'' . $namaBarang . '\', \'' . $satuanJual . '\')">' .
//                'Edit Harga</button>';
//     }

//     return "-";
// }
}
