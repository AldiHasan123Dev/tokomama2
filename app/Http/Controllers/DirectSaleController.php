<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaction;
use App\Models\Satuan;
use App\Models\Harga;
use App\Models\NSFP;
use App\Models\Jurnal;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\SuratJalan;
use Carbon\Carbon;

class DirectSaleController extends Controller
{
    public function ds_ppn()
    {
        $harga = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
    ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
    ->leftJoin('harga', function($join) {
        $join->on('barang.id', '=', 'harga.id_barang')
             ->where('harga.is_status', 1);
    })
    ->select(
        'barang.nama',
        'barang.kode_objek',
        'satuan.nama_satuan',
        'transaksi.*',
        'harga.harga as harga_barang' // Alias supaya tidak bentrok dengan transaksi.harga_jual / beli
    )
    ->where('barang.status', 'AKTIF')
    ->where('barang.status_ppn', 'ya')
    ->whereNull('id_surat_jalan')
    ->where('harga_jual', 0)
    ->where('harga_beli', '>', 0)
    ->where('sisa', '>', 0)
    ->whereNotNull('transaksi.stts')
    ->where('harga.harga','>',0)
    ->get();

        $satuan = Satuan::all();
        return view('direct_sale.ds-ppn', compact('harga','satuan'));
    }

    public function ds_nonppn()
    {
        $harga = Transaction::join('barang', 'transaksi.id_barang', '=', 'barang.id')
    ->join('satuan', 'barang.id_satuan', '=', 'satuan.id')
    ->leftJoin('harga', function($join) {
        $join->on('barang.id', '=', 'harga.id_barang')
             ->where('harga.is_status', 1);
    })
    ->select(
        'barang.nama',
        'barang.kode_objek',
        'satuan.nama_satuan',
        'transaksi.*',
        'harga.harga as harga_barang' // Alias supaya tidak bentrok dengan transaksi.harga_jual / beli
    )
    ->where('barang.status', 'AKTIF')
    ->where('barang.status_ppn', 'tidak')
    ->whereNull('id_surat_jalan')
    ->where('harga_jual', 0)
    ->where('harga_beli', '>', 0)
    ->where('sisa', '>', 0)
    ->whereNotNull('transaksi.stts')
    ->where('harga.harga','>',0)
    ->get();

        $satuan = Satuan::all();
        return view('direct_sale.ds-non-ppn', compact('harga','satuan'));
    }

     public function getHarga(Request $request)
    {
        $id = $request->id;
        $data = Transaction::with('barang')->find($id);
        $barang = $data->id_barang;
        $harga = Harga::where('id_barang', $barang)->where('is_status', 1)->first();

        if (!$data) {
            return response()->json(['harga' => null], 404);
        }
        
        // Hitung harga final berdasarkan status_ppn
        $hargaAsli = $harga->harga;
        $hargaFinal = $harga->barang->status_ppn === 'ya' ? $hargaAsli * 1.11 : $hargaAsli;

        return response()->json([
            'harga' => number_format($hargaFinal, 0, ',', '.') // Kirim harga dengan format rupiah
        ]);
    }

