<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\NSFP;
use App\Models\Transaction;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $array_jumlah[$item->id] = $item->jumlah_jual;
        }
        $array_jumlah = json_encode($array_jumlah);
        $invoice_count = request('invoice_count');

        $currentMonth = Carbon::now()->month;
        $noJNL = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $currentMonth)->orderBy('no', 'desc')->first() ?? 0;
        $no_JNL =  $noJNL ? $noJNL->no + 1 : 1;

        // dd($no_JNL);
        
        return view('invoice.index', compact('transaksi','ids','invoice_count','array_jumlah', 'no_JNL'));
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
        // dd($request->all());
        // dd(date("n"));
        
        $tgl_inv1 = $request->tgl_invoice;
        $tgl_inv = date('m', strtotime($tgl_inv1));
        $tipe = $tgl_inv. '-' . $request->tipe;
        $monthNumber = (int) substr($tgl_inv1, 5, 2);
        // dd($monthNumber);
        $data = array();
        $idtsk = array();
        $array_invoice = array();
        $invoice_count = $request->invoice_count;
        $nsfp = NSFP::where('available', '1')->orderBy('nomor')->take($invoice_count)->get();
        if($nsfp->count() < $invoice_count) {
            return back()->with('error', 'NSFP Belum Tersedia, pastikan nomor NSFP tersedia.');
        }

        $no = Invoice::whereYear('created_at', date('Y'))->max('no') + 1;
        foreach ($nsfp as $item) {
            $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
            $month_number = $monthNumber;
            $month_roman = $roman_numerals[$month_number];
            $inv= sprintf('%03d', $no) . '/INV/SB-' . $month_roman . '/' . date('Y');
            array_push($array_invoice, [
                'id_nsfp' => $item->id,
                'invoice' => $inv,
                'no' => $no
            ]);
            $no++;
        }


        foreach ($request->invoice as $id_transaksi => $invoice) {
            // dd($id_transaksi);
            foreach ($invoice as $idx => $item) {
                $data[$id_transaksi]['invoice'][$idx] = $item;
            }
        }
        foreach ($request->jumlah as $id_transaksi => $jumlah) {
            foreach ($jumlah as $idx => $item) {
                $data[$id_transaksi]['jumlah'][$idx] = $item;
            }
        }
        foreach ($request->invoice as $id_transaksi => $invoice) {
            array_push($idtsk, $id_transaksi);
        }
        
        
        // dd($idtsk);
        // dd($array_invoice);
        DB::transaction(function () use($data, $array_invoice, $request, $idtsk) {
            foreach ($data as $id_transaksi => $array_data) {
                // dd($request->tgl_invoice);
                for ($i=0; $i < count($array_data['invoice']); $i++) {
                    if ((int)$array_data['jumlah'][$i] > 0) {
                        $trx = Transaction::find($id_transaksi);
                        $barang = Barang::find($trx->id_barang);
                        $id_nsfp = $array_invoice[(int)$array_data['invoice'][$i]]['id_nsfp'];

                        // Ambil data NSFP berdasarkan id_nsfp
                        $nsfp = NSFP::find($id_nsfp); // atau bisa menggunakan NSFP::where('id', $id_nsfp)->first();

                        // Ambil nilai kolom 'nomor' dari data yang ditemukan
                        $nomor_nsfp = $nsfp->nomor;
                        if ($barang->status_ppn == 'ya') {
                            // Jika status_ppn adalah 'ya', ganti '080' menjadi '010'
                            $modified = str_replace('080', '010', $nomor_nsfp);
                            $nsfp->update([
                                'nomor' => $modified // Update kolom 'nomor' di tabel nsfp dengan hasil modifikasi
                            ]);
                        } else {
                            // Jika tidak, tidak melakukan perubahan
                            $modified = $nomor_nsfp; // Tetap sama seperti nomor_nsfp
                        }

                        Invoice::create([
                            'id_transaksi' => $id_transaksi,
                            'id_nsfp' => $id_nsfp,
                            'invoice' => $array_invoice[(int)$array_data['invoice'][$i]]['invoice'],
                            'harga' => $trx->harga_jual,
                            'jumlah' => $array_data['jumlah'][$i],
                            'subtotal' => $array_data['jumlah'][$i] * $trx->harga_jual,
                            'no' => $array_invoice[(int)$array_data['invoice'][$i]]['no'],
                            'tgl_invoice' => $request->tgl_invoice,
                        ]);
                        $trx->update([
                            'sisa' => $trx->sisa - $array_data['jumlah'][$i]
                        ]);
                        NSFP::find($id_nsfp)->update([
                            'available' => 0,
                            'invoice' => $array_invoice[(int)$array_data['invoice'][$i]]['invoice'],
                        ]);
                    }
                }
            }
            $this->autoJurnal($idtsk, $array_invoice, $request->tipe, $request->tgl_invoice, $modified);

        });;

        return to_route('keuangan.invoice')->with('success', 'Invoice Created Successfully');
    }

    private function autoJurnal($idtsk, $invoice, $tipe, $tgl, $modified)
    {
        $bulan = date('m', strtotime($tgl));
        $bulanNow = date('m');
        $tipe1 = $bulan . '-' . $tipe;
        $breakTipe = explode('/', $tipe)[0];
        $breakTipe1 = explode('-', $tipe)[0];
        $no = $breakTipe1;

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

        for ($i = 0; $i < count($invoice); $i++) {
            $result = Invoice::with([
                'transaksi.barang.satuan',
                'transaksi.suratJalan.customer'
            ])
            ->where('invoice', $invoice[$i]['invoice'])->get();
        
            $nopol = '';
            $temp_debit = 0;
            
            if ($bulan < $bulanNow) {
                DB::transaction(function () use ($result, $tgl, $newNoJNL, $maxArray, &$nopol, &$temp_debit, $invoice, $i, $modified) {
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
                            'invoice' => $invoice[$i]['invoice'],
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
                                'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $modified . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn, // Nilai PPN di kredit
                                'invoice' => $invoice[$i]['invoice'],
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
                            'invoice' => $invoice[$i]['invoice'],
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
                        'invoice' => $invoice[$i]['invoice'],
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
                        'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $modified . ')',
                        'debit' => 0, // Debit diisi 0
                        'kredit' => array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn, // Nilai PPN di kredit
                        'invoice' => $invoice[$i]['invoice'],
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
                        'invoice' => $invoice[$i]['invoice'],
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
