<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Ekspedisi;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\Invoice;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\SuratJalan;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class SuratJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nopol = Nopol::where('status', 'aktif')->get();
        return view('surat_jalan.index', compact('nopol'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::join('satuan', 'barang.id_satuan', '=', 'satuan.id')->select('barang.*', 'satuan.nama_satuan')->where('barang.status', 'AKTIF')->get();

        // dd($barang);
        $nopol = Nopol::where('status', 'aktif')->get();
        $customer = Customer::all();
        $ekspedisi = Ekspedisi::all();
        $satuan = Satuan::all();
        $supplier = Supplier::all();
        return view('surat_jalan.create', compact('barang', 'nopol', 'customer', 'ekspedisi', 'satuan', 'supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_customer' => 'required|exists:customer,id',
            'kepada' => 'required|exists:ekspedisi,nama', // Validasi untuk memastikan ekspedisi ada di database
            'no_pol' => 'required|exists:nopol,nopol',
        ], [
            'id_customer.required' => 'ID Customer tidak valid. Silakan pilih dari daftar yang tersedia.',
            'id_customer.exists' => 'Customer tidak valid. Silakan pilih dari daftar yang tersedia 1.',
            'no_pol.required' => 'Nomor Polisi wajib diisi.',
            'no_pol.exists' => 'Nomor Polisi tidak valid. Silakan pilih dari daftar yang tersedia.',
            'kepada.required' => 'Ekspedisi yang dimasukkan tidak valid. Silakan pilih dari daftar yang ada.',
            'kepada.exists' => 'Ekspedisi yang dimasukkan tidak valid. Silakan pilih dari daftar yang ada.' 
            // Pesan error kustom
        ]);
        for ($i = 0; $i < count($request->satuan_jual); $i++) {
            $satuanJual = Satuan::where('nama_satuan', $request->satuan_jual[$i])->exists();
            if (!$satuanJual) {
                if ($request->satuan_jual[$i] != null) {
                    $satuan = new Satuan;
                    $satuan->nama_satuan = $request->satuan_jual[$i];
                    $satuan->save();
                }
            }
        }

        for ($i = 0; $i < count($request->satuan_beli); $i++) {
            $satuanBeli = Satuan::where('nama_satuan', $request->satuan_beli[$i])->exists();
            if (!$satuanBeli) {
                if ($request->satuan_beli[$i] != null) {
                    $satuan = new Satuan;
                    $satuan->nama_satuan = $request->satuan_beli[$i];
                    $satuan->save();
                }
            }
        }

        $customer = Customer::find($request->id_customer);
        if (!$customer) {
            return back()->with('error', 'Customer Tidak Ditemukan');
        }
        $data = $request->all();
        if (SuratJalan::count() == 0) {
            $no = 87;
        } else {
            $no = SuratJalan::whereYear('created_at', date('Y'))->max('no') + 1;
        }

        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n", strtotime($request->tgl_sj)); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number];
        $data['no'] = $no;
        $data['nomor_surat'] = sprintf('%03d', $no) . '/SJ/SB-' . $month_roman . '/' . date('Y', strtotime($request->tgl_sj));
        $sj = SuratJalan::create($data);
        for ($i = 0; $i < count($request->barang); $i++) {
            // dd($request->barang);
            if ($request->barang[$i] != null && $request->supplier[$i] != null) {
                Transaction::create([
                    'id_surat_jalan' => $sj->id,
                    'id_barang' => $request->barang[$i],
                    'jumlah_beli' => $request->jumlah_beli[$i],
                    'jumlah_jual' => $request->jumlah_jual[$i],
                    'sisa' => $request->jumlah_jual[$i],
                    'satuan_beli' => $request->satuan_beli[$i],
                    'satuan_jual' => $request->satuan_jual[$i],
                    'keterangan' => $request->keterangan[$i],
                    'id_supplier' => $request->supplier[$i]
                ]);
            }
        }
        return redirect()->route('surat-jalan.cetak', $sj);
        // return redirect back()->route('surat-jalan.cetak', $sj);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // id, invoice, nomor_surat, kepada, jumlah, satuan, jenis_barang, nama_kapal, no_cont, no_seal, no_pol, no_job
        $data = SuratJalan::find($request->id);
        $data->invoice = $request->invoice;
        $data->nomor_surat = $request->nomor_surat;
        $data->kepada = $request->kepada;
        $data->jumlah = $request->jumlah;
        $data->satuan = $request->satuan;
        // $data->jenis_barang = $request->jenis_barang;
        $data->nama_kapal = $request->nama_kapal;
        $data->no_cont = $request->no_cont;
        $data->no_seal = $request->no_seal;
        $data->no_pol = $request->no_pol;
        $data->no_job = $request->no_job;
        $data->no_po = $request->no_po;

        $data->tgl_sj = $request->tgl_sj;
        $data->save();
        return redirect()->route('surat-jalan.index')->with('success', 'Data surat jalan berhasil di update');
    }

    public function checkBarangCount(Request $request)
{
    $count = Transaction::where('id_surat_jalan', $request->id_surat_jalan)->count();
    return response()->json(['count' => $count]);
    
}

    public function updateInvoiceExternal(Request $request) 
{
    // Ambil data transaksi berdasarkan id_surat_jalan dan id_supplier
    $check = Transaction::where('id_surat_jalan', $request->id_surat_jalan)
                        ->where('id_supplier', $request->id_supplier)
                        ->get();
    
    $inext = null; // Variabel untuk menyimpan invoice_external lama

    // Cari invoice_external yang tidak null
    foreach ($check as $c) {
        if ($c->invoice_external != null) {
            $inext = $c->invoice_external;
            break;
        }
    }
    

    // Jika ada invoice_external sebelumnya dan di request baru invoice_external kosong/null
    if ($inext != null && $request->invoice_external == null) {
        Transaction::where('id_surat_jalan', $request->id_surat_jalan)
                   ->where('id_supplier', $request->id_supplier)
                   ->update(['invoice_external' => '-']);
    } else {
        // Jika invoice_external ada dalam request, lakukan update
        Transaction::where('id_surat_jalan', $request->id_surat_jalan)
                   ->where('id_supplier', $request->id_supplier)
                   ->update(['invoice_external' => $request->invoice_external]);

        // Update juga di tabel Jurnal berdasarkan invoice_external lama
        if ($inext) {
            Jurnal::where('invoice_external', $inext)
                  ->where('tipe', 'BBK')
                  ->whereNotNull('nomor') // Tambahkan validasi nomor tidak kosong
                  ->update(['invoice_external' => $request->invoice_external,
                            'tgl' => $request->tgl_invx]);

                  Jurnal::where('invoice_external', $inext)
                  ->where('tipe', 'JNL')
                  ->whereNotNull('nomor') // Tambahkan validasi nomor tidak kosong
                  ->update(['invoice_external' => $request->invoice_external,
                            'tgl' => $request->tgl_invx]);
        }
    }

    // Jika invoice_external sebelumnya tidak ada, buat jurnal baru
    if ($inext == null) {
        $this->autoInvoiceExternalJurnal($request);
    }

    return redirect()->route('invoice-external.index')
    ->with('success', 'Invoice external berhasil diperbarui!');

}


    private function autoInvoiceExternalJurnal($request)
{
    $currentYear = $request->tgl_invx;
    $noBBK = Jurnal::where('tipe', 'BBK')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
    $no_BBK =  $noBBK ? $noBBK->no + 1 : 1;   
    $nomor_surat = "$no_BBK/BBK-SB/" . date('y');


    // Ambil data transaksi berdasarkan invoice_external
    $data = Transaction::where('invoice_external', $request->invoice_external)
    ->with([
        'barang:id,nama,status_ppn,value_ppn', // Menyertakan kolom status_ppn dan value_ppn dari tabel barang
        'suratJalan.customer',
        'suppliers:id,nama' // Menyertakan kolom nama dari tabel supplier
    ])->get();

    // Cek apakah jurnal sudah ada untuk transaksi dan invoice ini
    $existingJournals = Jurnal::where('id_transaksi', $request->id_transaksi)
        ->where('invoice_external', $request->invoice_external)
        ->where('invoice_external', $request->tgl_invx)
        ->where('keterangan', 'like', '%Pembelian%')
        ->get();

    if ($existingJournals->isNotEmpty()) {
        // Jika jurnal sudah ada, cukup update nomor jurnalnya
        foreach ($existingJournals as $journal) {
            $journal->update([
                'nomor' => $nomor_surat,
                'invoice_external' => $invoice_external,
                'tgl' => $currentYear // Pastikan $invoice_external berisi nilai yang diinginkan
            ]);
            
        }
    } else {
        // Buat atau update entri jurnal baru hanya jika belum ada jurnal yang sesuai
        DB::transaction(function () use ($data, $currentYear, $request, $no_BBK, $nomor_surat) {
            $total = 0;
            foreach ($data as $item) {
                    $value_ppn = $item->barang->value_ppn / 100;
                    $total += round($item->harga_beli * $item->jumlah_jual * $value_ppn);
                    
                    
                    Jurnal::updateOrCreate(
                        [
                            'id_transaksi' => $item->id,
                            'tipe' => 'BBK',
                            'coa_id' => 30 // Harsat beli
                        ],
                        [
                            'nomor' => $nomor_surat,
                            'tgl' => $currentYear,
                            'keterangan' => 'Pembelian ' . $item->barang->nama . ' (' .  number_format($item->jumlah_jual, 0, ',', '.') . ' ' .  $item->satuan_jual . ' Harsat ' .  number_format($item->harga_beli, 2, ',', '.') . ') untuk ' . $item->suratJalan->customer->nama,
                            'debit' => round($item->harga_beli * $item->jumlah_jual),
                            'kredit' => 0,
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $item->suratJalan->no_pol,
                            'container' => null,
                            'id_transaksi'=>$item->id,
                            'tipe' => 'BBK',
                            'no' => $no_BBK
                        ]
                    );
    
                    // Jurnal Kredit
                    Jurnal::updateOrCreate(
                        [
                            'id_transaksi' => $item->id,
                            'tipe' => 'BBK',
                            'coa_id' => 5
                        ],
                        [
                            'nomor' => $nomor_surat,
                            'tgl' => $currentYear,
                            'keterangan' => 'Pembelian ' . $item->barang->nama . ' (' . number_format($item->jumlah_jual, 0, ',', '.') . ' ' . $item->satuan_jual . ' Harsat ' .  number_format($item->harga_beli, 2, ',', '.') . ') untuk ' . $item->suratJalan->customer->nama,
                            'debit' => 0,
                            'kredit' => round($item->harga_beli * $item->jumlah_jual),
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $item->suratJalan->no_pol,
                            'container' => null,
                            'id_transaksi'=>$item->id,
                            'tipe' => 'BBK',
                            'no' => $no_BBK
                        ]
                    );
                }
                if ($data[0]->barang->status_ppn == 'ya') {
                Jurnal::updateOrCreate(
                    [
                        'id_transaksi' => $data[0]->id,
                        'tipe' => 'BBK',
                        'coa_id' => 10 // COA PPN masukan
                    ],
                    [
                        'nomor' => $nomor_surat,
                        'tgl' => $currentYear,
                        'keterangan' => 'PPN Masukan ' . $data[0]->suppliers->nama,
                        'debit' => $total,
                        'kredit' => 0,
                        'invoice' => null,
                        'invoice_external' => $request->invoice_external,
                        'nopol' => $data[0]->suratJalan->no_pol,
                        'container' => null,
                        'id_transaksi'=>$data[0]->id,
                        'tipe' => 'BBK',
                        'no' => $no_BBK
                    ]
                );
                Jurnal::create(
                    [
                        'id_transaksi' => $item->id,
                        'tipe' => 'BBK',
                        'coa_id' => 5, // COA bank mandiri,
                        'nomor' => $nomor_surat,
                        'tgl' => $currentYear,
                        'keterangan' => 'PPN Masukan ' . $item->suppliers->nama,
                        'debit' => 0,
                        'kredit' => $total, // PPN amount
                        'invoice' => null,
                        'invoice_external' => $request->invoice_external,
                        'nopol' => $item->suratJalan->no_pol,
                        'container' => null,
                        'id_transaksi'=>$item->id,
                        'tipe' => 'BBK',
                        'no' => $no_BBK
                    ]
                );
            }
        });
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return response()->json(['message' => 'ID is required'], 400);
        }
        try {
            // Ini penyebab invoice juga terhapus
            // $relatedInvoices = Invoice::where('id_transaksi', $id)->get();
            // foreach ($relatedInvoices as $invoice) {
            //     $invoice->delete();
            // }

            $transactions = Transaction::where('id_surat_jalan', $id)->get();
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }
    

            $suratJalan = SuratJalan::find($id);
            if ($suratJalan) {
                $suratJalan->delete();
            }

            return response()->json(['message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting data', 'error' => $e->getMessage()], 500);
        }
    }


    public function cetak(SuratJalan $surat_jalan)
    {
        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $ekspedisi = Ekspedisi::find($surat_jalan->id_ekspedisi);
        $pdf = Pdf::loadView('surat_jalan.cetak', compact('surat_jalan', 'ekspedisi'))->setPaper('a4', 'potrait');

        return $pdf->stream('surat_jalan.pdf');
        // return view('surat_jalan.cetak', compact('surat_jalan', 'ekspedisi'));
    }

    public function tarif()
    {
        return view('surat_jalan.tarif');
    }

    public function dataTable()
    {
        $data = SuratJalan::query()->orderBy('created_at', 'desc');
        
        // $data = SuratJalan::query()->join('ekspedisi', 'ekspedisi.id', '=', 'surat_jalan.id_ekspedisi')->join('transaction', 'transaction.id_surat_jalan', '=', 'surat_jalan.id')->select('surat_jalan.*', 'ekspedisi.nama', 'transaction.id_surat_jalan', 'transaction.harga_jual', 'transaction.jumlah_jual', 'transaction.harga_beli', 'transaction.jumlah_beli');


        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('profit', function ($row) {
                $total = $row->transactions->sum('margin');
                return number_format($total);
            })
            ->addColumn('invoice', function ($row) {
                $inv = array();
                foreach ($row->transactions as $key => $item) {
                    foreach ($item->invoices as $in) {
                        array_push($inv, $in->invoice);
                    }
                }
                $inv = array_unique($inv);
                return implode(', ', $inv);
            })
            ->addColumn('aksi', function ($row) {
                $action = '';
                $sisa = $row->transactions->sum('sisa');
                if ($sisa > 0) {
                    $action = '<button onclick="getData(' . $row->id . ', \'' . addslashes($row->invoice) . '\', \'' . addslashes($row->nomor_surat) . '\', \'' . addslashes($row->kepada) . '\', \'' . addslashes($row->jumlah) . '\', \'' . addslashes($row->satuan) . '\', \'' . addslashes($row->nama_kapal) . '\', \'' . addslashes($row->no_cont) . '\', \'' . addslashes($row->no_seal) . '\', \'' . addslashes($row->no_pol) . '\', \'' . addslashes($row->no_job) . '\',  \'' . addslashes($row->tgl_sj) . '\', \'' . addslashes($row->no_po) . '\')" id="edit" class="text-yellow-400 font-semibold mb-3 self-end"><i class="fa-solid fa-pencil"></i></button>
                                <button onclick="deleteData(' . $row->id . ')"  id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>';
                }
                return '<div class="flex gap-3 mt-2">
                                <a target="_blank" href="' . route('surat-jalan.cetak', $row) . '" class="text-green-500 font-semibold mb-3 self-end"><i class="fa-solid fa-print mt-2"></i></a>
                                ' . $action . '
                            </div>';
            })
            ->rawColumns(['profit'])
            ->rawColumns(['aksi'])
            ->toJson();
    }
    public function dataTableSupplier(Request $request)
{
    // Ambil parameter pencarian dari request
    $searchTerm = $request->get('searchString', ''); // Untuk pencarian global

    // Ambil data dengan relasi yang diperlukan dan group by
    $data = Transaction::with(['suratJalan', 'suppliers', 'barang'])
        ->select('id_surat_jalan', 'id_supplier', 'invoice_external', 'id_barang',
                 DB::raw('AVG(harga_beli) as avg_harga_beli'),
                 DB::raw('SUM(harga_beli) as sum_harga_beli'),
                 DB::raw('SUM(jumlah_beli) as total_jumlah_beli'))
        ->groupBy('id_surat_jalan', 'id_supplier', 'invoice_external')
        ->orderBy('created_at', 'desc');

    // Tambahkan filter pencarian jika ada
    if (!empty($searchTerm)) {
        $data->where(function ($query) use ($searchTerm) {
            $query->whereHas('suratJalan', function ($q) use ($searchTerm) {
                $q->where('nomor_surat', 'like', "%{$searchTerm}%");
            })
            ->orWhereHas('suppliers', function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%");
            })
            ->orWhere('invoice_external', 'like', "%{$searchTerm}%");
        });
    }

    // Hitung total record sebelum pagination (setelah pencarian diterapkan)
    $data1 = $data->get();
    
    $totalRecords = $data1->count();
    $currentPage = $request->page; 
    $perPage = $request->rows; 
    $index = ($currentPage - 1) * $perPage;
    $paginatedData = $data1->slice($index)->values();

    // Membuat array hasil untuk response JSON
    $result = $paginatedData->map(function ($row) use (&$index) {
        $index++;
        $ppn = 0;
        $subtotal = $row->avg_harga_beli * $row->total_jumlah_beli;
        $journal = Jurnal::where('invoice_external', $row->invoice_external)->get();
        $tgl_jurnal_array = $journal->pluck('tgl')->toArray();
        // Tentukan tanggal, gunakan yang pertama jika ada, atau tanggal default (hari ini) jika tidak ada
        $tgl_jurnal = !empty($tgl_jurnal_array) && $tgl_jurnal_array[0] !== '2023-12-31'
            ? Carbon::parse($tgl_jurnal_array[0])->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');
        $barang = Barang::where('id', $row->id_barang)->first();
        
        if ($barang && $barang->status_ppn === 'ya') {
            $value_ppn = $barang->value_ppn / 100;
            $ppn = round($subtotal * $value_ppn);
        }

        return [
            'DT_RowIndex' => $index,
            'nomor_surat' => $row->suratJalan->nomor_surat ?? '-',
            'harga_beli' => $row->avg_harga_beli ? number_format($row->avg_harga_beli, 2, ',', '.') : '-',
            'sum_harga_beli' => $row->avg_harga_beli ? number_format($row->avg_harga_beli, 4, ',', '.') : '-',
            'jumlah_beli' => $row->total_jumlah_beli ? number_format($row->total_jumlah_beli, 0, ',', '.') : '-',
            'total' => number_format($subtotal, 2, ',', '.'),
            'ppn' => number_format($ppn, 2, ',', '.'),
            'supplier' => $row->suppliers->nama ?? '-',
            'invoice_external' => $row->invoice_external,
            'aksi' => '<button onclick="getData(' . $row->id_surat_jalan . ', \'' . addslashes($row->suratJalan->nomor_surat) . '\', ' . $row->id_supplier . ', \'' . addslashes($row->suppliers->nama) . '\', \'' . addslashes($row->invoice_external) . '\', ' . $row->avg_harga_beli . ', ' . $row->total_jumlah_beli . ', \'' . ($barang->status_ppn ?? '-') . '\', ' . ($barang->value_ppn ?? 0) . ', \'' . addslashes($tgl_jurnal ?? format('Y-m-d')) . '\')" id="edit" class="text-yellow-400 font-semibold self-end"><i class="fa-solid fa-pencil"></i></button>'
        ];
    });

    // Kembalikan response JSON dengan format yang sesuai untuk jqGrid
    return response()->json([
        'current_page' => $currentPage, // Halaman saat ini
        'last_page' => ceil($totalRecords / $perPage), // Total halaman
        'total' => $totalRecords, // Total record setelah filter
        'data' => $result, // Data untuk halaman ini
    ]);
}

    
    

    public function editBarang()
    {
        $transactions = Transaction::with(['suppliers'])->orderBy('id_surat_jalan', 'desc')->get();
        $satuans = Satuan::all();
        $barangs = Barang::where('status', 'AKTIF')->get();
        $suppliers = Supplier::all();
        // dd($transactions[0]->suppliers);
        return view('surat_jalan.editBarang', compact('transactions', 'satuans', 'barangs', 'suppliers'));
    }

    public function editBarangPost(Request $request)
    {
        Transaction::where('id', $request->id)->update([
            'jumlah_jual' => $request->jumlah_jual,
            'jumlah_beli' => $request->jumlah_jual,
            'sisa' => $request->jumlah_jual,
            'satuan_beli' => $request->satuan,
            'satuan_jual' => $request->satuan,
            'id_supplier' => $request->supplier,
            'keterangan' => $request->keterangan,
        ]);
        return redirect()->back()->with('success', 'Data berhasil diupdate!!');
    }

    public function hapusBarang(Request $request)
    {
        Transaction::where('id', $request->id)->delete();
        return redirect()->back()->with('success', 'Data barang berhasil dihapus.');
    }

    public function tambahBarang(Request $request)
    {
        // dd($request->id_surat_jalan);
        Transaction::create([
            'id_surat_jalan' => $request->id_surat_jalan,
            'id_barang' => $request->id_barang,
            'id_supplier' => $request->id_supplier,
            'jumlah_jual' => $request->jumlah_jual,
            'jumlah_beli' => $request->jumlah_jual,
            'sisa' => $request->jumlah_jual,
            'satuan_jual' => $request->satuan_jual,
            'satuan_beli' => $request->satuan_jual,
            'harga_jual' => 0,
            'harga_beli' => 0,
            'margin' => 0,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data barang berhasil ditambahkan.');
    }
}