    public function ppn_store(Request $request)
    {
        $id_transaksi = $request->barang;
        $id_transaksi = array_filter($request->barang, function ($value) {
        return !is_null($value);
        });
        $jumlahBarang = count(array_filter($id_transaksi, function ($item) {
            return !is_null($item);
        }));

        $harga_jual = array_filter($request->harga, function ($value) {
        return !is_null($value);
        });
        $harga_jual = array_values($harga_jual); // untuk mereset indeks
        $harga_jual = array_map(function ($value) {
            return $value !== null ? (float) str_replace('.', '', $value) : null;
        }, $harga_jual);

        $jumlah_jual = array_filter($request->jumlah_jual, function ($value) {
        return !is_null($value);
        });
        $jumlah_jual = array_map(function ($value) {
            return $value !== null ? (int) str_replace('.', '', $value) : null;
        }, $jumlah_jual);

        $tgl = date('Y-m-d');
        //invoice
        $data = array();
        $idtsk = array();
        $array_invoice = array();
        $invoice = array();
        $date = date_create($tgl);
        $tgl_inv = date_format($date, 'd F Y'); 
        $tgl_m = date('m', strtotime($tgl));
        $tgl_y = date('y', strtotime($tgl));

        $customer = Customer::where('id', 126)->first();
        $no = SuratJalan::whereYear('created_at', date('Y'))->max('no') + 1;
        $nama_cust = $customer->nama;
        $roman_numerals = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        $month_number = date("n", strtotime($tgl));
        $month_roman = $roman_numerals[$month_number];

        $surat_jalan = [
            'nama_cust'     => $customer->nama,
            'nopol'     => '-',
            'id_customer'   => $customer->id,
            'kepada'        => $customer->nama_npwp ?? $customer->nama,
            'no'            => $no,
            'nomor_surat'   => sprintf('%03d', $no) . '/SJ/TM-' . $month_roman . '/' . date('Y', strtotime($tgl)),
            'tgl_sj'   => $tgl
        ];


        $noJNL = Jurnal::where('tipe', 'JNL')->whereMonth('tgl', $tgl_m)->orderBy('no', 'desc')->first() ?? 0;
        $no_JNL =  $noJNL ? $noJNL->no + 1 : 1;
        $tipeJurnal = $tgl_m . '-' . $no_JNL . '/' . 'TM' . '/' . $tgl_y;
        $monthNumber = (int) substr($tgl, 5, 2);
        $nsfp = NSFP::where('available', '1')->orderBy('nomor')->take($jumlahBarang)->get();
        if ($nsfp->count() < $jumlahBarang) {
            return back()->with('error', 'NSFP Belum Tersedia, pastikan nomor NSFP tersedia.');
        }
        $data = array();
        $idtsk = array();
        $idtsk1 = array();
        $jumlah = array();
        $array_invoice = array();
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
            
        }
                    // dd( count($id_transaksi),$id_transaksi);
    $createdData = [];
$updatedData = [];
            foreach ($id_transaksi as $i => $id) {
    $transaction = Transaction::find($id);
    if ($transaction) {
    //     // if (!$existing) {
    //         // Proses create karena belum ada sebelumnya
    //         $sisa = $transaction->sisa - $jumlah_jual[$i];
    //         $bkeluar = $jumlah_jual[$i] + $transaction->jumlah_jual;
    //         $invx = $transaction->invoice_external;

    //         $trx = Transaction::create([
    //             'sisa' => $jumlah_jual[$i],
    //             'invoice_external' => $invx,
    //             'jumlah_jual' => $jumlah_jual[$i],
    //             'jumlah_beli' => $transaction->jumlah_beli,
    //             'satuan_jual' => $transaction->satuan_beli,
    //             'satuan_beli' => $transaction->satuan_beli,
    //             'keterangan' => 'direct_sale',
    //             'id_barang' => $transaction->id_barang,
    //             'id_supplier' => $transaction->id_supplier,
    //             'harga_beli' => $transaction->harga_beli,
    //             'harga_jual' => $harga_jual[$i],
    //             'no_bm' => $transaction->no_bm,
    //             'stts' => $transaction->stts
    //         ]);

    //         $transaction->update([
    //             'sisa' => $sisa,
    //             'jumlah_jual' => $bkeluar,
    //             'satuan_jual' => $transaction->satuan_beli
    //         ]);

            $idtsk1[] = $transaction->id;
            $idtsk[] =  $transaction->id;
            $invoice[ $transaction->id] = [ (string) ($i + 1) ];
            $jumlah[ $transaction->id] = [ $jumlah_jual[$i] ];
    }
}

