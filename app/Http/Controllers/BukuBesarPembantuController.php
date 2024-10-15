<?php

namespace App\Http\Controllers;

use App\Models\BukuBesarPembantu;
use App\Models\Coa;
use App\Models\Nopol;
use App\Models\TemplateJurnal;
use App\Models\Transaction;
use App\Models\Jurnal;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SuratJalan;
use App\Models\Transaksi;
use App\Models\Invoice;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exports\NcsExport;
use App\Exports\CustomerExport;

use App\Exports\SupplierExport;



use Maatwebsite\Excel\Facades\Excel;

class BukuBesarPembantuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
   
    $coa = Coa::where('status', 'aktif')->get();

   
    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);
    $selectedState = $request->input('state', 'customer');

    
    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth()->toDateString();

   
    $customers = collect(); 
    $suppliers = collect(); 
    $ncsDetails = [];
    $ncsDebitTotal = 0; 
    $ncsKreditTotal = 0; 

    // Logika untuk pelanggan (customers)
    if ($selectedState == 'customer') {
        $customers = Customer::all(); // Menggunakan all() untuk mendapatkan koleksi
        foreach ($customers as $customer) {
            $suratJalan = SuratJalan::where('id_customer', $customer->id)->get();
            $debitTotal = 0;
            $kreditTotal = 0;

            foreach ($suratJalan as $sj) {
                $transaksi = Transaction::where('id_surat_jalan', $sj->id)->get();
                $processedInvoices = [];

                foreach ($transaksi as $tr) {
                    $invoices = Invoice::where('id_transaksi', $tr->id)->get();

                    foreach ($invoices as $inv) {
                        if (in_array($inv->invoice, $processedInvoices)) {
                            continue;
                        }

                        $jurnals = Jurnal::where('invoice', $inv->invoice)
                            ->where('coa_id', $selectedCoaId)
                            ->whereBetween('tgl', [$startDate, $endDate])
                            ->get();

                        foreach ($jurnals as $j) {
                            if ($j->debit > 0 || $j->kredit > 0) {
                                $debitTotal += $j->debit;
                                $kreditTotal += $j->kredit;
                            }
                        }

                        $processedInvoices[] = $inv->invoice;
                    }
                }
            }

            $customer->debit = $debitTotal;
            $customer->kredit = $kreditTotal;
        }
    }

    // Logika untuk pemasok (suppliers)
    if ($selectedState == 'supplier') {
        $suppliers = Supplier::all(); // Menggunakan all() untuk mendapatkan koleksi
        foreach ($suppliers as $supplier) {
            $debitTotal = 0;
            $kreditTotal = 0;

            // Ambil semua jurnal untuk supplier berdasarkan invoice_external
            $jurnals = Jurnal::where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNotNull('invoice_external') 
            ->where(function($query) {
                $query->whereNull('invoice')
                      ->orWhere('invoice', '')
                      ->orWhere('invoice', '-')
                      ->orWhere('invoice', 0);
            })->get();
            foreach ($jurnals as $j) {
                $transaksi = Transaction::where('invoice_external', $j->invoice_external)
                    ->where('id_supplier', $supplier->id)
                    ->first();

                if ($transaksi && ($j->debit > 0 || $j->kredit > 0)) {
                    $debitTotal += $j->debit;
                    $kreditTotal += $j->kredit;
                }
            }

            $supplier->debit = $debitTotal;
            $supplier->kredit = $kreditTotal;
        }
    }

   
    if ($selectedState == 'ncs') {
       $ncsRecords = Jurnal::where('coa_id', $selectedCoaId)
        ->whereBetween('tgl', [$startDate, $endDate])
        ->where(function($query) {
            $query->whereNull('invoice')
                ->orWhere('invoice', '')
                ->orWhere('invoice', '-')
                ->orWhere('invoice', 0);
        })
        ->where(function($subquery) {
            $subquery->whereNull('invoice_external')
                    ->orWhere('invoice_external', '')
                    ->orWhere('invoice_external', '-')
                    ->orWhere('invoice_external', 0)
                    ->orWhereRaw('LENGTH(TRIM(invoice_external)) = 0'); // Hanya menangkap nilai kosong
        })
        ->where('invoice_external', 'NOT LIKE', '%PO%') // Mengecualikan nilai yang mengandung 'PO'
        ->whereNotNull('keterangan_buku_besar_pembantu')
        ->orderBy('tgl', 'asc')
        ->get(['tgl', 'nomor', 'keterangan', 'debit', 'kredit', 'keterangan_buku_besar_pembantu']);
    
        $ncsDetails = [];
        $ncsDebitTotal = 0;
        $ncsKreditTotal = 0;
    
        foreach ($ncsRecords as $j) {
            // Menggunakan keterangan_buku_besar_pembantu sebagai key
            $key = $j->keterangan_buku_besar_pembantu;
    
            // Jika keterangan_buku_besar_pembantu belum ada dalam ncsDetails
            if (!isset($ncsDetails[$key])) {
                $ncsDetails[$key] = [
                    'tgl' => $j->tgl,
                    'nomor' => $j->nomor,
                    'keterangan' => $j->keterangan,
                    'debit' => $j->debit,
                    'kredit' => $j->kredit,
                    'details' => []
                ];
            } else {
                // Jika sudah ada, tambahkan detail ke dalam details
                $ncsDetails[$key]['details'][] = [
                    'tgl' => $j->tgl,
                    'nomor' => $j->nomor,
                    'keterangan' => $j->keterangan
                ];
    
                // Akumulasikan debit dan kredit
                $ncsDetails[$key]['debit'] += $j->debit;
                $ncsDetails[$key]['kredit'] += $j->kredit;
            }
    
            // Hitung total debit dan kredit untuk keseluruhan
            $ncsDebitTotal += $j->debit;
            $ncsKreditTotal += $j->kredit;
        }
    
        // Ubah $ncsDetails menjadi array numerik untuk kemudahan penanganan di view
        $ncsDetails = array_values($ncsDetails);
    }
    
    



   

    // Tentukan tipe (tipe) untuk perhitungan saldo
    $akun = Coa::where('id', $selectedCoaId)
        ->orWhere('no_akun', $selectedCoaId)
        ->get();

    $tipe = 'D';
    foreach ($akun as $item) {
        if (in_array(substr($item->no_akun, 0, 1), ['2', '3', '5'])) {
            $tipe = 'K';
        }
    }

    return view('jurnal.buku-besar-pembantu', compact('customers', 'suppliers',  'coa', 'selectedYear', 'selectedMonth', 'selectedCoaId', 'tipe', 'selectedState', 'ncsDetails', 'ncsDebitTotal', 'ncsKreditTotal'));
}



