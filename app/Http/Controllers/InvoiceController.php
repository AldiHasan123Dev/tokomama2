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
       if ($request->isMethod('get')) {
    return redirect()
        ->route('keuangan.pre-invoice')
        ->with('error',);
}

        foreach ($request->harga_jual as $id_transaksi => $harga_jual) {
            foreach ($harga_jual as $idx => $item) {
                $data[$id_transaksi]['harga_jual'][$idx] = $item; 
                $item = (int) str_replace(',', '', $item);
                if ($item == 0){
                    return back()->with('error', 'Silahkan input harga jual');
                }
                $trx1 = Transaction::find($id_transaksi);
                $barangs = Barang::find($trx1->id_barang);
                if($barangs->status_ppn == 'ya'){
                    $item =round($item / 1.11 ,4);
                }
                $margin = $trx1->harga_beli - $item;
                $trx1->update([
                    'harga_jual' => $item,
                    'margin' => $margin
                ]);
            }
        }
        $tgl_inv2 = $request->tgl_invoice; 
        $date = date_create($tgl_inv2);
        $tgl_inv1 = date_format($date, 'd F Y'); 
       
        $tgl_inv = date('m', strtotime($tgl_inv2));
        $tipe = $tgl_inv . '-' . $request->tipe;
    
        $monthNumber = (int) substr($tgl_inv2, 5, 2);
    
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
            $inv = sprintf('%03d', $no) . '/INV/TM-' . $month_roman . '/' . date('Y');
            
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
                        $data[$id_transaksi]['no_po'][$idx] = $suratJalan->no_po;
                        $data[$id_transaksi]['tgl_sj'][$idx] = $suratJalan->tgl_sj;
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
        $modified = null;
        
        // Menggabungkan data NSFP ke dalam data transaksi
        foreach ($data as $id_transaksi => &$array_data) {
            for ($i = 0; $i < count($array_data['invoice']); $i++) {
                if ((int)$array_data['jumlah'][$i] > 0 || $array_data['jumlah'][$i]) {
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
                         $modified = null;
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
        return view('invoice.pre-invoice', compact('invoice_count', 'tipe', 'data', 'inv', 'barang', 'satuan', 'tgl_inv2', 'tgl_inv1', 'transaksi', 'modified'));
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
        $this->autoJurnal($request->data, $invoice, $tipe, $tgl_invoice, $nsfp,$id_transaksi);
    
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
        $jurhutNow = $bulanNow . '-' . $no + 1 . '/' . $breakTipe1[1] . '/' . $breakTipe1[2];
       
      

        $sort = Jurnal::whereMonth('tgl', $bulan)->where('tipe', 'JNL')->get();
        $nomorArray = $sort->pluck('no')->toArray();
        if ($nomorArray == []){
            $nomorArray = [0];
        }
        $maxArray = end($nomorArray);

        $break = explode('/', $tipe1);
        $part = $break[0];
        $explode = explode('-', $part);
        $part1 = $explode[1];
        $title1 = $bulan;
        $year = $break[2];
        $newNoJNL = $title1 . '-' . $maxArray + 1 . '/' . $break[1] . '/' . $year;
        $jurhut = $title1 . '-' . $maxArray + 2 . '/' . $break[1] . '/' . $year;
        $total_all = array();
        $temp_total = array();
        $invoiceArray = explode(',', $invoice); 
        $result = Invoice::with([
            'transaksi.barang.satuan',
            'transaksi.suratJalan.customer'
        ])
        ->where('invoice', $invoiceArray) // Menggunakan string langsung tanpa loop
        ->get();
        
        $id_transaksi = $result->pluck('id_transaksi');
        $id_invx = $result->pluck('transaksi.invoice_external');
        $cekCoa = Jurnal::whereIn('invoice_external',$id_invx)
        ->where('coa_id', 30)->get();
        $invoice_external1 = Transaction::whereIn('id',$id_transaksi)->get();
        $invoice_external = $invoice_external1->pluck('invoice_external')->first();
        // dd($invoice_external);
       
        // if ($cekCoa->isNotEmpty()) {
        //     // Jika ada nilai, dump and die
        //     dd('INI HUTANG : ', $cekCoa);
        // } else{
        //     dd('INI persediaan stok : ', $cekCoa);
        // }
            $nopol = '';
            $temp_debit = 0;
            
            if ($bulan < $bulanNow) {
                    $temp_debit = 0; 
                    $nopol = $result[0]->transaksi->suratJalan->no_pol; 
                    $subtotal = $result->sum(function ($item) {
                        return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                    });

                    if ($result[0]->transaksi->barang->status_ppn == 'ya'){
                        $value_ppn = 11/100;
                        $subtotalPPN = $result->sum(function ($item) {
                            $value_ppn = 11 / 100;
                            return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual * $value_ppn);
                        });
                        $temp_debit = round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn, 4);
                        Jurnal::create([
                            'coa_id' => 8,
                            'nomor' => $newNoJNL,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                            'debit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn + array_sum(array_column($result->toArray(), 'subtotal'))) , // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => null,
                            'id_transaksi' => $result[0]->id_transaksi,
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
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => round($item->subtotal), // Hanya subtotal di kredit
                                'invoice' => $item->invoice,
                                'invoice_external' => null,
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
                                'kredit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn), // Nilai PPN di kredit
                                'invoice' => $invoice,
                                'invoice_external' => null,
                                'id_transaksi' => $result[0]->id_transaksi,
                                'nopol' => $nopol,
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $maxArray + 1
                            ]);
                           
                             //Jurnal Hutang PPN 
                             
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                                               
                        ///batas
                            //Jurnal Hutang PPN 
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                             
                             foreach ($result as $item) {
                                Jurnal::create([
                                    'coa_id' => 63,
                                    'nomor' => $jurhut,
                                    'tgl' => $tgl,
                                    'keterangan' => 'HPP ' . $item->transaksi->barang->nama . 
                                ' (' . $item->transaksi->jumlah_jual . ' ' . 
                                $item->transaksi->satuan_jual . ' Harsat ' . 
                                number_format($item->transaksi->harga_beli, 2, ',', '.') . ') ' . 
                                ' untuk ' . $item->transaksi->suratJalan->customer->nama,
                                    'debit' => round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual), // Debit diisi 0
                                    'kredit' => 0, // Menggunakan total debit sebagai kredit
                                    'invoice' => null,
                                    'invoice_external' => $item->transaksi->invoice_external,
                                    'id_transaksi' => $item->id_transaksi,
                                    'nopol' => $nopol,
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $maxArray + 2
                                ]);

                                 //coa 1.1.4 = PPN Masukan (Debit)
                            }
                             //coa 1.6 = Persediaan Barang / Stock (Kredit)
                             $supplierJournals = [];

                             foreach ($result as $item) {
                                 // Pastikan transaksi tersedia
                                 if (!$item->transaksi || !$item->transaksi->suppliers) {
                                     continue; // Lewati jika transaksi atau supplier tidak ada
                                 }
                             
                                 // Ambil semua supplier dalam bentuk array/koleksi
                                 $suppliers = is_iterable($item->transaksi->suppliers) ? $item->transaksi->suppliers : [$item->transaksi->suppliers];
                             
                                 foreach ($suppliers as $supplier) {
                                     $supplierId = $supplier->id;
                                     $supplierName = $supplier->nama;
                                     $inv_x = $item->transaksi->invoice_external;
                                     $kredit = round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                             
                                     // Jika supplier sudah ada, tambahkan jumlah kreditnya
                                     if (isset($supplierJournals[$inv_x])) {
                                         $supplierJournals[$inv_x]['kredit'] += $kredit;
                                     } else {
                                         // Simpan data jurnal sementara
                                         $supplierJournals[$inv_x] = [
                                             'coa_id' => 89,
                                             'nomor' => $jurhut,
                                             'tgl' => $tgl,
                                             'keterangan' => 'Persediaan Jayapura ' . $supplierName,
                                             'debit' => 0,
                                             'kredit' => $kredit,
                                             'invoice' => null,
                                             'invoice_external' => $inv_x,
                                             'id_transaksi' => $item->id_transaksi,
                                             'nopol' => $nopol,
                                             'container' => null,
                                             'tipe' => 'JNL',
                                             'no' => $maxArray + 2
                                         ];
                                     }
                                 }
                             }
                             
                             // Simpan jurnal setelah semua data dikumpulkan
                             foreach ($supplierJournals as $jurnalData) {
                                 Jurnal::create($jurnalData);
                             }                                                                           
                        
                            //End PPN Bulan $bulan < $bulanNow
                    } else{
                        $subtotal = $result->sum(function ($item) {
                            return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                        });
                        Jurnal::create([
                            'coa_id' => 8,
                            'nomor' => $newNoJNL,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                            'debit' => round(array_sum(array_column($result->toArray(), 'subtotal'))), // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => null,
                            'id_transaksi' => $result[0]->id_transaksi,
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
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                                'debit' => 0, // Menyimpan subtotal sebagai debit
                                'kredit' => round($item->subtotal), // Kredit diisi 0
                                'invoice' => $item->invoice,
                                'invoice_external' => null,
                                'id_transaksi' => $item->id_transaksi,
                                'nopol' => $nopol,
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $maxArray + 1
                            ]);
                    }
                 
                    //Jurnal Hutang No PPN
                    
                    // Simpan jurnal setelah semua data dikumpulkan
                    foreach ($supplierJournals as $jurnalData) {
                        Jurnal::create($jurnalData);
                    }                    
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                             foreach ($result as $item) {
                                Jurnal::create([
                                    'coa_id' => 63,
                                    'nomor' => $jurhut,
                                    'tgl' => $tgl,
                                    'keterangan' => 'HPP ' . $item->transaksi->barang->nama . 
                                ' (' . $item->transaksi->jumlah_jual . ' ' . 
                                $item->transaksi->satuan_jual . ' Harsat ' . 
                                number_format($item->transaksi->harga_beli, 2, ',', '.') . ') ' . 
                                ' untuk ' . $item->transaksi->suratJalan->customer->nama,
                                    'debit' => round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual), // Debit diisi 0
                                    'kredit' => 0, // Menggunakan total debit sebagai kredit
                                    'invoice' => null,
                                    'invoice_external' => $item->transaksi->invoice_external,
                                    'id_transaksi' => $item->id_transaksi,
                                    'nopol' => $nopol,
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $maxArray + 2
                                ]);
                            }
                             //coa 1.6 = Persediaan Barang / Stock (Kredit)
                             
                      
                            //Jurnal Hutang No PPN 
                     //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                     //coa 1.6 = Persediaan Barang / Stock (Kredit)
                     $supplierJournals = [];

                     foreach ($result as $item) {
                         // Pastikan transaksi tersedia
                         if (!$item->transaksi || !$item->transaksi->suppliers) {
                             continue; // Lewati jika transaksi atau supplier tidak ada
                         }
                     
                         // Ambil semua supplier dalam bentuk array/koleksi
                         $suppliers = is_iterable($item->transaksi->suppliers) ? $item->transaksi->suppliers : [$item->transaksi->suppliers];
                     
                         foreach ($suppliers as $supplier) {
                             $supplierId = $supplier->id;
                             $supplierName = $supplier->nama;
                             $inv_x = $item->transaksi->invoice_external;
                             $kredit = round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                     
                             // Jika supplier sudah ada, tambahkan jumlah kreditnya
                             if (isset($supplierJournals[$inv_x])) {
                                 $supplierJournals[$inv_x]['kredit'] += $kredit;
                             } else {
                                 // Simpan data jurnal sementara
                                 $supplierJournals[$inv_x] = [
                                     'coa_id' => 89,
                                     'nomor' => $jurhut,
                                     'tgl' => $tgl,
                                     'keterangan' => 'Persediaan Jayapura ' . $supplierName,
                                     'debit' => 0,
                                     'kredit' => $kredit,
                                     'invoice' => null,
                                     'invoice_external' => $inv_x,
                                     'id_transaksi' => $item->id_transaksi,
                                     'nopol' => $nopol,
                                     'container' => null,
                                     'tipe' => 'JNL',
                                     'no' => $maxArray + 2
                                 ];
                             }
                         }
                     }
                     
                     // Simpan jurnal setelah semua data dikumpulkan
                     foreach ($supplierJournals as $jurnalData) {
                         Jurnal::create($jurnalData);
                     }                                         
                        
                            //End Jurnal Hutang NO PPN
                    }
            } else {
                $subtotal = $result->sum(function ($item) {
                    return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                });
                $subtotalPPN = $result->sum(function ($item) {
                    $value_ppn = 11 / 100;
                    return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual * $value_ppn);
                });
                // Menginisialisasi variabel sebelum loop
                $temp_debit = 0; 
                $nopol = $result[0]->transaksi->suratJalan->no_pol; // Asumsikan nopol sama untuk semua item
                if ($result[0]->transaksi->barang->status_ppn == 'ya'){
                    $value_ppn = 11 /100;
                    $temp_debit = round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn);
                    Jurnal::create([
                        'coa_id' => 8,
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                        'debit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn + array_sum(array_column($result->toArray(), 'subtotal'))) , // Debit diisi 0
                        'kredit' => 0, // Menggunakan total debit sebagai kredit
                        'invoice' => $invoice,
                        'invoice_external' => null,
                        'id_transaksi' => $result[0]->id_transaksi,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                    ]);
                    foreach ($result as $item) {
                        $temp_debit += round($item->subtotal); // Total debit
                        
                        // Mengambil nomor polisi
                        $nopol = $item->transaksi->suratJalan->no_pol;
                    Jurnal::create([
                        'coa_id' => 52, // COA untuk Pendapatan
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                        'debit' => 0, // Debit diisi 0
                        'kredit' => round($item->subtotal), // Hanya subtotal di kredit
                        'invoice' => $item->invoice,
                        'invoice_external' => null,
                        'id_transaksi' => $item->id_transaksi,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                    ]);
                }
                    Jurnal::create([
                        'coa_id' => 12, // COA untuk PPN Keluaran
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $nsfp . ')',
                        'debit' => 0, // Debit diisi 0
                        'kredit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn), // Nilai PPN di kredit
                        'invoice' => $invoice,
                        'invoice_external' => null,
                        'id_transaksi' => $result[0]->id_transaksi,
                        'nopol' => $nopol,
                        'container' => null,
                        'tipe' => 'JNL',
                        'no' => $no
                        ]);
                        
                             
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                             foreach ($result as $item) {
                                Jurnal::create([
                                    'coa_id' => 63,
                                    'nomor' => $jurhutNow,
                                    'tgl' => $tgl,
                                    'keterangan' => 'HPP ' . $item->transaksi->barang->nama . 
                                ' (' . $item->transaksi->jumlah_jual . ' ' . 
                                $item->transaksi->satuan_jual . ' Harsat ' . 
                                number_format($item->transaksi->harga_beli, 2, ',', '.') . ') ' . 
                                ' untuk ' . $item->transaksi->suratJalan->customer->nama,
                                    'debit' => round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual), // Debit diisi 0
                                    'kredit' => 0, // Menggunakan total debit sebagai kredit
                                    'invoice' => null,
                                    'invoice_external' => $item->transaksi->invoice_external,
                                    'id_transaksi' => $item->id_transaksi,
                                    'nopol' => $nopol,
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $no + 1
                                ]);

                                 //coa 1.1.4 = PPN Masukan (Debit)
                            }
                            
                             //coa 1.6 = Persediaan Barang / Stock (Kredit)
                             $supplierJournals = [];

                             foreach ($result as $item) {
                                 // Pastikan transaksi tersedia
                                 if (!$item->transaksi || !$item->transaksi->suppliers) {
                                     continue; // Lewati jika transaksi atau supplier tidak ada
                                 }
                             
                                 // Ambil semua supplier dalam bentuk array/koleksi
                                 $suppliers = is_iterable($item->transaksi->suppliers) ? $item->transaksi->suppliers : [$item->transaksi->suppliers];
                             
                                 foreach ($suppliers as $supplier) {
                                     $supplierId = $supplier->id;
                                     $supplierName = $supplier->nama;
                                     $inv_x = $item->transaksi->invoice_external;
                                     $kredit = round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                             
                                     // Jika supplier sudah ada, tambahkan jumlah kreditnya
                                     if (isset($supplierJournals[$inv_x])) {
                                         $supplierJournals[$inv_x]['kredit'] += $kredit;
                                     } else {
                                         // Simpan data jurnal sementara
                                         $supplierJournals[$inv_x] = [
                                             'coa_id' => 89,
                                             'nomor' => $jurhutNow,
                                             'tgl' => $tgl,
                                             'keterangan' => 'Persediaan Jayapura ' . $supplierName,
                                             'debit' => 0,
                                             'kredit' => $kredit,
                                             'invoice' => null,
                                             'invoice_external' => $inv_x,
                                             'id_transaksi' => $item->id_transaksi,
                                             'nopol' => $nopol,
                                             'container' => null,
                                             'tipe' => 'JNL',
                                             'no' => $no + 1
                                         ];
                                     }
                                 }
                             }
                             
                             // Simpan jurnal setelah semua data dikumpulkan
                             foreach ($supplierJournals as $jurnalData) {
                                 Jurnal::create($jurnalData);
                             }                                                         
                       
                            //Jurnal Hutang PPN
                         //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                                                  
                        

                            //End PPN Bulan $bulan < $bulanNow
                } else {
                    $subtotal = $result->sum(function ($item) {
                        return round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                    });
                    Jurnal::create([
                        'coa_id' => 8,
                        'nomor' => $tipe1,
                        'tgl' => $tgl,
                        'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                        'debit' => round(array_sum(array_column($result->toArray(), 'subtotal'))), // Debit diisi 0
                        'kredit' => 0, // Menggunakan total debit sebagai kredit
                        'invoice' => $invoice,
                        'invoice_external' => null,
                        'id_transaksi' => $result[0]->id_transaksi,
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
                            'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                            'debit' => 0, // Menyimpan subtotal sebagai debit
                            'kredit' => round($item->subtotal), // Kredit diisi 0
                            'invoice' => $item->invoice,
                            'invoice_external' => null,
                            'id_transaksi' => $item->id_transaksi,
                            'nopol' => $nopol,
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no
                        ]);
                }
               
                //Jurnal Hutang No PPN 
                             
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                             foreach ($result as $item) {
                                Jurnal::create([
                                    'coa_id' => 63,
                                    'nomor' => $jurhutNow,
                                    'tgl' => $tgl,
                                    'keterangan' => 'HPP ' . $item->transaksi->barang->nama . 
                                ' (' . $item->transaksi->jumlah_jual . ' ' . 
                                $item->transaksi->satuan_jual . ' Harsat ' . 
                                number_format($item->transaksi->harga_beli, 2, ',', '.') . ') ' . 
                                ' untuk ' . $item->transaksi->suratJalan->customer->nama,
                                    'debit' => round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual), // Debit diisi 0
                                    'kredit' => 0, // Menggunakan total debit sebagai kredit
                                    'invoice' => null,
                                    'invoice_external' => $item->transaksi->invoice_external,
                                    'id_transaksi' => $item->id_transaksi,
                                    'nopol' => $nopol,
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $no + 1
                                ]);
                            }
                             //coa 1.6 = Persediaan Barang / Stock (Kredit)
                             $supplierJournals = [];

                             foreach ($result as $item) {
                                 // Pastikan transaksi tersedia
                                 if (!$item->transaksi || !$item->transaksi->suppliers) {
                                     continue; // Lewati jika transaksi atau supplier tidak ada
                                 }
                             
                                 // Ambil semua supplier dalam bentuk array/koleksi
                                 $suppliers = is_iterable($item->transaksi->suppliers) ? $item->transaksi->suppliers : [$item->transaksi->suppliers];
                             
                                 foreach ($suppliers as $supplier) {
                                     $supplierId = $supplier->id;
                                     $supplierName = $supplier->nama;
                                     $inv_x = $item->transaksi->invoice_external;
                                     $kredit = round($item->transaksi->harga_beli * $item->transaksi->jumlah_jual);
                             
                                     // Jika supplier sudah ada, tambahkan jumlah kreditnya
                                     if (isset($supplierJournals[$inv_x])) {
                                         $supplierJournals[$inv_x]['kredit'] += $kredit;
                                     } else {
                                         // Simpan data jurnal sementara
                                         $supplierJournals[$inv_x] = [
                                             'coa_id' => 89,
                                             'nomor' => $jurhutNow,
                                             'tgl' => $tgl,
                                             'keterangan' => 'Persediaan Jayapura ' . $supplierName,
                                             'debit' => 0,
                                             'kredit' => $kredit,
                                             'invoice' => null,
                                             'invoice_external' => $inv_x,
                                             'id_transaksi' => $item->id_transaksi,
                                             'nopol' => $nopol,
                                             'container' => null,
                                             'tipe' => 'JNL',
                                             'no' => $no + 1
                                         ];
                                     }
                                 }
                             }
                             
                             // Simpan jurnal setelah semua data dikumpulkan
                             foreach ($supplierJournals as $jurnalData) {
                                 Jurnal::create($jurnalData);
                             }                                                           
                     
                 //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
               
                            //End Jurnal Hutang NO PPN
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