            foreach ($invoice as $id_transaksi => $invoice) {
            foreach ($invoice as $idx => $item) {
                $data[$id_transaksi]['invoice'][$idx] = $item;
            }
        }
        $i = 0;
         foreach ($jumlah as $id_transaksi => $jumlah) {
            foreach ($jumlah as $idx => $item) {
                $data[$id_transaksi]['jumlah'][$idx] = $item;
                $trx = Transaction::find($id_transaksi);
                if ($trx) {
                    // Ambil barang berdasarkan id_barang
                    $barang = Barang::find($trx->id_barang);
                    $satuan = Satuan::find($barang->id_satuan);
                    $harga = $harga_jual[$i] ?? 0;
                    // if (isset($barang) && $barang->status_ppn === 'ya') {
                    //     $harga = $harga / 1.11;
                    // }
                    $data[$id_transaksi]['satuan_jual'][$idx] = $trx->satuan_jual;
                    $data[$id_transaksi]['satuan_beli'][$idx] = $trx->satuan_beli;
                    $data[$id_transaksi]['id_supplier'][$idx] = $trx->id_supplier;
                    $data[$id_transaksi]['harga_beli'][$idx] = $trx->harga_beli;
                    $data[$id_transaksi]['jumlah_beli'][$idx] = $trx->jumlah_beli;
                    $data[$id_transaksi]['no_bm'][$idx] = $trx->no_bm;
                    $data[$id_transaksi]['sisa'][$idx] = $trx->sisa;
                    $data[$id_transaksi]['invoice_external'][$idx] = $trx->invoice_external;
                    $data[$id_transaksi]['stts'][$idx] = $trx->stts;
                    $data[$id_transaksi]['harga_jual'][$idx] = $harga;
                    $data[$id_transaksi]['jumlah_jual'][$idx] = $trx->jumlah_jual;
                    $data[$id_transaksi]['keterangan'][$idx] = $trx->keterangan;
                     $data[$id_transaksi]['tgl_ds'][$idx] = date('Y-m-d');
                    if ($satuan) {
                        $data[$id_transaksi]['nama_satuan'][$idx] = $satuan->nama_satuan;
                    }

                    if ($satuan) {
                        $data[$id_transaksi]['nama_satuan'][$idx] = $satuan->nama_satuan;
                    }
                    
                    // Pastikan barang ditemukan dan simpan nama barang
                    if ($barang) {
                         $data[$id_transaksi]['id_barang'][$idx] = $barang->id;
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
                    $i++;
                } else {
                    $data[$id_transaksi]['nama_barang'][$idx] = 'Transaksi tidak ditemukan'; // Penanganan jika transaksi tidak ada
                    $data[$id_transaksi]['satuan'][$idx] = 'Satuan tidak ditemukan'; // Penanganan jika transaksi tidak ada
                }
            }
        }
        $modified = null;
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
         return view('direct_sale.preview-ds', compact('nama_cust', 'surat_jalan','jumlahBarang', 'tipeJurnal', 'data', 'inv', 'barang', 'satuan', 'tgl', 'tgl_inv', 'transaksi', 'modified'));
    }