public function showDetail($id, Request $request)
{
    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);
    $selectedState = $request->input('state', 'customer'); 

    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth()->toDateString();
    
    $suratJalan = SuratJalan::where('id_customer', $id)->get();

    $details = [];
    $coa = Coa::findOrFail($selectedCoaId);
    $totalDebit = 0;
    $totalKredit = 0;

    if ($selectedState == 'customer') {
        $entity = Customer::findOrFail($id);
        $suratJalan = SuratJalan::where('id_customer', $id)->get();

        foreach ($suratJalan as $sj) {
            $transaksi = Transaction::where('id_surat_jalan', $sj->id)->get();
            $processedInvoices = [];

            foreach ($transaksi as $tr) {
                $invoices = Invoice::where('id_transaksi', $tr->id)->get();

                foreach ($invoices as $inv) {
                    if (in_array($inv->invoice, $processedInvoices)) {
                        continue;
                    }

                    $jurnals = Jurnal::where('invoice', $inv->invoice)
                        ->where('coa_id', $selectedCoaId)
                        ->whereBetween('tgl', [$startDate, $endDate])
                        ->get();

                    foreach ($jurnals as $j) {
                        if ($j->debit > 0 || $j->kredit > 0) {
                            $details[] = [
                                'nomor' => $j->nomor,
                                'tgl' => $j->tgl,
                                'invoice' => $inv->invoice,
                                'debit' => $j->debit,
                                'kredit' => $j->kredit,
                                'keterangan' => $j->keterangan // Menambahkan keterangan
                            ];
                            $totalDebit += $j->debit;
                            $totalKredit += $j->kredit;
                        }
                    }

                    $processedInvoices[] = $inv->invoice;
                }
            }
        }
        $entityName = $entity->nama;
    } elseif ($selectedState == 'supplier') {
        $entity = Supplier::findOrFail($id);

        $jurnals = Jurnal::where('coa_id', $selectedCoaId)
        ->whereBetween('tgl', [$startDate, $endDate])
        ->whereNotNull('invoice_external') 
        ->where(function($query) {
            $query->whereNull('invoice')
                  ->orWhere('invoice', '')
                  ->orWhere('invoice', '-')
                  ->orWhere('invoice', 0);
        })->get();

        foreach ($jurnals as $j) {
            $transaksi = Transaction::where('invoice_external', $j->invoice_external)
                ->where('id_supplier', $entity->id)
                ->first();

            if ($transaksi && ($j->debit > 0 || $j->kredit > 0)) {
                $details[] = [
                    'nomor' => $j->nomor,
                    'tgl' => $j->tgl,
                    'invoice_external' => $j->invoice_external,
                    'debit' => $j->debit,
                    'kredit' => $j->kredit,
                    'keterangan' => $j->keterangan // Menambahkan keterangan
                ];
                $totalDebit += $j->debit;
                $totalKredit += $j->kredit;
            }
        }
        $entityName = $entity->nama;
    } elseif ($selectedState == 'ncs') { // Logika untuk NCS (Non-Customer/Supplier)
        $ncsRecords = Jurnal::where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNotNull('nomor') 
            ->whereNotNull('keterangan_buku_besar_pembantu') 
            ->whereColumn('nomor', 'keterangan_buku_besar_pembantu') 
            ->orderBy('tgl', 'asc')
            ->get();
    
        foreach ($ncsRecords as $j) {
            // Tambahkan detail langsung tanpa pengelompokan
            if ($j->debit > 0 || $j->kredit > 0) {
                $details[] = [
                    'tgl' => $j->tgl,
                    'nomor' => $j->nomor, // Tambahkan nomor
                    'keterangan_buku_besar_pembantu' => $j->keterangan_buku_besar_pembantu, // Update keterangan
                    'keterangan' => $j->keterangan,
                    'debit' => $j->debit,
                    'kredit' => $j->kredit,
                ];
                $totalDebit += $j->debit;
                $totalKredit += $j->kredit;
            }
        }
        $entityName = 'NCS'; // Set nama entitas sebagai NCS
    }
    

    // Calculate balance (saldo)
    $view_total = ($coa->tipe == 'K') ? $totalKredit - $totalDebit : $totalDebit - $totalKredit;

    return response()->json([
        'entity' => isset($entity) ? $entity : null,
        'details' => $details,
        'coa' => $coa,
        'totalDebit' => $totalDebit,
        'totalKredit' => $totalKredit,
        'view_total' => $view_total, // Pass view_total to the view
        'entityName' => $entityName
    ]);
}

