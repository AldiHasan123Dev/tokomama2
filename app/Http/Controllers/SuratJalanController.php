<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Ekspedisi;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\Invoice;
use App\Models\Satuan;
use DateTime;
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
        $customer = Customer::all();
        return view('surat_jalan.index', compact('nopol','customer'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
        ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
        ->select(
            'barang.nama',
            'barang.kode_objek',
            'satuan.nama_satuan',
            'transaksi.*'
        )
        ->where('barang.status', 'AKTIF')
        ->where('barang.status_ppn', 'ya')
        ->whereNull('id_surat_jalan')
        ->where('harga_jual',0)
        ->where('harga_beli', '>', 0)
        ->where('sisa', '>', 0)
        ->whereNotNull('transaksi.stts')
        ->get();

        $nopol = Nopol::where('status', 'aktif')->get();
        $customer = Customer::all();
        $ekspedisi = Ekspedisi::all();
        $satuan = Satuan::all();
        $supplier = Supplier::all();
        return view('surat_jalan.create', compact('barang', 'nopol', 'customer', 'ekspedisi', 'satuan', 'supplier'));
    }

    public function createNoPPN()
    {
        $barang = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
        ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
        ->select(
            'barang.nama',
            'barang.kode_objek',
            'satuan.nama_satuan',
            'transaksi.*'
        )
        ->where('barang.status', 'AKTIF')
        ->where('barang.status_ppn', 'tidak')
        ->whereNull('id_surat_jalan')
        ->where('harga_jual',0)
        ->where('harga_beli', '>', 0)
        ->where('sisa', '>', 0)
        ->whereNotNull('transaksi.stts')
        ->get();

        $nopol = Nopol::where('status', 'aktif')->get();
        $customer = Customer::all();
        $ekspedisi = Ekspedisi::all();
        $satuan = Satuan::all();
        $supplier = Supplier::all();
        return view('surat_jalan.create_noppn', compact('barang', 'nopol', 'customer', 'ekspedisi', 'satuan', 'supplier'));
    }

    public function create_bm()
    {
        $barang = Barang::join('satuan', 'barang.id_satuan', '=', 'satuan.id')->select('barang.*', 'satuan.nama_satuan')->where('barang.status', 'AKTIF')->get();
        // dd($barang);
        $nopol = Nopol::where('status', 'aktif')->get();
        $customer = Customer::all();
        $ekspedisi = Ekspedisi::all();
        $satuan = Satuan::all();
        $supplier = Supplier::all();
        return view('surat_jalan.create_bm', compact('barang', 'nopol', 'customer', 'ekspedisi', 'satuan', 'supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'id_customer' => 'required|exists:customer,id',
        //     'no_pol' => 'required|exists:nopol,nopol',
        // ], [
        //     'id_customer.required' => 'ID Customer tidak valid. Silakan pilih dari daftar yang tersedia.',
        //     'id_customer.exists' => 'Customer tidak valid. Silakan pilih dari daftar yang tersedia.',
        //     'no_pol.required' => 'Nomor Polisi wajib diisi.',
        //     'no_pol.exists' => 'Nomor Polisi tidak valid. Silakan pilih dari daftar yang tersedia.'
        // ]);
    
        // Validasi dan cek stok terlebih dahulu
        if (
            empty(array_filter($request->barang)) || 
            empty(array_filter($request->jumlah_jual))
        ) {
            return redirect()->back()->with('error', 'Minimal isi satu barang, jumlah jual, dan satuan jual!');
        }
        for ($i = 0; $i < count($request->barang); $i++) {
            if ($request->barang[$i] != null && $request->jumlah_jual[$i] != null) {
                // Ambil stok barang dari database berdasarkan barang yang dipilih
                $transactions = Transaction::whereIn('id', $request->barang)->pluck('sisa')->toArray();
        
                // Jika ada lebih dari satu transaksi, hitung total stok
                $totalStok = count($transactions) > 1 ? $transactions : [$transactions[0] ?? 0];
        
                // Ambil jumlah jual dan pastikan dalam bentuk array numerik
                $totalJumlahJual = array_map('intval', array_filter($request->jumlah_jual));
        
        
                // Lakukan pengurangan stok
                $cek = array_map(fn($stok, $jual) => $stok - $jual, $totalStok, $totalJumlahJual);
        
                // Debug output
        
                // Jika stok kurang, redirect kembali dengan pesan error
                if (min($cek) < 0) {
                    return redirect()->back()->with('error', "Stok barang tidak mencukupi!");
                }
            }
        }
        
        
    
        // Lanjutkan pengecekan untuk satuan jual
        // for ($i = 0; $i < count($request->satuan_jual); $i++) {
        //     if ($request->satuan_jual[$i] != null) {
        //         $satuanJual = Satuan::where('nama_satuan', $request->satuan_jual[$i])->exists();
        //         if (!$satuanJual) {
        //             // Jika satuan tidak ada, buat baru
        //             $satuan = new Satuan;
        //             $satuan->nama_satuan = $request->satuan_jual[$i];
        //             $satuan->save();
        //         }
        //     }
        // }
    
        // Mendapatkan customer berdasarkan id_customer
        $customer = Customer::where('nama',$request->tujuan)->first();
        if (!$customer) {
            return back()->with('error', 'Customer Tidak Ditemukan');
        }
        
        $data = $request->all();
        $nopol = Nopol::where('nopol',$request->no_pol)->first();
        // $expedisi = Ekspedisi::where('nama',$request->kepada)->first();
        // Mengatur nomor surat jalan
        if (SuratJalan::count() == 0) {
            $no = 87;
        } else {
            $no = SuratJalan::whereYear('created_at', date('Y'))->max('no') + 1;
        }
        // $nopol = Nopol::where('s')
    
        // Menentukan nomor surat jalan dalam format roman
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $month_number = date("n", strtotime($request->tgl_sj));
        $month_roman = $roman_numerals[$month_number];
        $data['id_customer'] = $customer->id;
        $data['kepada']= $customer->nama_npwp ?? $customer->nama;
        $data['no'] = $no;
        $data['nomor_surat'] = sprintf('%03d', $no) . '/SJ/TM-' . $month_roman . '/' . date('Y', strtotime($request->tgl_sj));
        $sj = SuratJalan::create($data);
    
        // Periksa apakah Surat Jalan sudah ada
        if (SuratJalan::find($sj->id)) {
            // Jika ada, lanjutkan update transaksi
            for ($i = 0; $i < count($request->barang); $i++) {
                if ($request->barang[$i] != null && $request->jumlah_jual[$i] != null) {
                    // Cek stok lagi sebelum update transaksi
                    $transaction = Transaction::where('id', $request->barang[$i])->first();
                   
                    if ($transaction) {
                       
                            $sisa = $transaction->sisa - $request->jumlah_jual[$i];
                            $bkeluar = $request->jumlah_jual[$i] + $transaction->jumlah_jual;
                            $invx = $transaction->invoice_external;
                            Transaction::create([
                                'sisa' => $request->jumlah_jual[$i],
                                'invoice_external' => $invx, // Menggunakan sisa dari transaksi yang ada
                                'id_surat_jalan' => $sj->id, // ID surat jalan yang baru
                                'jumlah_jual' => $request->jumlah_jual[$i],
                                'jumlah_beli' => $transaction->jumlah_beli, // Jumlah jual yang baru
                                'satuan_jual' =>  $transaction->satuan_beli,
                                'satuan_beli' => $transaction->satuan_beli, // Satuan jual yang baru
                                'keterangan' => $request->keterangan[$i], // Keterangan dari request
                                'id_barang' => $transaction->id_barang,
                                'id_supplier' => $transaction->id_supplier, 
                                'id_surat_jalan' => $sj->id,
                                'harga_beli' => $transaction->harga_beli,
                                'no_bm' => $transaction->no_bm,
                                'stts' => $transaction->stts
                            ]);

                            $transaction->update([
                                'sisa' => $sisa,
                                'jumlah_jual' => $bkeluar,
                                'satuan_jual' =>  $transaction->satuan_beli
                            ]);
                        
                    }

                }
            }
        } else {
            return back()->with('error', 'ID Surat Jalan tidak valid');
        }
    
        return redirect()->route('surat-jalan.cetak', $sj);
    }
    
    

    public function store_bm(Request $request)
    {
        $data = $request->all();
        if (!$request->tgl_bm) {
            return back()->with('error', 'Silahkan input tanggal barang masuk');
        }
        if (Transaction::count() == 0) {
            $no = 1;
        } else {
            $no = Transaction::whereYear('created_at', date('Y'))->max('no') + 1 ;
        }

        for ($i = 0; $i < count($request->satuan_beli); $i++) {
            if ($request->satuan_beli[$i] != null) {
                $satuanJual = Satuan::where('nama_satuan', $request->satuan_beli[$i])->exists();
                if (!$satuanJual) {
                    // Jika satuan tidak ada, buat baru
                    $satuan = new Satuan;
                    $satuan->nama_satuan = $request->satuan_beli[$i];
                    $satuan->save();
                }
            }
        }
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n", strtotime($request->tgl));
        $month_roman = $roman_numerals[$month_number];
        $no_bm = sprintf('%03d', $no) . '/BM/TM-' . $month_roman . '/' . date('Y', strtotime($request->tgl));
        for ($i = 0; $i < count($request->barang); $i++) {
            // dd($request->barang);
            if ($request->barang[$i] != null && $request->supplier[$i] != null) {
                Transaction::create([
                    'id_barang' => $request->barang[$i],
                    'jumlah_beli' => $request->jumlah_beli[$i],
                    'keterangan' => $request->keterangan[$i],
                    'sisa' => $request->jumlah_beli[$i],
                    'satuan_beli' => $request->satuan_beli[$i],
                    'id_supplier' => $request->supplier[$i],
                    'no' => $no,
                    'no_bm' => $no_bm,
                    'tgl_bm' => $request->tgl_bm
                ]);
            }
        }
        return redirect()->route('harga_beli')->with('success', 'Barang Masuk sudah tersimpan, silahkan input harga beli');
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
        $customer = Customer::find($request->kepada);
        $data->invoice = $request->invoice;
        $data->nomor_surat = $request->nomor_surat;
        $data->id_customer = $customer->id;
        $data->kepada = $customer->nama;
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
    $check = Transaction::where('no_bm', $request->no_bm)
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
        Transaction::where('no_bm', $request->no_bm)
                   ->where('id_supplier', $request->id_supplier)
                   ->update(['invoice_external' => '-']);
    } else {
        // Jika invoice_external ada dalam request, lakukan update
        Transaction::where('no_bm', $request->no_bm)
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
    $date = new DateTime($currentYear);
    $month = $date->format('m');
    $year = $date->format('y');
    $noJNL = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $month)->orderBy('no', 'desc')->first() ?? 0;
    $no_JNL =  $noJNL ? $noJNL->no + 1 : 1;   
    $no_JNL1 =  $no_JNL + 1;   
    $nomor_surat = "$month-$no_JNL/TM/$year";
    $nomor_surat1 = "$month-$no_JNL1/TM/$year";


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
        DB::transaction(function () use ($data, $currentYear, $request, $no_JNL, $nomor_surat, $no_JNL1, $nomor_surat1) {
            $total = 0;
            $total_ppn = 0;
            foreach ($data as $item) {
                    $value_ppn = $item->barang->value_ppn / 100;
                    $total_ppn += round($item->harga_beli * $item->jumlah_beli * $value_ppn);
                    $total  += $item->harga_beli * $item->jumlah_beli;
                    
                    //Jurnal Persediaan
                    Jurnal::updateOrCreate(
                        [
                            'id_transaksi' => $item->id,
                            'tipe' => 'JNL',
                            'coa_id' => 91 // Harsat beli
                        ],
                        [
                            'nomor' => $nomor_surat,
                            'tgl' => $currentYear,
                            'keterangan' => 'Persediaan SBY ' . $item->barang->nama . ' (' .  number_format($item->jumlah_beli, 0, ',', '.') . ' ' .  $item->satuan_beli . ' Harsat ' .  number_format($item->harga_beli, 2, ',', '.') . ') - ' . $item->suppliers->nama,
                            'debit' => round($item->harga_beli * $item->jumlah_beli),
                            'kredit' => 0,
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $item->suratJalan->no_pol ?? null,
                            'container' => null,
                            'id_transaksi'=>$item->id,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]
                    );
                }
                if ($data[0]->barang->status_ppn == 'ya') {
                    Jurnal::updateOrCreate(
                        [
                            'id_transaksi' => $data[0]->id,
                            'tipe' => 'JNL',
                            'coa_id' => 10 // COA PPN masukan
                        ],
                        [
                            'nomor' => $nomor_surat,
                            'tgl' => $currentYear,
                            'keterangan' => 'PPN Masukan ' . $data[0]->suppliers->nama,
                            'debit' => $total_ppn,
                            'kredit' => 0,
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $data[0]->suratJalan->no_pol ?? null,
                            'container' => null,
                            'id_transaksi'=>$data[0]->id,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]
                    );

                    Jurnal::create(
                        [
                            'id_transaksi' => $item->id,
                            'tipe' => 'JNL',
                            'coa_id' => 35, // COA bank mandiri,
                            'nomor' => $nomor_surat,
                            'tgl' => $currentYear,
                            'keterangan' => 'Hutang ' . $item->suppliers->nama,
                            'debit' => 0,
                            'kredit' => $total + $total_ppn, // PPN amount
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $item->suratJalan->no_pol ?? null,
                            'container' => null,
                            'id_transaksi'=>$item->id,
                            'tipe' => 'JNL',
                            'no' => $no_JNL
                        ]
                    );
                } else {
                    Jurnal::create(
                     [
                         'id_transaksi' => $item->id,
                         'tipe' => 'JNL',
                         'coa_id' => 35, // COA bank mandiri,
                         'nomor' => $nomor_surat,
                         'tgl' => $currentYear,
                         'keterangan' => 'Hutang ' . $item->suppliers->nama,
                         'debit' => 0,
                         'kredit' => $total, // PPN amount
                         'invoice' => null,
                         'invoice_external' => $request->invoice_external,
                         'nopol' => $item->suratJalan->no_pol ?? null,
                         'container' => null,
                         'id_transaksi'=>$item->id,
                         'tipe' => 'JNL',
                         'no' => $no_JNL
                     ]
                 );
                }
                    // Jurnal Hutang
                $total = 0;
                $total_ppn = 0;
                foreach ($data as $item) {
                    $value_ppn = $item->barang->value_ppn / 100;
                    $total_ppn += round($item->harga_beli * $item->jumlah_beli * $value_ppn);
                    $total  += $item->harga_beli * $item->jumlah_beli;
                    
                    //Jurnal Persediaan
                    Jurnal::updateOrCreate(
                        [
                            'id_transaksi' => $item->id,
                            'tipe' => 'JNL',
                            'coa_id' => 90 // Harsat beli
                        ],
                        [
                            'nomor' => $nomor_surat1,
                            'tgl' => $currentYear,
                            'keterangan' => 'Persediaan Perjalanan ' . $item->barang->nama . ' (' .  number_format($item->jumlah_beli, 0, ',', '.') . ' ' .  $item->satuan_beli . ' Harsat ' .  number_format($item->harga_beli, 2, ',', '.') . ') - ' . $item->suppliers->nama,
                            'debit' => round($item->harga_beli * $item->jumlah_beli),
                            'kredit' => 0,
                            'invoice' => null,
                            'invoice_external' => $request->invoice_external,
                            'nopol' => $item->suratJalan->no_pol ?? null,
                            'container' => null,
                            'id_transaksi'=>$item->id,
                            'tipe' => 'JNL',
                            'no' => $no_JNL1
                        ]
                    );
                }
                Jurnal::create(
                    [
                        'id_transaksi' => $item->id,
                        'tipe' => 'JNL',
                        'coa_id' => 91, // COA bank mandiri,
                        'nomor' => $nomor_surat1,
                        'tgl' => $currentYear,
                        'keterangan' => 'Persediaan SBY - ' . $item->suppliers->nama,
                        'debit' => 0,
                        'kredit' => $total, // PPN amount
                        'invoice' => null,
                        'invoice_external' => $request->invoice_external,
                        'nopol' => $item->suratJalan->no_pol ?? null,
                        'container' => null,
                        'id_transaksi'=>$item->id,
                        'tipe' => 'JNL',
                        'no' => $no_JNL1
                    ]
                );           
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

    public function harga_beli()
    {
        return view('surat_jalan.harga_beli');
    }

    public function dataTable()
    {
        $data = SuratJalan::query()
        ->with('transactions.suppliers') // Pastikan relasi transactions dipanggil untuk efisiensi
        ->orderBy('created_at', 'desc');
    
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('no_bm', function ($row) {
            return optional($row->transactions->first())->no_bm; // Menghindari error jika tidak ada transaksi
        })
        ->addColumn('suppliers', function ($row) {
            $transaction = $row->transactions->first();
            return optional($transaction?->suppliers)->nama; // Menghindari error jika tidak ada transaksi
        })
    
            ->addColumn('profit', function ($row) {
                $total = $row->transactions->sum('margin');
                return number_format($total);
            })
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
                    $action = '<button onclick="getData(' . $row->id . ', \'' . addslashes($row->invoice) . '\', \'' . addslashes($row->nomor_surat) . '\', \'' . addslashes($row->customer->nama) . '\', \'' . addslashes($row->jumlah) . '\', \'' . addslashes($row->satuan) . '\', \'' . addslashes($row->nama_kapal) . '\', \'' . addslashes($row->no_cont) . '\', \'' . addslashes($row->no_seal) . '\', \'' . addslashes($row->no_pol) . '\', \'' . addslashes($row->no_job) . '\',  \'' . addslashes($row->tgl_sj) . '\', \'' . addslashes($row->no_po) . '\')" id="edit" class="text-yellow-400 font-semibold mb-3 self-end"><i class="fa-solid fa-pencil"></i></button>';
                                // <button onclick="deleteData(' . $row->id . ')"  id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>';
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
        ->select('no_bm','id_surat_jalan', 'id_supplier', 'invoice_external', 'id_barang',
                 DB::raw('AVG(harga_beli) as avg_harga_beli'),
                 DB::raw('SUM(harga_beli) as sum_harga_beli'),
                 DB::raw('SUM(jumlah_beli) as total_jumlah_beli'))
        ->where('harga_beli', '>', 0)
        ->whereNull('id_surat_jalan')
        ->groupBy('no_bm', 'id_supplier', 'invoice_external')
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
            'sum_harga_beli' => $row->sum_harga_beli ? number_format($row->sum_harga_beli, 4, ',', '.') : '-',
            'jumlah_beli' => $row->total_jumlah_beli ? number_format($row->total_jumlah_beli, 0, ',', '.') : '-',
            'no_bm' => $row->no_bm ?? '-',
            'total' => number_format($subtotal, 2, ',', '.'),
            'ppn' => number_format($ppn, 2, ',', '.'),
            'supplier' => $row->suppliers->nama ?? '-',
            'invoice_external' => $row->invoice_external,
            'aksi' => '<button onclick="getData(' . $row->id_supplier . ', \'' . addslashes($row->suppliers->nama) . '\', \'' . addslashes($row->invoice_external) . '\', ' . $row->avg_harga_beli . ', ' . $row->total_jumlah_beli . ', \'' . ($barang->status_ppn ?? '-') . '\', ' . ($barang->value_ppn ?? 0) . ', \'' . addslashes($tgl_jurnal ?? format('Y-m-d')) . '\', \'' . addslashes($row->no_bm) . '\')" id="edit" class="text-yellow-400 font-semibold self-end"><i class="fa-solid fa-pencil"></i></button>'
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

public function getStock($id)
{
    $trx = Transaction::find($id);

    if (!$trx) {
        return response()->json([
            'message' => 'Transaksi tidak ditemukan'
        ], 404);
    }

    $stock = Transaction::whereNull('id_surat_jalan')
        ->where('no_bm', $trx->no_bm)
        ->where('id_barang', $trx->id_barang)
        ->where('id_supplier', $trx->id_supplier)
        ->first();

        $tag = $stock->barang->nama . ' || Stock ' . $stock->sisa . ' || ' . $stock->no_bm;
        return response()->json([
        'stock' => $tag ?? 'tidak diketahui',
        'trx_jumlah_jual' => $trx->jumlah_jual
    ]);
}


    public function editBarang()
    {
        $transactions = Transaction::with(['suppliers'])->orderBy('id_surat_jalan', 'desc')->whereNotNull('id_surat_jalan')->get();
        $satuans = Satuan::all();
        $suppliers = Supplier::all();
        $stocksNoppn = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
                        ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
                        ->join('suppliers', 'transaksi.id_supplier', '=', 'suppliers.id') // Join langsung ke transaksi
                        ->select(
                            'barang.*',
                            'suppliers.nama as nama_supplier',
                            'suppliers.id as supplier_id',
                            'barang.kode_objek',
                            'satuan.nama_satuan',
                            'transaksi.*'
                        )
                        ->where('barang.status', 'AKTIF')
                        ->where('barang.status_ppn', 'tidak')
                        ->whereNull('id_surat_jalan')
                        ->where('harga_jual',0)
                        ->where('harga_beli', '>', 0)
                        ->where('sisa', '>', 0)
                        ->whereNotNull('transaksi.stts')
                        ->get();

        $stocksPpn = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
                    ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
                    ->join('suppliers', 'transaksi.id_supplier', '=', 'suppliers.id') // Join langsung ke transaksi
                    ->select(
                        'barang.*',
                        'suppliers.nama as nama_supplier',
                        'suppliers.id as supplier_id',
                        'barang.kode_objek',
                        'satuan.nama_satuan',
                        'satuan.id as id_satuan',
                        'transaksi.*'
                    )
                    ->where('barang.status', 'AKTIF')
                    ->where('barang.status_ppn', 'ya')
                    ->whereNull('transaksi.id_surat_jalan')
                    ->where('transaksi.harga_jual', '=', 0)
                    ->where('transaksi.harga_beli', '>', 0)
                    ->where('transaksi.sisa', '>', 0)
                    ->whereNotNull('transaksi.stts')
                    ->get();
        // dd($transactions[0]->suppliers);
        return view('surat_jalan.editBarang', compact('transactions', 'stocksPpn', 'stocksNoppn','satuans','suppliers'));
    }

    public function editBarangPost(Request $request)
    {
        $trx = Transaction::find($request->id);
        
        if (!$trx) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        }
    
        $stock = Transaction::whereNull('id_surat_jalan')
            ->where('no_bm', $trx->no_bm)
            ->where('id_barang', $trx->id_barang)
            ->where('id_supplier', $trx->id_supplier)
            ->first();
    
        if (!$stock) {
            return redirect()->back()->with('error', 'Stok tidak ditemukan.');
        }
        if ($request->qty == 'tambah') {
            $jumlah_jual_stock = $stock->jumlah_jual + $request->tambah;
            $qty = $stock->sisa - $request->tambah;
            $jumlah_jual = $trx->jumlah_jual + $request->tambah;
        } else {
            $jumlah_jual_stock = $stock->jumlah_jual - $request->kurangi;
            $qty = $stock->sisa + $request->kurangi;
            $jumlah_jual = $trx->jumlah_jual - $request->kurangi;
        }        
        if ($qty < 0) { // Jika stok tidak mencukupi
            return redirect()->back()->with(
                'error',
                'Stok tidak mencukupi! Barang: ' . (optional($tstock->barang)->nama ?? 'Tidak diketahui') . 
                ', Sisa Stok: ' . ($stock->sisa ?? 0)
            );
        }
        
        // Update stok awal
        $stock->update([
            'sisa' => $qty,
            'jumlah_jual' => $jumlah_jual_stock
        ]);
        // Update transaksi yang diedit
        Transaction::where('id', $request->id)->update([
            'jumlah_jual' => $jumlah_jual,
            'jumlah_beli' => $stock->jumlah_beli,
            'sisa' => $jumlah_jual,
            'satuan_beli' => $stock->satuan_beli,
            'satuan_jual' => $stock->satuan_jual,
            'keterangan' => $request->keterangan,
        ]);
    
        return redirect()->back()->with('success', 'Data berhasil diupdate!!');
    }
    

    public function hapusBarang(Request $request)
    {
        $trx = Transaction::find($request->id);
        $stock = Transaction::whereNull('id_surat_jalan')
            ->where('no_bm', $trx->no_bm)
            ->where('id_barang', $trx->id_barang)
            ->where('id_supplier', $trx->id_supplier)
            ->first();
        $jumlah_jual = $stock->jumlah_jual - $trx->jumlah_jual;
        $qty = $stock->sisa + $trx->jumlah_jual;
        if($stock->sisa == 0 || $stock->sisa < 0){
            $trx->delete();
        }
        $stock->update([
                'sisa' => $qty,
                'jumlah_jual' => $jumlah_jual
            ]);
        $trx->delete();
        return redirect()->back()->with('success', 'Data barang berhasil dihapus.');
    }
    

    public function tambahBarang(Request $request)
    {
        $ppn = $request->id_barang_ppn ?? null;
        $noppn = $request->id_barang_no_ppn ?? null;
        $trans = Transaction::find($ppn ?? $noppn);
        $sisa = $trans->sisa - $request->jumlah_jual;
        $bkeluar = $request->jumlah_jual + $trans->jumlah_jual;
        Transaction::create([
            'id_surat_jalan' => $request->id_surat_jalan,
            'id_barang' => $trans->id_barang,
            'id_supplier' => $trans->id_supplier,
            'jumlah_jual' => $request->jumlah_jual,
            'invoice_external' => $trans->invoice_external,
            'jumlah_beli' => $trans->jumlah_beli,
            'sisa' => $request->jumlah_jual,
            'satuan_jual' => $trans->satuan_beli,
            'satuan_beli' => $trans->satuan_beli,
            'harga_jual' => 0,
            'harga_beli' => $trans->harga_beli,
            'no_bm' => $trans->no_bm,
            'stts' => $trans->stts,
            'keterangan' => $request->keterangan
        ]);
        $trans->update([
            'sisa' => $sisa,
            'jumlah_jual' => $bkeluar,
            'satuan_jual' =>  $request->satuan_jual
        ]);


        return redirect()->back()->with('success', 'Data barang berhasil ditambahkan.');
    }
}