     public function dsInv_store(Request $request){
        $tipe = $request->tipe;
        $sj['tgl_sj'] = $request->tgl_sj;
        $sj['id_customer'] = $request->id_customer;
        $sj['kepada'] = $request->kepada;
        $sj['no'] = $request->no;
        $sj['nomor_surat'] =  $request->nomor_surat;
        $surat_jalan = SuratJalan::create($sj);
        $tgl_invoice= $request->tgl_invoice;
        $invoice= $request->invoice;
        $nsfp = $request->nsfp;
        $data= $request->data;
        $createdTrx = []; // untuk menyimpan transaksi baru
            foreach ($data as $id_transaksi => $items) {
        // Ambil data dari array
        $jumlah_jual     = $items['jumlah'][0];
        $jumlah_beli     = $items['jumlah_beli'][0];
        $satuan_jual     = $items['satuan_jual'][0];
        $satuan_beli     = $items['satuan_beli'][0];
        $harga_jual = $items['harga_jual'][0];
        $sts_ppn    = $items['status_ppn'][0] ?? null;

        if ($sts_ppn == 'ya') {
            $harga_jual = $harga_jual / 1.11;
        }
        $harga_beli      = $items['harga_beli'][0];
        $keterangan      = $items['keterangan'][0] ?? null;
        $id_barang       = $items['id_barang'][0] ?? null;
        $id_supplier     = $items['id_supplier'][0] ?? null;
        $invoice_external= $items['invoice_external'][0] ?? null;
        $no_bm           = $items['no_bm'][0] ?? null;
        $stts            = $items['stts'][0] ?? null;
        $id_nsfp = $items['id_nsfp'];

        // Temukan transaksi lama berdasarkan ID (key array)
        $transaction = Transaction::find($id_transaksi);
        if (!$transaction) continue;

        // Hitung sisa dan jumlah keluar baru
        $sisa_baru   = $transaction->sisa - $jumlah_jual;
        $bkeluar     = $transaction->jumlah_jual + $jumlah_jual;

        // Buat data transaksi baru
        $trxBaru = Transaction::create([
            'id_surat_jalan'   => $surat_jalan->id,
            'sisa'             => 0,
            'invoice_external' => $invoice_external,
            'jumlah_jual'      => $jumlah_jual,
            'jumlah_beli'      => $jumlah_beli,
            'satuan_jual'      => $satuan_beli,
            'satuan_beli'      => $satuan_beli,
            'keterangan'       => $keterangan ?? null,
            'id_barang'        => $id_barang,
            'id_supplier'      => $id_supplier,
            'harga_beli'       => $harga_beli,
            'harga_jual'       => $harga_jual,
            'no_bm'            => $no_bm,
            'stts'             => $stts
        ]);

        $createdTrx[] = [
        'trx_id' => $trxBaru->id,
        'jumlah_jual' => $jumlah_jual,
        'harga_jual' => $harga_jual,
        'id_nsfp' => $items['id_nsfp'],
        'no' => $items['no'],
        ];

        // Update data transaksi lama
        $transaction->update([
            'sisa'         => $sisa_baru,
            'jumlah_jual'  => $bkeluar,
            'satuan_jual'  => $satuan_beli
        ]);

        NSFP::find($id_nsfp)->update([
                'available' => 0,
                'invoice' => $invoice,
                'nomor' => $nsfp // Update kolom 'nomor' di tabel nsfp dengan hasil modifikasi
            ]);
    }
                // Hitung subtotal
                foreach ($createdTrx as $trx) {
    $subtotal = $trx['jumlah_jual'] * $trx['harga_jual'];

    Invoice::create([
        'id_transaksi' => $trx['trx_id'],
        'id_nsfp'      => $trx['id_nsfp'],
        'invoice'      => $invoice,
        'harga'        => $trx['harga_jual'],
        'jumlah'       => $trx['jumlah_jual'],
        'subtotal'     => $subtotal,
        'no'           => $trx['no'],
        'tgl_invoice'  => $tgl_invoice,
    ]);
}
 $this->autoJurnal($request->data, $invoice, $tipe, $tgl_invoice, $nsfp,$transaction->id);
return to_route('keuangan.invoice.cetak', ['invoice' => $invoice])
    ->with('success', 'Data Invoice berhasil disimpan');

    }

