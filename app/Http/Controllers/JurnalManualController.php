<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\Supplier;
use App\Models\SuratJalan;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use App\Models\TipeJurnal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JurnalManualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = TemplateJurnal::all();
        $nopol = Nopol::where('status', 'aktif')->get();
        $coa = Coa::where('status', 'aktif')->get();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $noJNL = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $currentMonth)->orderBy('no', 'desc')->first() ?? 0;
        $no_JNL = $noJNL ? $noJNL->no + 1 : 1;
        $noBKK = Jurnal::where('tipe', 'BKK')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        $no_BKK = $noBKK ? $noBKK->no + 1 : 1;
        $noBKM = Jurnal::where('tipe', 'BKM')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        $no_BKM = $noBKM ? $noBKM->no + 1 : 1;
        $noBBK = Jurnal::where('tipe', 'BBK')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        $no_BBK = $noBBK ? $noBBK->no + 1 : 1;
        session(['no_BBK' => $no_BBK]);
        $noBBM = Jurnal::where('tipe', 'BBM')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        $no_BBM = $noBBM ? $noBBM->no + 1 : 1;
        $noBBMO = Jurnal::where('tipe', 'BBMO')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        $no_BBMO = $noBBMO ? $noBBMO->no + 1 : 1;
        $noBBKO = Jurnal::where('tipe', 'BBKO')->whereYear('tgl', $currentYear)->orderBy('no', 'desc')->first() ?? 0;
        
        $no_BBKO = $noBBKO ? $noBBKO->no + 1 : 1;

        $invoices = Invoice::all();
        $processedInvoices = [];
        $invoiceCounts = [];
        foreach ($invoices as $invoice) {
            $invoiceNumber = $invoice->invoice;
            if (!isset($invoiceCounts[$invoiceNumber])) {
                $invoiceCounts[$invoiceNumber] = 0;
            }
            $invoiceCounts[$invoiceNumber]++;

            $processedInvoiceNumber = $invoiceNumber . '_' . $invoiceCounts[$invoiceNumber];
            $processedInvoices[] = $processedInvoiceNumber;
        }

        $transaksi = Transaction::whereNotNull('invoice_external')
        ->leftJoin('invoice', 'transaksi.id', '=', 'invoice.id_transaksi')
        ->select('transaksi.*', 'invoice.*')
        ->get();    
        $procTransactions = [];
        $transactionCounts = [];
        foreach ($transaksi as $transaction) {
            $invoiceNumber = $transaction->invoice_external;
            if (!isset($transactionCounts[$invoiceNumber])) {
                $transactionCounts[$invoiceNumber] = 0;
            }
            $transactionCounts[$invoiceNumber]++;
            $invoiceValue = $transaction->invoice ? $transaction->invoice : '-';
            $procTransactionNumber = $invoiceNumber . '_' . $transactionCounts[$invoiceNumber] . ' | ' . $invoiceValue;
            $procTransactions[] = $procTransactionNumber;
        }
        $uniqueNomors = DB::table('jurnal')
        ->whereNotNull('nomor')
      
        ->distinct()
        ->pluck('nomor');

        // dd($procTransactions);
        return view('jurnal.jurnal-manual', compact('templates', 'nopol', 'coa', 'no_BBMO', 'no_BBKO', 'no_JNL', 'no_BKK', 'no_BKM', 'no_BBK', 'no_BBM', 'invoices', 'processedInvoices', 'procTransactions', 'transaksi','uniqueNomors', 'transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = TemplateJurnal::get();
        $coa = Coa::where('status', 'aktif')->get();
        $nopol = Nopol::where('status', 'aktif')->get();
        return view('jurnal.jurnal-manual', compact('templates', 'coa', 'nopol'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $month = $request->tanggal_jurnal;
        $month1 = date('m', strtotime($month));
        $month2 = date('m');
        $nomor = $month1. '-' . $request->tipe;
        $data_nomor = explode('/', $request->tipe)[1];
        $tipe = explode('-', $data_nomor)[0];
        $noCounter = explode('-', $nomor)[1];
        $no = str_replace(' ', '', explode('/', $noCounter)[0]);
        if (strpos($nomor, 'JNL') !== false) {
            // Buang "JNL" dari string
            $nomor = str_replace('JNL', '', $nomor);
            
            // Lakukan explode dengan delimiter yang sesuai, misal spasi
            $nomor = explode(' ', $nomor);
            // Mengambil elemen pertama dari array jika explode menghasilkan array
            $nomor = $nomor[0];
            $data_nomor = explode('/', $request->tipe)[1];
            $tipe = 'JNL';
            $noCounter = explode('-', $nomor)[1];
            $no = str_replace(' ', '', explode('/', $noCounter)[0]);
        } else {
            $nomor =$request->tipe;
        }
        // dd($nomor);

        $tgl = $request->tanggal_jurnal;
        $bulan = date('m', strtotime($tgl));
        $bulanNow = date('m');

        //pemecahan nomor jurnal
        $jurnalsort = Jurnal::whereMonth('tgl', $bulan)->where('tipe', 'JNL')->get();
        $nomorArray = $jurnalsort->pluck('no')->toArray();
        if ($nomorArray == []) {
            $nomorArray = [0];
        }
        $maxNomor = max($nomorArray); // max nmor pada bulan yang diinputkan user
        // penggabungan nomor jurnal untuk kondisi jurnal yang diinputkan bulannya tidak sama dengan bulan sekarang
        $breakdown = explode('/', $nomor);
        $sec2 = $breakdown[1];
        $sec2 = str_replace('-', '', $sec2);
        $sec3 = $breakdown[2];
        $title = $request->tanggal_jurnal;
        $title1 = date('m', strtotime($title));
        $newNoJurnal = $title1 . '-' . $maxNomor + 1 . '/' . $sec2 . '/' . $sec3;
        $keteranganList = [];
        $ket_arr = [];
        foreach ($request->keterangan as $key => $ket) {
            array_push($ket_arr, $ket);
        }

        // dd($request->keterangan);
        for ($i = 0; $i < $request->counter; $i++) {
            $keterangan = $ket_arr[$i];
            if (str_contains($ket_arr[$i], '[1]')) {
                if ($request->param1[$i] != null) {
                    $keterangan = str_replace('[1]', $request->param1[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[2]')) {
                if ($request->param2[$i] != null) {
                    $keterangan = str_replace('[2]', $request->param2[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[3]')) {
                if ($request->param3[$i] != null) {
                    $keterangan = str_replace('[3]', $request->param3[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[4]')) {
                if ($request->param4[$i] != null) {
                    $keterangan = str_replace('[4]', $request->param4[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[5]')) {
                if ($request->param5[$i] != null) {
                    $keterangan = str_replace('[5]', $request->param5[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[6]')) {
                if ($request->param6[$i] != null) {
                    $keterangan = str_replace('[6]', $request->param6[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[7]')) {
                if ($request->param7[$i] != null) {
                    $keterangan = str_replace('[7]', $request->param7[$i], $keterangan);
                }
            }
            if (str_contains($ket_arr[$i], '[8]')) {
                if ($request->param8[$i] != null) {
                    $keterangan = str_replace('[8]', $request->param8[$i], $keterangan);
                }
            }

            $keteranganList[$i] = $keterangan;
        }

        // dd($request->nominal, $request->keterangan);
        for ($i = 0; $i < $request->counter; $i++) {
            if ($request->check[$i] == 1) {
                if ($tipe == 'JNL') {
                    if ($bulan < $bulanNow) {
                        DB::transaction(
                            function () use ($request, $i, $tipe, $keteranganList, $maxNomor, $newNoJurnal) {
                                if ($request->akun_debet[$i] != 0) {
                                    if($request->invoice != 0) {
                                        $invc = explode('_', $request->invoice[$i])[0];
                                        $result = Invoice::where('invoice', $invc)->get();
                                    } else if($request->invoice_external != 0) {
                                        $invx = explode('_', $request->invoice_external[$i])[0];
                                        $result = Invoice::where('invoice', $invx)->get();
                                    }
                                    Jurnal::create([
                                        'coa_id' => $request->akun_debet[$i],
                                        'nomor' => $newNoJurnal,
                                        'tgl' => $request->tanggal_jurnal,
                                        'keterangan' => $keteranganList[$i],
                                        'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $newNoJurnal,
                                        'debit' => $request->nominal[$i],
                                        'kredit' => 0,
                                        'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : 0,
                                        'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                        'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                        'nopol' => $request->nopol[$i] ?? null,
                                        'tipe' => $tipe,
                                        'no' => $maxNomor + 1,

                                        
                                    ]);
                                }
    
                                if ($request->akun_kredit[$i] != 0) {
                                    if($request->invoice != 0) {
                                        $invc = explode('_', $request->invoice[$i])[0];
                                        $result = Invoice::where('invoice', $invc)->get();
                                    } else if($request->invoice_external != 0) {
                                        $invx = explode('_', $request->invoice_external[$i])[0];
                                        $result = Invoice::where('invoice', $invx)->get();
                                    }
                                    Jurnal::create([
                                        'coa_id' => $request->akun_kredit[$i],
                                        'nomor' => $newNoJurnal,
                                        'tgl' => $request->tanggal_jurnal,
                                        'keterangan' => $keteranganList[$i],
                                        'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $newNoJurnal,
                                        'debit' => 0,
                                        'kredit' => $request->nominal[$i],
                                        'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : null,
                                        'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                        'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                        'nopol' => $request->nopol[$i] ?? null,
                                        'tipe' => $tipe,
                                        'no' => $maxNomor + 1,
                                       
                                    ]);
                                }
                            }
                        );
                    }else {
                        // dd($request, $i, $nomor, $tipe, $no, $keteranganList[0]);
                        DB::transaction(
                            function () use ($request, $i, $nomor, $tipe, $no, $keteranganList) {
                                if ($request->akun_debet[$i] != 0) {
                                    if($request->invoice != 0) {
                                        $invc = explode('_', $request->invoice[$i])[0];
                                        $result = Invoice::where('invoice', $invc)->get();
                                    } else if($request->invoice_external != 0) {
                                        $invx = explode('_', $request->invoice_external[$i])[0];
                                        $result = Invoice::where('invoice', $invx)->get();
                                    }
                                    Jurnal::create([
                                        'coa_id' => $request->akun_debet[$i],
                                        'nomor' => $nomor,
                                        'tgl' => $request->tanggal_jurnal,
                                        'keterangan' => $keteranganList[$i],
                                        'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $nomor,
                                        'debit' => $request->nominal[$i],
                                        'kredit' => 0,
                                        'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : null,
                                        'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                        'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                        'nopol' => $request->nopol[$i] ?? null,
                                        'tipe' => $tipe,
                                        'no' => $no,
                                        
                                    ]);
                                }
    
                                if ($request->akun_kredit[$i] != 0) {
                                    if($request->invoice != 0) {
                                        $invc = explode('_', $request->invoice[$i])[0];
                                        $result = Invoice::where('invoice', $invc)->get();
                                    } else if($request->invoice_external != 0) {
                                        $invx = explode('_', $request->invoice_external[$i])[0];
                                        $result = Invoice::where('invoice', $invx)->get();
                                    }
                                    Jurnal::create([
                                        'coa_id' => $request->akun_kredit[$i],
                                        'nomor' => $nomor,
                                        'tgl' => $request->tanggal_jurnal,
                                        'keterangan' => $keteranganList[$i],
                                        'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $nomor,
                                        'debit' => 0,
                                        'kredit' => $request->nominal[$i],
                                        'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : null,
                                        'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                        'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                        'nopol' => $request->nopol[$i] ?? null,
                                        'tipe' => $tipe,
                                        'no' => $no,
                                       
                                    ]);
                                }
                            }
                        );
                    }
                } else {
                    // Kode untuk tipe selain 'JNL'
                    DB::transaction(
                        function () use ($request, $i, $nomor, $tipe, $no, $keteranganList) {
                            if ($request->akun_debet[$i] != 0) {
                                if($request->invoice != 0) {
                                    $invc = explode('_', $request->invoice[$i])[0];
                                    $result = Invoice::where('invoice', $invc)->get();
                                } else if($request->invoice_external != 0) {
                                    $invx = explode('_', $request->invoice_external[$i])[0];
                                    $result = Invoice::where('invoice', $invx)->get();
                                }
                                Jurnal::create([
                                    'coa_id' => $request->akun_debet[$i],
                                    'nomor' => $nomor,
                                    'tgl' => $request->tanggal_jurnal,
                                    'keterangan' => $keteranganList[$i],
                                    'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $nomor,
                                    'debit' => $request->nominal[$i],
                                    'kredit' => 0,
                                    'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : null,
                                    'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                    'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                    'nopol' => $request->nopol[$i] ?? null,
                                    'tipe' => $tipe,
                                    'no' => $no,
                                    
                                ]);
                            }

                            if ($request->akun_kredit[$i] != 0) {
                                if($request->invoice != 0) {
                                    $invc = explode('_', $request->invoice[$i])[0];
                                    $result = Invoice::where('invoice', $invc)->get();
                                } else if($request->invoice_external != 0) {
                                    $invx = explode('_', $request->invoice_external[$i])[0];
                                    $result = Invoice::where('invoice', $invx)->get();
                                }
                                Jurnal::create([
                                    'coa_id' => $request->akun_kredit[$i],
                                    'nomor' => $nomor,
                                    'tgl' => $request->tanggal_jurnal,
                                    'keterangan' => $keteranganList[$i],
                                    'keterangan_buku_besar_pembantu' => !empty($request->keterangan_buku_besar_pembantu[$i]) ? $request->keterangan_buku_besar_pembantu[$i] : $nomor,
                                    'debit' => 0,
                                    'kredit' => $request->nominal[$i],
                                    'invoice' => $request->invoice ? explode('_', $request->invoice[$i])[0] : null,
                                    'invoice_external' => $request->invoice_external ? explode('_', $request->invoice_external[$i])[0] : null,
                                    'id_transaksi' => $result[$i]['id_transaksi'] ?? null,
                                    'nopol' => $request->nopol[$i] ?? null,
                                    'tipe' => $tipe,
                                    'no' => $no,
                                   
                                ]);
                            }
                        }

                    );
                }
            }
        }

        return redirect()->route('jurnal.index')->with('success', 'Data jurnal berhasil disimpan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Jurnal $jurnal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurnal $jurnal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
    {
        //
    }

    public function getInvoiceWhereNoInv()
    {
        // dd(request('invoice'));
        $invoices = Invoice::with([
            'transaksi.suppliers',
            'transaksi.barang',
            'transaksi.suratJalan.customer',
        ])
            ->where('invoice', request('invoice'))
            ->get();

        $dataCust = [];
        $dataSup = [];
        $dataBar = [];
        $dataQty = [];
        $dataSat = [];
        $dataHarsBel = [];
        $dataHarsJul = [];
        $dataKet = [];

        if ($invoices->isNotEmpty()) {
            foreach ($invoices as $invoice) {
                $dataCust[] = $invoice->transaksi->suratJalan->customer->nama;
                $dataSup[] = $invoice->transaksi->suppliers->nama;
                $dataBar[] = $invoice->transaksi->barang->nama;
                $dataQty[] = $invoice->transaksi->jumlah_jual;
                $dataSat[] = $invoice->transaksi->satuan_jual;
                $dataHarsBel[] = $invoice->transaksi->harga_beli;
                $dataHarsJul[] = $invoice->transaksi->harga_jual;
                $dataKet[] = $invoice->transaksi->keterangan;
            }
        } else {
            return response()->json(['error' => 'No invoices found'], 404);
        }

        // dd($suratJalans);

        return response()->json([
            'customer' => $dataCust,
            'supplier' => $dataSup,
            'barang' => $dataBar,
            'quantity' => $dataQty,
            'satuan' => $dataSat,
            'harsat_beli' => $dataHarsBel,
            'harsat_jual' => $dataHarsJul,
            'keterangan' => $dataKet
        ]);
    }

    public function getInvoiceWhereNoInvExt(Request $request)
    {
        // Validasi input
        $request->validate([
            'invoice_ext' => 'required|string',
        ]);

        // Ambil data transaksi berdasarkan invoice_external
        $invoiceExt = Transaction::where('invoice_external', $request->invoice_ext)
            ->with(['suratJalan.customer', 'barang', 'suppliers'])
            ->get();

        // Siapkan array untuk menyimpan data
        $dataCust = [];
        $dataSup = [];
        $dataBar = [];
        $dataQty = [];
        $dataSat = [];
        $dataHarsBel = [];
        $dataHarsJul = [];
        $dataKet = [];

        // Periksa apakah ada data yang ditemukan
        if ($invoiceExt->isNotEmpty()) {
            foreach ($invoiceExt as $item) {
                // Ambil data yang diperlukan dari relasi
                $dataCust[] = $item->suratJalan->customer->nama ?? ''; // Menggunakan null coalescing operator untuk menghindari error
                $dataSup[] = $item->suppliers->nama ?? '';
                $dataBar[] = $item->barang->nama ?? '';
                $dataQty[] = $item->jumlah_jual;
                $dataSat[] = $item->satuan_jual;
                $dataHarsBel[] = $item->harga_beli;
                $dataHarsJul[] = $item->harga_jual;
                $dataKet[] = $item->keterangan;
            }
        } else {
            return response()->json(['error' => 'No invoices found'], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json([
            'customer' => $dataCust,
            'supplier' => $dataSup,
            'barang' => $dataBar,
            'quantity' => $dataQty,
            'satuan' => $dataSat,
            'harsat_beli' => $dataHarsBel,
            'harsat_jual' => $dataHarsJul,
            'keterangan' => $dataKet
        ]);
    }
    

    public function terapanTemplateJurnal()
    {
        $data = TemplateJurnalItem::where('template_jurnal_id', request('template'))
            ->with([
                'coa_debit',
                'coa_kredit'
            ])
            ->get();
        $count = count($data);
        $coa_debit = [];
        $coa_kredit = [];
        $keterangan = [];

        foreach ($data as $d) {
            $coa_debit[] = $d->coa_debit;
            $coa_kredit[] = $d->coa_kredit;
            $keterangan[] = $d->keterangan;
        }

        return response()->json([
            'count' => $count,
            'coa_debit' => $coa_debit,
            'coa_kredit' => $coa_kredit,
            'keterangan' => $keterangan
        ]);
    }

    public function transaksi(Request $request)
    {
        // Mengambil parameter pencarian dari request
        $searchTerm = $request->get('search');
    
        // Membangun query untuk mengambil data transaksi
        $query = Transaction::with(['suratJalan', 'suppliers'])
            ->whereNull('invoice_external')
            ->groupBy('id_surat_jalan', 'id_supplier', 'invoice_external')
            ->orderBy('invoice_external');
    
        // Jika ada parameter pencarian, tambahkan kondisi pencarian
        if ($searchTerm) {
            $query->whereHas('suratJalan', function ($q) use ($searchTerm) {
                // Mencari nomor surat yang sesuai dengan input pencarian
                $q->where('nomor_surat', 'like', '%' . $searchTerm . '%'); // Sesuaikan dengan nama kolom yang sesuai
            });
        }
    
        // Ambil data dengan query yang sudah dibangun
        $data = $query->get(['id', 'id_surat_jalan', 'id_supplier']); 
    
        // Mengembalikan respons dalam format JSON
        return response()->json($data);
    }
    

    public function Jurnalhutang(Request $request){
        $no_SJ = $request->id_surat_jalan;
        $invoice_e = $request->invoice_external;
        $supplier = $request->id_supplier;
        $time = date('Y-m-d');
        $year = date('y', strtotime($time));
        $month = date('m', strtotime($time));
        $jnl = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $month)->orderBy('no', 'desc')->first() ?? 0;
        $nojnl =  $jnl ? $jnl->no + 1 : 1;
        $nomor = $month . '-' . $nojnl . '/' . 'SB' . '/' . $year;
        $transaksi = Transaction::where('id_surat_jalan', $no_SJ)->get();
        $core =  Transaction::where('id_surat_jalan', $request->id_surat_jalan)
        ->where('id_supplier', $request->id_supplier)
        ->get();
        $nopol =$core[0]->suratJalan->no_pol;
        $subtotal = $core->sum(function ($item) {
            return round($item->harga_beli * $item->jumlah_jual);
        });
        // $invoice_count = 1;
        // $nsfp = NSFP::where('available', '1')->orderBy('nomor')->take($invoice_count)->get();


        Transaction::where('id_surat_jalan', $request->id_surat_jalan)
        ->where('id_supplier', $request->id_supplier)
        ->update(['invoice_external' => $request->invoice_external]);

        if ($core[0]->barang->status_ppn == 'ya') {
                $value_ppn = $core[0]->barang->value_ppn / 100;

                $subtotalPPN = $core->sum(function ($item) {
                    $value_ppn = $item->barang->value_ppn / 100;
                    return round($item->harga_beli * $item->jumlah_jual * $value_ppn);
                });
               
            foreach ($core as $item) {
                Jurnal::create([
                    'coa_id' => 63,
                    'nomor' => $nomor,
                    'tgl' => $time,
                    'keterangan' => 'Pembelian ' . $item->barang->nama . 
                ' (' . number_format($item->jumlah_jual, 2, ',', '.') . ' ' . 
                $item->satuan_jual . ' Harsat ' . 
                number_format($item->harga_beli, 2, ',', '.') . ') ' . 
                ' untuk ' . $item->suratJalan->customer->nama,
                    'debit' => round($item->harga_beli * $item->jumlah_jual), // Debit diisi 0
                    'kredit' => 0, // Menggunakan total debit sebagai kredit
                    'invoice' => '',
                    'invoice_external' => $invoice_e,
                    'id_transaksi' => $request->id,
                    'nopol' => $nopol,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $nojnl
                ]);
            }
            Jurnal::create([
                'coa_id' => 10,
                'nomor' => $nomor,
                'tgl' => $time,
                'keterangan' => 'PPN Masukkan ' . $core[0]->suppliers->nama . '(FP : ---)' ,
                'debit' => $subtotalPPN, // Menyimpan subtotal sebagai debit
                'kredit' =>  0,// Kredit diisi 0
                'invoice' => '',
                'invoice_external' => $invoice_e,
                'id_transaksi' => $request->id,
                'nopol' => $nopol,
                'container' => null,
                'tipe' => 'JNL',
                'no' => $nojnl
            ]);

            Jurnal::create([
                'coa_id' => 35,
                'nomor' => $nomor,
                'tgl' => $time,
                'keterangan' => 'Hutang ' . $item->suppliers->nama,
                'debit' => 0, // Menyimpan subtotal sebagai debit
                'kredit' => round($subtotal + $subtotalPPN), // Kredit diisi 0
                'invoice' => '',
                'invoice_external' => $invoice_e,
                'id_transaksi' => $request->id,
                'nopol' => $nopol,
                'container' => null,
                'tipe' => 'JNL',
                'no' => $nojnl
            ]);
        } else {
            foreach ($core as $item) {
            Jurnal::create([
                'coa_id' => 63,
                'nomor' => $nomor,
                'tgl' => $time,
                'keterangan' => 'Pembelian ' . $item->barang->nama . 
                ' (' . number_format($item->jumlah_jual, 2, ',', '.') . ' ' . 
                $item->satuan_jual . ' Harsat ' . 
                number_format($item->harga_beli, 2, ',', '.') . ') ' . 
                ' untuk ' . $item->suratJalan->customer->nama,
                'debit' => round($item->harga_beli * $item->jumlah_jual), // Debit diisi 0
                'kredit' => 0, // Menggunakan total debit sebagai kredit
                'invoice' => '',
                'invoice_external' => $invoice_e,
                'id_transaksi' => $request->id,
                'nopol' => $nopol,
                'container' => null,
                'tipe' => 'JNL',
                'no' => $nojnl
            ]);
        }
                // Membuat entri jurnal untuk debit
                Jurnal::create([
                    'coa_id' => 35,
                    'nomor' => $nomor,
                    'tgl' => $time,
                    'keterangan' => 'Hutang ' . $item->suppliers->nama,
                    'debit' => 0, // Menyimpan subtotal sebagai debit
                    'kredit' => $subtotal, // Kredit diisi 0
                    'invoice' => '',
                    'invoice_external' => $invoice_e,
                    'id_transaksi' => $request->id,
                    'nopol' => $nopol,
                    'container' => null,
                    'tipe' => 'JNL',
                    'no' => $nojnl
                ]);
        }

        return redirect()->route('jurnal.index')->with('success', 'Data jurnal hutang berhasil disimpan.');
    }
    
}