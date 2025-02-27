<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\Supplier;
use App\Models\SuratJalan;
use App\Models\TemplateJurnal;
use App\Models\TipeJurnal;
use App\Models\Barang;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JurnalALLExport;
use Yajra\DataTables\Facades\DataTables;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (isset($_GET['tipe']) && isset($_GET['month']) && isset($_GET['year'])) {
            $data = Jurnal::whereMonth('tgl', $_GET['month'])->whereYear('tgl', $_GET['year'])->where('tipe', $_GET['tipe'])->orderBy('no', 'desc')->orderBy('created_at', 'desc')->orderBy('id', 'desc')->orderBy('tgl', 'desc')->join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')->get();
        } elseif (isset($_GET['month']) && isset($_GET['year'])) {
            $data = Jurnal::whereMonth('tgl', $_GET['month'])->whereYear('tgl', $_GET['year'])->orderBy('no', 'desc')->orderBy('created_at', 'desc')->orderBy('id', 'desc')->orderBy('tgl', 'desc')->join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')->get();
        } else {
            $data = Jurnal::join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')->orderBy('no', 'desc')->orderBy('created_at', 'desc')->orderBy('id', 'desc')->orderBy('tgl', 'desc')->get();
        }

        // bulan
        if(isset($_GET['month']) && isset($_GET['year'])) {
            $MonJNL = Jurnal::whereMonth('tgl', $_GET ['month'])
            ->whereYear('tgl', $_GET['year'])
            ->join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')
            ->get();
            
            $balance = Jurnal::select('nomor', 
                              DB::raw('SUM(debit) as total_debit'), 
                              DB::raw('SUM(kredit) as total_kredit'))
            ->whereYear('tgl', $_GET['year'])
            ->groupBy('nomor')
            ->get();
            $LastJNL = Jurnal::whereMonth('tgl', $_GET['month'])
            ->whereYear('tgl',  $_GET['year'])
            ->where('tipe', 'JNL')->
            join('coa', 'jurnal.coa_id', '=', 'coa.id')
            ->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')
            ->get();

            $notBalance = [];
            for ($i = 0; $i < count($balance); $i++) {
                if ($balance[$i]->total_debit != $balance[$i]->total_kredit) {
                    $notBalance[] = $balance[$i]->nomor;
                }
            }
            // tahun
        } else {
            $MonJNL = Jurnal::join('coa', 'jurnal.coa_id', '=', 'coa.id')
            ->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')
            ->get();
            $balance = Jurnal::select('nomor', 
                              DB::raw('SUM(debit) as total_debit'), 
                              DB::raw('SUM(kredit) as total_kredit'))
            ->whereYear('tgl', date('Y'))
            ->groupBy('nomor')
            ->get();

            $LastJNL = Jurnal::where('tipe', 'JNL')
            ->join('coa', 'jurnal.coa_id', '=', 'coa.id')
            ->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')
            ->get();

            $notBalance = [];
            
            for($i = 0; $i < count($balance); $i++) {
                if($balance[$i]->total_debit != $balance[$i]->total_kredit) {
                    $notBalance[] = $balance[$i]->nomor;
                }
            }
            
        }

        return view('jurnal.jurnal', compact('data', 'MonJNL',
        'notBalance', 'LastJNL'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        // dd($_GET['tipe']);
        // dd($data[0]->nomor);
        $nomor = $_GET['nomor'];
        // dd($nomor);
        $jurnal = Jurnal::where('nomor', $nomor) ->orderBy('id', 'desc')->first();
        $tgl = $jurnal->tgl;
        $data = Jurnal::where('nomor', $nomor)->join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun') ->orderBy('id', 'asc')->get();
        $coa = Coa::where('status', 'aktif')->get();
        $nopol = Nopol::where('status', 'aktif')->get();
        $jurnals = Jurnal::select('keterangan_buku_besar_pembantu') // Tambahkan 'id' ke dalam select
        ->whereNotNull('keterangan_buku_besar_pembantu')
        ->distinct()
        ->orderBy('id', 'desc')
        ->get();

    
        $id_jurnals = Jurnal::select('id') // Tambahkan 'id' ke dalam select
        ->orderBy('id', 'desc')
        ->get(); // Mengambil koleksi ID
        $latestId = $id_jurnals->pluck('id')->first(); // Mengambil ID terbaru (karena data sudah diurutkan desc)
    
        $cekVoucher = Jurnal::where('nomor', $nomor)->whereIn('coa_id', [2, 5, 6])->get();
        $cekVoucher_d = $cekVoucher->pluck('debit');
        $cekVoucher_k = $cekVoucher->pluck('kredit');



        // dd($jurnals);

        $invoices = Invoice::all();
        $invProc = [];
        $invoiceCounts = [];
        foreach ($invoices as $invoice) {
            $invoiceNumber = $invoice->invoice;
            if (!isset($invoiceCounts[$invoiceNumber])) {
                $invoiceCounts[$invoiceNumber] = 0;
            }
            $invoiceCounts[$invoiceNumber]++;

            $processedInvoiceNumber = $invoiceNumber . '_' . $invoiceCounts[$invoiceNumber];
            $invProc[] = $processedInvoiceNumber;
        }


        $invext = Transaction::whereNot('invoice_external', null)->get();
        $invExtProc = [];
        $transactionCounts = [];
        foreach ($invext as $transaction) {
            $invoiceNumber = $transaction->invoice_external;
            if (!isset($transactionCounts[$invoiceNumber])) {
                $transactionCounts[$invoiceNumber] = 0;
            }
            $transactionCounts[$invoiceNumber]++;

            $procTransactionNumber = $invoiceNumber . '_' . $transactionCounts[$invoiceNumber];
            $invExtProc[] = $procTransactionNumber;
        }


        session(['jurnal_edit_url' => url()->full()]);
        return view('jurnal.edit-jurnal', compact('latestId','cekVoucher_d', 'cekVoucher_k','jurnals','data', 'tgl', 'coa', 'nopol', 'invProc', 'invExtProc'));
    }

    public function merger()
    {
        $jurnal = Jurnal::groupBy('nomor')->orderBy('nomor', 'asc')->get();
        return view('jurnal.jurnal-merger', compact('jurnal'));
    }

    function merger_store(Request $request)
    {
        $tujuan = Jurnal::where('nomor', $request->jurnal_tujuan)->first();
        Jurnal::where('nomor', $request->jurnal_awal)->update([
            'nomor' => $tujuan->nomor,
            'no' => $tujuan->no,
            'tipe' => $tujuan->tipe,
            'tgl' => $tujuan->tgl
        ]);

        return to_route('jurnal.index')->with('success', 'Merge No. Jurnal berhasil');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        // dd($request->all());
        if ($request->invoice != null) {
            if (str_contains($request->invoice, '_')) {
                $inv = explode('_', $request->invoice)[0];
                $index = explode('_', $request->invoice)[1];
                $invoices = Invoice::with([
                    'transaksi.suppliers',
                    'transaksi.barang',
                    'transaksi.suratJalan.customer',
                ])
                    ->where('invoice', $inv)
                    ->get();
                    $barang = $invoices[$index - 1]->transaksi->barang->nama;
                    $supplier = $invoices[$index - 1]->transaksi->suppliers->nama;
                    $customer = $invoices[$index - 1]->transaksi->suratJalan->customer->nama;
                    $quantity = $invoices[$index - 1]->transaksi->jumlah_jual;
                $satuan = $invoices[$index - 1]->transaksi->satuan_jual;
                $hargabeli = $invoices[$index - 1]->transaksi->harga_beli;
                $hargajual = $invoices[$index - 1]->transaksi->harga_jual;
                $ket = $invoices[$index - 1]->transaksi->keterangan;
                
                // dd($customer, $satuan, $quantity, $hargabeli, $hargajual, $ket, $supplier, $barang);
                $keterangan = $request->keterangan;
                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $id_transaksi = Invoice::where('invoice', $inv)->where('harga', $hargajual)->pluck('id_transaksi')->first();
                
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::find($request->id);
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->nomor = $request->nomor;
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keterangan;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice = $request->invoice;
                $invoices = Invoice::where('invoice', $invoice)->with([
                    'transaksi.suppliers',
                    'transaksi.barang',
                    'transaksi.suratJalan.customer',
                    ])->get();

                
                $barang = $invoices[0]->transaksi->barang->nama;
                $supplier = $invoices[0]->transaksi->suppliers->nama;
                $customer = $invoices[0]->transaksi->suratJalan->customer->nama;
                $quantity = $invoices[0]->transaksi->jumlah_jual;
                $satuan = $invoices[0]->transaksi->satuan_jual;
                $hargabeli = $invoices[0]->transaksi->harga_beli;
                $hargajual = $invoices[0]->transaksi->harga_jual;
                $ket = $invoices[0]->transaksi->keterangan;
                
                

                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $request->keterangan;
                $id_transaksi = Invoice::where('invoice', $invoice)->where('harga', $hargajual)->pluck('id_transaksi')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::find($request->id);
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->nomor = $request->nomor;
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keteranganNow;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else if ($request->invoice_external ) {
            if (str_contains($request->invoice_external, '_')) {
                $invext = explode('_', $request->invoice_external)[0];
                $index = explode('_', $request->invoice_external)[1];
                
                
                $invoice_external = Transaction::where('invoice_external', $invext)
                ->with(['suratJalan.customer', 'barang', 'suppliers'])
                ->whereNotNull('id_surat_jalan')
                ->get();
                
                
                $barang = $invoice_external[$index - 1]->barang->nama;
                $supplier = $invoice_external[$index - 1]->suppliers->nama;
                $customer = $invoice_external[$index - 1]->suratJalan->customer->nama ??$invoice_external[$index - 1]->suratJalan->customer->nama ;
                $quantity = $invoice_external[$index - 1]->jumlah_jual;
                $satuan = $invoice_external[$index - 1]->satuan_jual;
                $hargabeli = $invoice_external[$index - 1]->harga_beli;
                $hargajual = $invoice_external[$index - 1]->harga_jual;
                $ket = $invoice_external[$index - 1]->keterangan;
                $invoice_external= $request->invoice_external ?? '';
                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invext)->where('id_barang', $id_barang)->pluck('id')->first();
                $keteranganNow = $keterangan;
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::find($request->id);
                $data->nomor = $request->nomor;
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keteranganNow;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice_external = $request->invoice_external;
                $invoiceExternal = Transaction::where('invoice_external', $request->invoice_external)
                ->with(['suratJalan.customer', 'barang', 'suppliers'])
                ->get();
                $barang = $invoiceExternal[0]->barang->nama ?? null;
                $supplier = $invoiceExternal[0]->suppliers->nama ?? null;
                $customer = $invoiceExternal[0]->suratJalan->customer->nama ?? null;
                $quantity = $invoiceExternal[0]->jumlah_jual ?? null;
                $satuan = $invoiceExternal[0]->satuan_jual ?? null;
                $hargabeli = $invoiceExternal[0]->harga_beli ?? null;
                $hargajual = $invoiceExternal[0]->harga_jual ?? null;
                $ket = $invoiceExternal[0]->keterangan ?? null;
                
                // dd($request->invoice_external);
                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invoice_external)->where('id_barang', $id_barang)->pluck('id')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                $data = Jurnal::find($request->id);
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->nomor = $request->nomor;
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keteranganNow;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;
                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data->nomor, $data->tgl));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else if ($request->keterangan) {
            if (str_contains($request->invoice_external, '_')) {
                $invext = explode('_', $request->invoice_external)[0];
                $index = explode('_', $request->invoice_external)[1];

                $invoice_external = Transaction::where('invoice_external', $request->invoice_external)
                    ->with(['suratJalan.customer', 'barang', 'suppliers'])
                    ->get();

                $barang = $invoice_external[$index - 1]->barang->nama;
                $supplier = $invoice_external[$index - 1]->suppliers->nama;
                $customer = $invoice_external[$index - 1]->suratJalan->customer->nama;
                $quantity = $invoice_external[$index - 1]->jumlah_jual;
                $satuan = $invoice_external[$index - 1]->satuan_jual;
                $hargabeli = $invoice_external[$index - 1]->harga_beli;
                $hargajual = $invoice_external[$index - 1]->harga_jual;
                $ket = $invoice_external[$index - 1]->keterangan;

                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invext)->where('id_barang', $id_barang)->pluck('id')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                $data = Jurnal::find($request->id);
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->nomor = $request->nomor;
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keteranganNow;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice_external = $request->invoice_external;
                if ($invoice_external != null) {
                $invoiceExternal = Transaction::where('invoice_external', $request->invoice_external)
                    ->with(['suratJalan.customer', 'barang', 'suppliers'])
                    ->get();

                    
                        $barang = optional(optional($invoiceExternal[0])->barang)->nama;
                        $supplier = optional(optional($invoiceExternal[0])->suppliers)->nama;
                        $customer = optional(optional(optional($invoiceExternal[0])->suratJalan)->customer)->nama;
                        $quantity = optional($invoiceExternal[0])->jumlah_jual;
                        $satuan = optional($invoiceExternal[0])->satuan_jual;
                        $hargabeli = optional($invoiceExternal[0])->harga_beli;
                        $hargajual = optional($invoiceExternal[0])->harga_jual;                    
                        $ket = optional($invoiceExternal[0])->keterangan;
                        $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray() ?? null;
                        $id_transaksi =Transaction::where('invoice_external', $invoice_external)->where('id_barang', $id_barang)->pluck('id')->first() ?? null;
                    }                    
                    $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                
                $data = Jurnal::find($request->id);
                if ($request->invoice === null && $request->invoice_external === null) {
                    $data->id_transaksi = null;
                } else {
                    $data->id_transaksi = $id_transaksi;
                }
                $data->nomor = $request->nomor;
                $data->debit = $request->debit;
                $data->kredit = $request->kredit;
                $data->keterangan = $keteranganNow;
                $data->keterangan_buku_besar_pembantu = $request->keterangan_buku_besar_pembantu;
                $data->invoice = !empty($request->invoice) ? explode('_', $request->invoice)[0] : null;
                $data->invoice_external = !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null;
                $data->nopol = $request->nopol;
                $data->tipe = $tipe;
                $data->coa_id = $request->coa_id;

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data->nomor, $data->tgl));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else {
            return redirect()->back()->with('error', 'Invoice dan Invoice External kosong');
        }
        
        return redirect()->route('jurnal.edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
{
    // Menghapus jurnal berdasarkan ID yang dikirim
    $data = Jurnal::destroy(request('id'));
    
    // Mendapatkan URL redirect dari session
    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
    
    // Memeriksa apakah redirectUrl ada, jika tidak maka mengarahkan ke jurnal.index
    if (!$redirectUrl) {
        return redirect()->route('jurnal.index')->with('success', 'Data Jurnal berhasil dihapus!');
    }

    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil dihapus!');
}


    public function dataTable()
    {
        $jurnal = Jurnal::join('coa', 'jurnal.coa_id', '=', 'coa.id')->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')->orderBy('tgl', 'desc')->orderBy('nomor', 'desc')->orderBy('tipe', 'asc')->get();

        return DataTables::of($jurnal)
            ->addIndexColumn()
            //            ->addColumn('#', function ($row) {
            //                return '<input type="checkbox" name="id' . $row->id . '" id="id" value="' . $row->id . '">';
            //            })
            //            ->rawColumns(['#'])
            ->make(true);
    }

    public function tglUpdate(Request $request)
    {
        // dd($request->all());
        $tgl = $request->tgl_input;
        $nomor = $request->nomor_jurnal_input;
        // dd($tgl, $nomor);
        $data = Jurnal::where('nomor', $nomor)->update([
            'tgl' => $tgl
        ]);
        $redirectUrl = route('jurnal.edit', ['nomor' => $nomor]);
        return redirect($redirectUrl)->with('success', 'Tanggal Jurnal berhasil diubah!');
    }

    public function store(Request $request, Jurnal $jurnal)
    {
        if ($request->invoice != null) {
            if (str_contains($request->invoice, '_')) {
                $inv = explode('_', $request->invoice)[0];
                $index = explode('_', $request->invoice)[1];
                $invoices = Invoice::with([
                    'transaksi.suppliers',
                    'transaksi.barang',
                    'transaksi.suratJalan.customer',
                ])
                    ->where('invoice', $inv)
                    ->get();
                    $barang = $invoices[$index - 1]->transaksi->barang->nama;
                    $supplier = $invoices[$index - 1]->transaksi->suppliers->nama;
                    $customer = $invoices[$index - 1]->transaksi->suratJalan->customer->nama;
                    $quantity = $invoices[$index - 1]->transaksi->jumlah_jual;
                $satuan = $invoices[$index - 1]->transaksi->satuan_jual;
                $hargabeli = $invoices[$index - 1]->transaksi->harga_beli;
                $hargajual = $invoices[$index - 1]->transaksi->harga_jual;
                $ket = $invoices[$index - 1]->transaksi->keterangan;
                
                // dd($customer, $satuan, $quantity, $hargabeli, $hargajual, $ket, $supplier, $barang);
                $keteranganNow = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $id_transaksi = Invoice::where('invoice', $inv)->where('harga', $hargajual)->pluck('id_transaksi')->first();
                
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                    'id_transaksi' => $id_transaksi ?? $request->id_transaksi,

                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);                

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice = $request->invoice;
                $invoices = Invoice::where('invoice', $invoice)->with([
                    'transaksi.suppliers',
                    'transaksi.barang',
                    'transaksi.suratJalan.customer',
                    ])->get();

                
                $barang = $invoices[0]->transaksi->barang->nama;
                $supplier = $invoices[0]->transaksi->suppliers->nama;
                $customer = $invoices[0]->transaksi->suratJalan->customer->nama;
                $quantity = $invoices[0]->transaksi->jumlah_jual;
                $satuan = $invoices[0]->transaksi->satuan_jual;
                $hargabeli = $invoices[0]->transaksi->harga_beli;
                $hargajual = $invoices[0]->transaksi->harga_jual;
                $ket = $invoices[0]->transaksi->keterangan;
                
                

                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $request->keterangan;
                $id_transaksi = Invoice::where('invoice', $invoice)->where('harga', $hargajual)->pluck('id_transaksi')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                    'id_transaksi' => $id_transaksi ?? $request->id_transaksi,
                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);       
                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else if ($request->invoice_external ) {
            if (str_contains($request->invoice_external, '_')) {
                $invext = explode('_', $request->invoice_external)[0];
                $index = explode('_', $request->invoice_external)[1];
                
                
                $invoice_external = Transaction::where('invoice_external', $invext)
                ->with(['suratJalan.customer', 'barang', 'suppliers'])
                ->get();
                
                
                $barang = $invoice_external[$index - 1]->barang->nama;
                $supplier = $invoice_external[$index - 1]->suppliers->nama;
                $customer = $invoice_external[$index - 1]->suratJalan->customer->nama;
                $quantity = $invoice_external[$index - 1]->jumlah_jual;
                $satuan = $invoice_external[$index - 1]->satuan_jual;
                $hargabeli = $invoice_external[$index - 1]->harga_beli;
                $hargajual = $invoice_external[$index - 1]->harga_jual;
                $ket = $invoice_external[$index - 1]->keterangan;
                $invoice_external= $request->invoice_external ?? '';
                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invext)->where('id_barang', $id_barang)->pluck('id')->first();
                $keteranganNow = $keterangan;
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    $noCounter = explode('-', $nomor)[1]; // Ambil bagian kedua setelah '-'
                    $no = str_replace(' ', '', explode('/', $noCounter)[0]); // Ambil bagian pertama sebelum '/'
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                   'id_transaksi' => $id_transaksi ?? $request->id_transaksi,
                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);       

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice_external = $request->invoice_external;
                $invoiceExternal = Transaction::where('invoice_external', $request->invoice_external)
                ->with(['suratJalan.customer', 'barang', 'suppliers'])
                ->get();
                
                $barang = $invoiceExternal[0]->barang->nama;
                $supplier = $invoiceExternal[0]->suppliers->nama;
                $customer = $invoiceExternal[0]->suratJalan->customer->nama;
                $quantity = $invoiceExternal[0]->jumlah_jual;
                $satuan = $invoiceExternal[0]->satuan_jual;
                $hargabeli = $invoiceExternal[0]->harga_beli;
                $hargajual = $invoiceExternal[0]->harga_jual;
                $ket = $invoiceExternal[0]->keterangan;
                
                // dd($request->invoice_external);
                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invoice_external)->where('id_barang', $id_barang)->pluck('id')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                    'id_transaksi' => $id_transaksi ?? $request->id_transaksi,
                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);         
                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data->nomor, $data->tgl));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else if ($request->keterangan) {
            if (str_contains($request->invoice_external, '_')) {
                $invext = explode('_', $request->invoice_external)[0];
                $index = explode('_', $request->invoice_external)[1];

                $invoice_external = Transaction::where('invoice_external', $request->invoice_external)
                    ->with(['suratJalan.customer', 'barang', 'suppliers'])
                    ->get();

                $barang = $invoice_external[$index - 1]->barang->nama;
                $supplier = $invoice_external[$index - 1]->suppliers->nama;
                $customer = $invoice_external[$index - 1]->suratJalan->customer->nama;
                $quantity = $invoice_external[$index - 1]->jumlah_jual;
                $satuan = $invoice_external[$index - 1]->satuan_jual;
                $hargabeli = $invoice_external[$index - 1]->harga_beli;
                $hargajual = $invoice_external[$index - 1]->harga_jual;
                $ket = $invoice_external[$index - 1]->keterangan;

                $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray();
                $id_transaksi =Transaction::where('invoice_external', $invext)->where('id_barang', $id_barang)->pluck('id')->first();
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                    'id_transaksi' => $id_transaksi ?? $request->id_transaksi,
                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);       

                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            } else {
                $invoice_external = $request->invoice_external;
                $invoiceExternal = Transaction::where('invoice_external', $request->invoice_external)
                    ->with(['suratJalan.customer', 'barang', 'suppliers'])
                    ->get();

                // Mengambil elemen pertama dengan aman
                $invoice = $invoiceExternal->first();

                $barang = optional(optional($invoice)->barang)->nama ?? null;
                $supplier = optional(optional($invoice)->suppliers)->nama ?? null;
                $customer = optional(optional(optional($invoice)->suratJalan)->customer)->nama ?? null;
                $quantity = optional($invoice)->jumlah_jual ?? null;
                $satuan = optional($invoice)->satuan_jual ?? null;
                $hargabeli = optional($invoice)->harga_beli ?? null;
                $hargajual = optional($invoice)->harga_jual ?? null;
                $ket = optional($invoice)->keterangan ?? null;

                    $keterangan = $request->keterangan;

                if (str_contains($request->keterangan, '[1]')) {
                    $keterangan = str_replace('[1]', $customer, $keterangan);
                }
                if (str_contains($request->keterangan, '[2]')) {
                    $keterangan = str_replace('[2]', $supplier, $keterangan);
                }
                if (str_contains($request->keterangan, '[3]')) {
                    $keterangan = str_replace('[3]', $barang, $keterangan);
                }
                if (str_contains($request->keterangan, '[4]')) {
                    $keterangan = str_replace('[4]', $quantity, $keterangan);
                }
                if (str_contains($request->keterangan, '[5]')) {
                    $keterangan = str_replace('[5]', $satuan, $keterangan);
                }
                if (str_contains($request->keterangan, '[6]')) {
                    $keterangan = str_replace('[6]', $hargabeli, $keterangan);
                }
                if (str_contains($request->keterangan, '[7]')) {
                    $keterangan = str_replace('[7]', $hargajual, $keterangan);
                }
                if (str_contains($request->keterangan, '[8]')) {
                    $keterangan = str_replace('[8]', $ket, $keterangan);
                }

                $keteranganNow = $keterangan;
                $id_barang = Barang::where('nama', $barang)->pluck('id')->toArray() ?? null;
                $id_transaksi =Transaction::where('invoice_external', $invoice_external)->where('id_barang', $id_barang)->pluck('id')->first() ?? null;
               
                $nomor = $request->nomor;
                $tipe = $request->tipe;
                $noCounter = explode('-', $nomor)[0];
                $no = str_replace(' ', '', explode('/', $noCounter)[0]);
                if ($tipe == 'JNL') {
                    if ($nomor == 'SALDO AWAL') {
                        $noCounter = 'SALDO AWAL';
                        $no = 0;
                    } else {
                        $noCounter = explode('-', $nomor)[1] ?? 'SALDO AWAL'; // Ambil bagian kedua setelah '-' jika ada
                        $no = str_replace(' ', '', explode('/', $noCounter)[0] ?? 0); // Ambil bagian pertama sebelum '/' jika ada
                    }
                }
                $data = Jurnal::create([
                    'id' => $request->id,
                    'no' => $request->no,
                    'tgl' => $request->tgl,
                    'id_transaksi' => $id_transaksi ?? $request->id_transaksi,
                    'nomor' => $request->nomor,
                    'debit' => $request->debit,
                    'kredit' => $request->kredit,
                    'keterangan' => $keteranganNow,
                    'keterangan_buku_besar_pembantu' => $request->keterangan_buku_besar_pembantu,
                    'invoice' => !empty($request->invoice) ? explode('_', $request->invoice)[0] : null,
                    'invoice_external' => !empty($request->invoice_external) ? explode('_', $request->invoice_external)[0] : null,
                    'nopol' => $request->nopol,
                    'tipe' => $tipe,
                    'coa_id' => $request->coa_id,
                ]);       
                if ($data->save()) {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data->nomor, $data->tgl));
                    return redirect($redirectUrl)->with('success', 'Data Jurnal berhasil diubah!');
                } else {
                    $redirectUrl = session('jurnal_edit_url', route('jurnal.edit', $data));
                    return redirect($redirectUrl)->with('error', 'Data Jurnal Gagal diubah!');
                }
            }
        } else {
            return redirect()->back()->with('error', 'Invoice dan Invoice External kosong');
        }
        
        return redirect()->route('jurnal.edit');
    }
    public function exportJurnal(Request $request){

        if ($request->mulai == null || $request->sampai == null) {
            return back()->with('error', 'Silahkan atur nilai rentang data.');
        }
        return Excel::download(new JurnalALLExport($request->mulai, $request->sampai), 'jurnal.xlsx');
    }
}