        private function autoJurnal($idtsk, $invoice, $tipe, $tgl, $nsfp) {
        $bulan = date('m', strtotime($tgl));
        $bulanNow = date('m');
        $tipe1 = $tipe;
        $breakTipe = explode("-", $tipe1);;
        $breakTipe1 = explode("/", $breakTipe[1]);
        $no = $breakTipe1[0];
        $no1= $no + 1;
        $jurhut = $bulanNow . '-' . $no . '/' . $breakTipe1[1] . '/' . $breakTipe1[2];
        $jurnalInvx = $bulanNow . '-' . $no1 . '/' . $breakTipe1[1] . '/' . $breakTipe1[2];
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
        $invoice_external1 = Transaction::whereIn('id',$id_transaksi)->get();
        $invoice_external = $invoice_external1->pluck('invoice_external')->first();
                    $temp_debit = 0; 
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
                            'nomor' => $jurhut,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                            'debit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn + array_sum(array_column($result->toArray(), 'subtotal'))) , // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => null,
                            'id_transaksi' => $result[0]->id_transaksi,
                            'nopol' => '-',
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no
                        ]);

                        foreach ($result as $item) {
                            Jurnal::create([
                                'coa_id' => 52, // COA untuk Pendapatan
                                'nomor' => $jurhut,
                                'tgl' => $tgl,
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => round($item->subtotal), // Hanya subtotal di kredit
                                'invoice' => $item->invoice,
                                'invoice_external' => null,
                                'id_transaksi' => $item->id_transaksi,
                                'nopol' => '-',
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $no
                            ]);
                        }
                            Jurnal::create([
                                'coa_id' => 12, // COA untuk PPN Keluaran
                                'nomor' => $jurhut,
                                'tgl' => $tgl,
                                'keterangan' => 'PPN Keluaran ' . $result[0]->transaksi->suratJalan->customer->nama . ' (FP: ' . $nsfp . ')',
                                'debit' => 0, // Debit diisi 0
                                'kredit' => round(array_sum(array_column($result->toArray(), 'subtotal')) * $value_ppn), // Nilai PPN di kredit
                                'invoice' => $invoice,
                                'invoice_external' => null,
                                'id_transaksi' => $result[0]->id_transaksi,
                                'nopol' => '-',
                                'container' => null,
                                'tipe' => 'JNL',
                                'no' => $no
                            ]);
                           
                             //Jurnal Hutang PPN 
                             
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                                               
                        ///batas
                            //Jurnal Hutang PPN 
                             //coa 6.2.1 = Biaya Operasional Trading Bulan Berjalan(Debit)
                             
                             foreach ($result as $item) {
                                Jurnal::create([
                                    'coa_id' => 63,
                                    'nomor' => $jurnalInvx,
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
                                    'nopol' => null,
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $no1
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
                                             'nomor' => $jurnalInvx,
                                             'tgl' => $tgl,
                                             'keterangan' => 'Persediaan Jayapura ' . $supplierName,
                                             'debit' => 0,
                                             'kredit' => $kredit,
                                             'invoice' => null,
                                             'invoice_external' => $inv_x,
                                             'id_transaksi' => $item->id_transaksi,
                                             'nopol' => '-',
                                             'container' => null,
                                             'tipe' => 'JNL',
                                             'no' => $no1
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
                            'nomor' => $jurhut,
                            'tgl' => $tgl,
                            'keterangan' => 'Piutang ' . $result[0]->transaksi->suratJalan->customer->nama . '-' . $result[0]->transaksi->suratJalan->customer->kota,
                            'debit' => round(array_sum(array_column($result->toArray(), 'subtotal'))), // Debit diisi 0
                            'kredit' => 0, // Menggunakan total debit sebagai kredit
                            'invoice' => $invoice,
                            'invoice_external' => null,
                            'id_transaksi' => $result[0]->id_transaksi,
                            'nopol' => '-',
                            'container' => null,
                            'tipe' => 'JNL',
                            'no' => $no
                        ]);
                        foreach ($result as $item) {
                            $temp_debit += $item->subtotal; // Total debit
                            
                            // Mengambil nomor polisi
                            $nopol = $item->transaksi->suratJalan->no_pol;
            
                            // Membuat entri jurnal untuk debit
                            Jurnal::create([
                                'coa_id' => 52,
                                'nomor' => $jurhut,
                                'tgl' => $tgl,
                                'keterangan' => 'Pendapatan ' . $item->transaksi->barang->nama . ' (' . $item->jumlah . ' ' . $item->transaksi->satuan_jual . ' Harsat ' . number_format($item->transaksi->harga_jual, 2, ',', '.') . ')',
                                'debit' => 0, // Menyimpan subtotal sebagai debit
                                'kredit' => round($item->subtotal), // Kredit diisi 0
                                'invoice' => $item->invoice,
                                'invoice_external' => null,
                                'id_transaksi' => $item->id_transaksi,
                                'nopol' => '-',
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
                                    'nomor' => $jurnalInvx,
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
                                    'nopol' => '-',
                                    'container' => null,
                                    'tipe' => 'JNL',
                                    'no' =>  $no1
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
                                     'nomor' => $jurnalInvx,
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
                                     'no' => $no1
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
        }
}