public function exportNcs(Request $request)
{
    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);
    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth()->toDateString();

    $exportData = [];
    $ncsRecords = Jurnal::where('coa_id', $selectedCoaId)
        ->whereBetween('tgl', [$startDate, $endDate])
        ->orderBy('tgl', 'asc')
        ->get(['tgl', 'nomor', 'keterangan', 'debit', 'kredit', 'keterangan_buku_besar_pembantu']);

    $ncsDetails = [];
    foreach ($ncsRecords as $j) {
        $formattedDate = Carbon::parse($j->tgl)->format('Y-m-d');
        $coa = Coa::find($selectedCoaId);

        // Initialize or update the main entry
        if (!isset($ncsDetails[$j->keterangan_buku_besar_pembantu])) {
            $ncsDetails[$j->keterangan_buku_besar_pembantu] = [
                'tgl' => $formattedDate,
                'nomor' => $j->nomor,
                'keterangan' => $j->keterangan,
                'debit' => $j->debit,
                'kredit' => $j->kredit,
                'saldo' => $j->kredit - $j->debit,
                'details' => []
            ];
        } else {
            $ncsDetails[$j->keterangan_buku_besar_pembantu]['debit'] += $j->debit;
            $ncsDetails[$j->keterangan_buku_besar_pembantu]['kredit'] += $j->kredit;
            $ncsDetails[$j->keterangan_buku_besar_pembantu]['saldo'] = $ncsDetails[$j->keterangan_buku_besar_pembantu]['kredit'] - $ncsDetails[$j->keterangan_buku_besar_pembantu]['debit'];
        }

        // Add detail information
        $ncsDetails[$j->keterangan_buku_besar_pembantu]['details'][] = [
            'tgl' => $formattedDate,
            'nomor' => $j->nomor,
            'keterangan' => $j->keterangan
        ];
    }

    // Convert associative array to numeric array for easier processing
    $ncsDetails = array_values($ncsDetails);

    foreach ($ncsDetails as $key => $ncs) {
        // Add main entry
        $exportData[] = [
            'no' => $key + 1,
            'tanggal' => $ncs['tgl'],
            'nomor' => $ncs['nomor'],
            'keterangan' => $ncs['keterangan'],
            'debit' => number_format($ncs['debit'], 2, ',', '.'),
            'kredit' => number_format($ncs['kredit'], 2, ',', '.'),
            'saldo' => number_format($ncs['saldo'], 2, ',', '.'),
            'tanggal_detail' => implode("\n", array_column($ncs['details'], 'tgl')),
            'nomor_detail' => implode("\n", array_column($ncs['details'], 'nomor')),
            'keterangan_detail' => implode("\n", array_column($ncs['details'], 'keterangan'))
        ];
    }

    return Excel::download(new NcsExport($exportData), 'ncs_export.xlsx');
}


