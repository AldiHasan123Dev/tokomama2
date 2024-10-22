<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\NSFP;
use App\Models\Transaction;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ids = explode(',', request('id_transaksi'));
        $transaksi = Transaction::whereIn('id', $ids)->get();
        $count = $transaksi->groupBy('suratJalan.id_customer');
        if($count->count()>1){
            return back()->with('error', 'Invoice hanya bisa dibuat untuk 1 customer');
        }
        $invoice_count = request('invoice_count');
        $nsfp = NSFP::where('available', '1')->orderBy('nomor')->take($invoice_count)->get();
        if($nsfp->count() < $invoice_count) {
            return back()->with('error', 'NSFP Belum Tersedia, pastikan nomor NSFP tersedia.');
        }
        $array_jumlah = [];
        foreach ($transaksi as $item) {
            $array_jumlah[$item->id] = $item->sisa;
        }
        $array_jumlah = json_encode($array_jumlah);
        $invoice_count = request('invoice_count');

        $currentMonth = Carbon::now()->month;
        $noJNL = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $currentMonth)->orderBy('no', 'desc')->first() ?? 0;
        $no_JNL =  $noJNL ? $noJNL->no + 1 : 1;

        
        return view('invoice.index', compact('transaksi','ids','invoice_count','array_jumlah', 'no_JNL'));
    }

    public function preview(Request $request)
    {
        $tgl_inv1 = $request->tgl_invoice;
        $tgl_inv = date('m', strtotime($tgl_inv1));
        $tipe = $tgl_inv . '-' . $request->tipe;
    
        $monthNumber = (int) substr($tgl_inv1, 5, 2);
    
        $data = array();
        $idtsk = array();
        $array_invoice = array();
        $invoice_count = $request->invoice_count;
        $nsfp = NSFP::where('available', '1')->orderBy('nomor')->take($invoice_count)->get();
        
        if ($nsfp->count() < $invoice_count) {
            return back()->with('error', 'NSFP Belum Tersedia, pastikan nomor NSFP tersedia.');
        }
        
        $no = Invoice::whereYear('created_at', date('Y'))->max('no') + 1;
        foreach ($nsfp as $item) {
            $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
            $month_number = $monthNumber;
            $month_roman = $roman_numerals[$month_number];
            $inv = sprintf('%03d', $no) . '/INV/SB-' . $month_roman . '/' . date('Y');
            
            array_push($array_invoice, [
                'id_nsfp' => $item->id,
                'invoice' => $inv,
                'no' => $no
            ]);
            
            $no++;
        }

    
        foreach ($request->invoice as $id_transaksi => $invoice) {
            foreach ($invoice as $idx => $item) {
                $data[$id_transaksi]['invoice'][$idx] = $item;
            }
        }
    
        foreach ($request->jumlah as $id_transaksi => $jumlah) {
            foreach ($jumlah as $idx => $item) {
                $data[$id_transaksi]['jumlah'][$idx] = $item;
    
                // Ambil transaksi berdasarkan id_transaksi
                $trx = Transaction::find($id_transaksi);
    
                // Pastikan transaksi ditemukan
                if ($trx) {
                    // Ambil barang berdasarkan id_barang
                    $barang = Barang::find($trx->id_barang);
                    $satuan = Satuan::find($barang->id_satuan);
                    $suratJalan = SuratJalan::find($trx->id_surat_jalan);
                    
                    $data[$id_transaksi]['satuan_jual'][$idx] = $trx->satuan_jual;
                    $data[$id_transaksi]['harga_jual'][$idx] = $trx->harga_jual;
                    $data[$id_transaksi]['jumlah_jual'][$idx] = $trx->jumlah_jual;
                    $data[$id_transaksi]['keterangan'][$idx] = $trx->keterangan;

                    if ($suratJalan){
                        $data[$id_transaksi]['no_cont'][$idx] = $suratJalan->no_cont;
                    }
    
                    if ($satuan) {
                        $data[$id_transaksi]['nama_satuan'][$idx] = $satuan->nama_satuan;
                    }
                    
                    // Pastikan barang ditemukan dan simpan nama barang
                    if ($barang) {
                        $data[$id_transaksi]['nama_barang'][$idx] = $barang->nama_singkat;
                        $data[$id_transaksi]['status_ppn'][$idx] = $barang->status_ppn;
                        $data[$id_transaksi]['value_ppn'][$idx] = $barang->value_ppn;
                        $data[$id_transaksi]['value'][$idx] = $barang->value; // Menyimpan value PPN
                        // Ambil satuan dari barang dan simpan
                        $data[$id_transaksi]['satuan'][$idx] = $barang->satuan->nama_satuan; // Menyimpan nama satuan
                    } else {
                        $data[$id_transaksi]['nama_barang'][$idx] = 'Barang tidak ditemukan'; // Penanganan jika barang tidak ada
                        $data[$id_transaksi]['satuan'][$idx] = 'Satuan tidak ditemukan'; // Penanganan jika satuan tidak ada
                    }
                } else {
                    $data[$id_transaksi]['nama_barang'][$idx] = 'Transaksi tidak ditemukan'; // Penanganan jika transaksi tidak ada
                    $data[$id_transaksi]['satuan'][$idx] = 'Satuan tidak ditemukan'; // Penanganan jika transaksi tidak ada
                }
            }
        }
    
        // Mengumpulkan id_transaksi ke dalam array idtsk
        foreach ($request->invoice as $id_transaksi => $invoice) {
            array_push($idtsk, $id_transaksi);
        }
    
        // Menggabungkan data NSFP ke dalam data transaksi
        foreach ($data as $id_transaksi => &$array_data) {
            for ($i = 0; $i < count($array_data['invoice']); $i++) {
                if ((int)$array_data['jumlah'][$i] > 0) {
                    $trx = Transaction::find($id_transaksi);
                    if ($trx) {
                        $barang = Barang::find($trx->id_barang);
                        $id_nsfp = $array_invoice[$i]['id_nsfp']; // Ambil id_nsfp sesuai dengan index
                    
                        // Tambahkan id_nsfp, no, dan invoice dari array_invoice ke dalam array_data
                        $array_data['id_nsfp'] = $id_nsfp;
                        $array_data['no'] = $array_invoice[$i]['no'];
                        // Menambahkan invoice
                    
                        // Update nomor NSFP jika diperlukan
                        $nsfp = NSFP::find($id_nsfp);
                        
                        if ($nsfp) {
                            $nomor_nsfp = $nsfp->nomor;
                            if ($barang->status_ppn == 'ya') {
                                $modified = str_replace('080', '010', $nomor_nsfp);
                                // $nsfp->update(['nomor' => $modified]);
                            } else{
                                $modified = $nomor_nsfp;
                            }
                        }
                    }
                    
                }
            }
        }
        $transaksi = Transaction::where('id', $id_transaksi)->first();
      
        // Pass data to view
        return view('invoice.pre-invoice', compact('invoice_count', 'tipe', 'data', 'inv', 'barang', 'satuan', 'tgl_inv1', 'transaksi', 'modified'));
    }
    
    

        // return view('invoice.pre-invoice');

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
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'nsfp' => 'required',
            'invoice' => 'required',
            'tgl_invoice' => 'required|date',
            'tipe' => 'required|string|max:255',
            'invoice_count' => 'required|integer|min:1',
            'data' => 'required|array',
            'data.*.jumlah' => 'required|array',
            'data.*.satuan_jual' => 'required|array',
            'data.*.harga_jual' => 'required|array',
            'data.*.jumlah_jual' => 'required|array',
            'data.*.keterangan' => 'nullable|array',
            'data.*.nama_satuan' => 'nullable|array',
            'data.*.nama_barang' => 'nullable|array',
            'data.*.status_ppn' => 'nullable|array',
            'data.*.value_ppn' => 'nullable|array',
            'data.*.value' => 'nullable|array',
            'data.*.satuan' => 'nullable|array',
            'data.*.id_nsfp' => 'required|string',
            'data.*.no' => 'required|string',
        ]);
        
        // Mengambil data yang divalidasi
        $nsfp = $validatedData['nsfp'];
        $tgl_invoice = $validatedData['tgl_invoice'];
        $tipe = $validatedData['tipe'];
        $invoice = $validatedData['invoice'];
        $invoice_count = $validatedData['invoice_count'];
        $data = $validatedData['data'];
    
        // Loop melalui data untuk menyimpan detail invoice
      
            // Mengambil data dari items
            foreach ($data as $id_transaksi => $items) {
                // Mengambil data dari items
                $jumlah = $items['jumlah'][0]; // Ambil nilai pertama
                $satuan_jual = $items['satuan_jual'][0];
                $harga_jual = $items['harga_jual'][0];
                $keterangan = $items['keterangan'][0] ?? null;
                $id_nsfp = $items['id_nsfp'];
                $no = $items['no'];
    
            // Hitung subtotal
            $subtotal = $jumlah * $harga_jual;
    
            // Simpan Invoice
            $invoiceRecord = Invoice::create([
                'id_transaksi' => $id_transaksi,
                'id_nsfp' => $id_nsfp,
                'invoice' => $invoice,
                'harga' => $harga_jual,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
                'no' => $no,
                'tgl_invoice' => $tgl_invoice,
            ]);
    
            // Update transaksi
            $trx = Transaction::find($id_transaksi); // Pastikan untuk mengambil transaksi
            if ($trx) {
                $trx->update([
                    'sisa' => $trx->sisa - $jumlah,
                ]);
            }
           
            // Update NSFP
            NSFP::find($id_nsfp)->update([
                'available' => 0,
                'invoice' => $invoice,
                'nomor' => $nsfp // Update kolom 'nomor' di tabel nsfp dengan hasil modifikasi
            ]);
            }
    
        // Panggil fungsi untuk mencatat jurnal otomatis (jika diperlukan)
        $this->autoJurnal($request->data, $invoice, $tipe, $tgl_invoice, $nsfp);
    
        return to_route('keuangan.invoice')->with('success', 'Data Invoice berhasil disimpan');
    }
    

    private function autoJurnal($idtsk, $invoice, $tipe, $tgl, $nsfp)
    {
        $bulan = date('m', strtotime($tgl));
        $bulanNow = date('m');
        $tipe1 = $tipe;
        $breakTipe = explode("-", $tipe1);;
        $breakTipe1 = explode("/", $breakTipe[1]);
        $no = $breakTipe1[0];
      

        $sort = Jurnal::whereMonth('tgl', $bulan)->where('tipe', 'JNL')->get();
        $nomorArray = $sort->pluck('no')->toArray();
        if ($nomorArray == []){
            $nomorArray = [0];
        }
        $maxArray = max($nomorArray);

        $break = explode('/', $tipe1);
        $part = $break[0];
        $explode = explode('-', $part);
        $part1 = $explode[1];
        $title1 = $bulan;
        $year = $break[2];
        $newNoJNL = $title1 . '-' . $maxArray + 1 . '/' . $break[1] . '/' . $year;
        $total_all = array();
        $temp_total = array();
        $invoiceArray = explode(',', $invoice); 
        $result = Invoice::with([
            'transaksi.barang.satuan',
            'transaksi.suratJalan.customer'
        ])
        ->where('invoice', $invoice) // Menggunakan string langsung tanpa loop
        ->get();
            $nopol = '';
            $temp_debit = 0;
            
            if ($bulan < $bulanNow) {
                DB::transaction(function () use ($result, $tgl, $newNoJNL, $maxArray, &$nopol, &$temp_debit, $invoice, $nsfp) {
                    $temp_debit = 0; 
                    $nopol = $result[0]->transaksi->suratJalan->no_pol; 

                    if ($result[0]->transaksi->barang->status_ppn == 'ya'){
                        $value_ppn = $result[0]->transaksi->barang->value_ppn/100;
                        $temp_debit = array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn;
                        Jurnal::create([
                            'coa_id' => 8,
                            'nomor' => $newNoJNL,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama,
                            'debit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn + array_sum(array_column($result->toArray(), 'subtotal')) , // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => '',
                            'id_transaksi' => null,
                            'nopol' => $nopol,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $maxArray + 1
                        ]);

                        foreach ($result as $item) {
                            Jurnal::create([
                                'coa_id' => 52, // COA untuk Pendapatan
                                'nomor' => $newNoJNL,
                                'tgl' => $tgl,
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . $item->transaksi->harga_jual . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => $item->subtotal, // Hanya subtotal di kredit
                                'invoice' => $item->invoice,
                                'invoice_external' => '',
                                'id_transaksi' => $item->id_transaksi,
                                'nopol' => $nopol,
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $maxArray + 1
                            ]);
                        }
                            Jurnal::create([
                                'coa_id' => 12, // COA untuk PPN Keluaran
                                'nomor' => $newNoJNL,
                                'tgl' => $tgl,
                                'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $nsfp . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn, // Nilai PPN di kredit
                                'invoice' => $invoice,
                                'invoice_external' => '',
                                'id_transaksi' => null,
                                'nopol' => $nopol,
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $maxArray + 1
                            ]);
                    } else{
                        Jurnal::create([
                            'coa_id' => 8,
                            'nomor' => $newNoJNL,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama,
                            'debit' => array_sum(array_column($result->toArray(), 'subtotal')), // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => '',
                            'id_transaksi' => null,
                            'nopol' => $nopol,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $maxArray + 1
                        ]);
                        foreach ($result as $item) {
                            $temp_debit += $item->subtotal; // Total debit
                            
                            // Mengambil nomor polisi
                            $nopol = $item->transaksi->suratJalan->no_pol;
            
                            // Membuat entri jurnal untuk debit
                            Jurnal::create([
                                'coa_id' => 52,
                                'nomor' => $newNoJNL,
                                'tgl' => $tgl,
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . $item->transaksi->harga_jual . ')',
                                'debit' => 0, // Menyimpan subtotal sebagai debit
                                'kredit' => $item->subtotal, // Kredit diisi 0
                                'invoice' => $item->invoice,
                                'invoice_external' => '',
                                'id_transaksi' => $item->id_transaksi,
                                'nopol' => $nopol,
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $maxArray + 1
                            ]);
                    }
                    }
                });
            } else {
                // Menginisialisasi variabel sebelum loop
                $temp_debit = 0; 
                $nopol = $result[0]->transaksi->suratJalan->no_pol; // Asumsikan nopol sama untuk semua item
                if ($result[0]->transaksi->barang->status_ppn == 'ya'){
                    $value_ppn = $result[0]->transaksi->barang->value_ppn/100;
                    $temp_debit = array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn;
                    Jurnal::create([
                        'coa_id' => 8,
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama,
                        'debit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn + array_sum(array_column($result->toArray(), 'subtotal')) , // Debit diisi 0
                        'kredit' => 0, // Menggunakan total debit sebagai kredit
                        'invoice' => $invoice,
                        'invoice_external' => '',
                        'id_transaksi' => null,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $maxArray + 1
                    ]);
                    foreach ($result as $item) {
                        $temp_debit += $item->subtotal; // Total debit
                        
                        // Mengambil nomor polisi
                        $nopol = $item->transaksi->suratJalan->no_pol;
                    Jurnal::create([
                        'coa_id' => 52, // COA untuk Pendapatan
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . $item->transaksi->harga_jual . ')',
                        'debit' => 0, // Debit diisi 0
                        'kredit' => $item->subtotal, // Hanya subtotal di kredit
                        'invoice' => $item->invoice,
                        'invoice_external' => '',
                        'id_transaksi' => $item->id_transaksi,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                    ]);
                }
                    Jurnal::create([
                        'coa_id' => 12, // COA untuk PPN Keluaran
                        'nomor' => $newNoJNL,
                        'tgl' => $tgl,
                        'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $nsfp . ')',
                        'debit' => 0, // Debit diisi 0
                        'kredit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn, // Nilai PPN di kredit
                        'invoice' => $invoice,
                        'invoice_external' => '',
                        'id_transaksi' => null,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                        ]);
                } else {
                    Jurnal::create([
                        'coa_id' => 8,
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama,
                        'debit' => array_sum(array_column($result->toArray(), 'subtotal')), // Debit diisi 0
                        'kredit' => 0, // Menggunakan total debit sebagai kredit
                        'invoice' => $invoice,
                        'invoice_external' => '',
                        'id_transaksi' => null,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                    ]);
    
    // Kemudian lakukan loop untuk entri debit
                    foreach ($result as $item) {
                        $temp_debit += $item->subtotal; // Total debit
                        $nopol = $item->transaksi->suratJalan->no_pol;
    
                        // Membuat entri jurnal untuk debit
                        Jurnal::create([
                            'coa_id' => 52,
                            'nomor' => $tipe1,
                            'tgl' => $tgl,
                            'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . $item->transaksi->harga_jual . ')',
                            'debit' => 0, // Menyimpan subtotal sebagai debit
                            'kredit' => $item->subtotal, // Kredit diisi 0
                            'invoice' => $item->invoice,
                            'invoice_external' => '',
                            'id_transaksi' => $item->id_transaksi,
                            'nopol' => $nopol,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no
                        ]);
                }
                // Membuat entri jurnal untuk kredit (piutang) terlebih dahulu
                }
            }
}

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