public function exportCustomer(Request $request)
{

    $coa = Coa::where('status', 'aktif')->get();

 
    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);


    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth()->toDateString();

  
    $exportData = [];


    $customers = Customer::all();
    foreach ($customers as $customer) {
        $suratJalan = SuratJalan::where('id_customer', $customer->id)->get();

        foreach ($suratJalan as $sj) {
            $transaksi = Transaction::where('id_surat_jalan', $sj->id)->get();

            foreach ($transaksi as $tr) {
                $invoices = Invoice::where('id_transaksi', $tr->id)->get();

                foreach ($invoices as $inv) {
                
                    $jurnals = Jurnal::where('invoice', $inv->invoice)
                        ->where('coa_id', $selectedCoaId)
                        ->whereBetween('tgl', [$startDate, $endDate])

                        ->get();

               
                    foreach ($jurnals as $j) {
                    
                        $formattedDate = Carbon::parse($j->tgl)->format('Y-m-d');
                        $coa = Coa::find($selectedCoaId);

                        
                        $data = [
                            'customer_name' => $customer->nama,
                            'invoice' => $inv->invoice,
                            'tanggal' => $formattedDate,
                            'no_akun' => $coa->no_akun ?? '',
                            'debit' => number_format($j->debit, 2, ',', '.'), 
                            'kredit' => number_format($j->kredit, 2, ',', '.'), 
                            'keterangan' => $j->keterangan ?? '', 
                        ];

                        
                        if (!in_array($data, $exportData)) {
                            $exportData[] = $data; 
                        }
                    }
                }
            }
        }
    }
  
    usort($exportData, function ($a, $b) {
        return strcmp($a['customer_name'], $b['customer_name']) ?: strcmp($a['tanggal'], $b['tanggal']);
    });


    return Excel::download(new CustomerExport($exportData), 'customer_export.xlsx');
}
public function exportSupplier(Request $request)
{

    $coa = Coa::where('status', 'aktif')->get();

  
    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);

 
    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth()->toDateString();

   
    $exportData = [];

  
    $suppliers = Supplier::all();
    foreach ($suppliers as $supplier) {
      
        $jurnals = Jurnal::where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNotNull('invoice_external') 
            ->get();

        foreach ($jurnals as $j) {
            $transaksi = Transaction::where('invoice_external', $j->invoice_external)
                ->where('id_supplier', $supplier->id)
                ->first();

            if ($transaksi) {
               
                $formattedDate = Carbon::parse($j->tgl)->format('Y-m-d');
                $coa = Coa::find($selectedCoaId);

         
                $exportData[] = [
                    'customer_name' => $supplier->nama,
                    'invoice' => $j->invoice_external,
                    'tanggal' => $formattedDate,
                    'no_akun' => $coa->no_akun ?? '',
                    'debit' => number_format($j->debit, 2, ',', '.'), 
                    'kredit' => number_format($j->kredit, 2, ',', '.'), 
                    'keterangan' => $j->keterangan ?? '',
                    
                ];
            }
        }
    }
    usort($exportData, function ($a, $b) {
        return strcmp($a['customer_name'], $b['customer_name']) ?: strcmp($a['tanggal'], $b['tanggal']);
    });

    return Excel::download(new SupplierExport($exportData), 'supplier_export.xlsx');
}


}
