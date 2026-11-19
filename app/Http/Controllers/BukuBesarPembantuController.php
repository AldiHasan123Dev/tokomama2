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
    $endDate = Carbon::create($selectedYear, $selectedMonth)
        ->endOfMonth()
        ->toDateString();

    $customers = collect();
    $suppliers = collect();
    $ncsDetails = [];
    $ncsDebitTotal = 0;
    $ncsKreditTotal = 0;

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER
    |--------------------------------------------------------------------------
    */
    if ($selectedState == 'customer') {

        // Ambil semua jurnal lebih dulu
        $jurnals = Jurnal::query()
            ->select('invoice', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
            ->where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNotNull('invoice')
            ->groupBy('invoice')
            ->get()
            ->keyBy('invoice');

        // Ambil semua invoice + relasi transaksi dan surat jalan
        $invoices = Invoice::query()
            ->select('invoice', 'id_transaksi')
            ->with([
                'transaksi:id,id_surat_jalan',
                'transaksi.suratJalan:id,id_customer'
            ])
            ->get();

        $customerTotals = [];

        foreach ($invoices as $inv) {

            if (
                !$inv->transaksi ||
                !$inv->transaksi->suratJalan
            ) {
                continue;
            }

            $customerId = $inv->transaksi->suratJalan->id_customer;

            if (!isset($jurnals[$inv->invoice])) {
                continue;
            }

            if (!isset($customerTotals[$customerId])) {
                $customerTotals[$customerId] = [
                    'debit' => 0,
                    'kredit' => 0,
                    'processed_invoice' => []
                ];
            }

            // Hindari invoice double
            if (in_array($inv->invoice, $customerTotals[$customerId]['processed_invoice'])) {
                continue;
            }

            $customerTotals[$customerId]['debit'] += $jurnals[$inv->invoice]->total_debit;
            $customerTotals[$customerId]['kredit'] += $jurnals[$inv->invoice]->total_kredit;
            $customerTotals[$customerId]['processed_invoice'][] = $inv->invoice;
        }

        $customers = Customer::query()
            ->get()
            ->map(function ($customer) use ($customerTotals) {

                $customer->debit = $customerTotals[$customer->id]['debit'] ?? 0;
                $customer->kredit = $customerTotals[$customer->id]['kredit'] ?? 0;

                return $customer;
            })
            ->sortByDesc(function ($customer) {
                return [$customer->debit, $customer->kredit];
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER
    |--------------------------------------------------------------------------
    */
    if ($selectedState == 'supplier') {

        // Ambil jurnal sekali saja
        $jurnals = Jurnal::query()
            ->select(
                'invoice_external',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(kredit) as total_kredit')
            )
            ->where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNotNull('invoice_external')
            ->where(function ($query) {
                $query->whereNull('invoice')
                    ->orWhere('invoice', '')
                    ->orWhere('invoice', '-')
                    ->orWhere('invoice', 0);
            })
            ->groupBy('invoice_external')
            ->get()
            ->keyBy('invoice_external');

        // Mapping transaksi supplier
        $transactions = Transaction::query()
            ->select('id_supplier', 'invoice_external')
            ->whereNotNull('invoice_external')
            ->get();

        $supplierTotals = [];

        foreach ($transactions as $tr) {

            if (!isset($jurnals[$tr->invoice_external])) {
                continue;
            }

            if (!isset($supplierTotals[$tr->id_supplier])) {
                $supplierTotals[$tr->id_supplier] = [
                    'debit' => 0,
                    'kredit' => 0,
                    'processed_invoice' => []
                ];
            }

            // Hindari double invoice_external
            if (in_array($tr->invoice_external, $supplierTotals[$tr->id_supplier]['processed_invoice'])) {
                continue;
            }

            $supplierTotals[$tr->id_supplier]['debit'] += $jurnals[$tr->invoice_external]->total_debit;
            $supplierTotals[$tr->id_supplier]['kredit'] += $jurnals[$tr->invoice_external]->total_kredit;

            $supplierTotals[$tr->id_supplier]['processed_invoice'][] = $tr->invoice_external;
        }

        $suppliers = Supplier::query()
            ->get()
            ->map(function ($supplier) use ($supplierTotals) {

                $supplier->debit = $supplierTotals[$supplier->id]['debit'] ?? 0;
                $supplier->kredit = $supplierTotals[$supplier->id]['kredit'] ?? 0;

                return $supplier;
            })
            ->sortByDesc(function ($supplier) {
                return [$supplier->debit, $supplier->kredit];
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | NCS
    |--------------------------------------------------------------------------
    */
    if ($selectedState == 'ncs') {

        $ncsRecords = Jurnal::query()
            ->where('coa_id', $selectedCoaId)
            ->whereBetween('tgl', [$startDate, $endDate])
            ->whereNull('invoice_external')
            ->whereNull('invoice')
            ->whereNotNull('keterangan_buku_besar_pembantu')
            ->orderBy('tgl', 'asc')
            ->get([
                'tgl',
                'nomor',
                'keterangan',
                'debit',
                'kredit',
                'keterangan_buku_besar_pembantu'
            ]);

        $grouped = $ncsRecords->groupBy('keterangan_buku_besar_pembantu');

        foreach ($grouped as $key => $items) {

            $first = $items->first();

            $ncsDetails[] = [
                'tgl' => $first->tgl,
                'nomor' => $first->nomor,
                'keterangan' => $first->keterangan,
                'debit' => $items->sum('debit'),
                'kredit' => $items->sum('kredit'),
                'details' => $items->skip(1)->map(function ($item) {
                    return [
                        'tgl' => $item->tgl,
                        'nomor' => $item->nomor,
                        'keterangan' => $item->keterangan,
                    ];
                })->values()->toArray()
            ];

            $ncsDebitTotal += $items->sum('debit');
            $ncsKreditTotal += $items->sum('kredit');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TIPE AKUN
    |--------------------------------------------------------------------------
    */
    $akun = Coa::query()
        ->where('id', $selectedCoaId)
        ->orWhere('no_akun', $selectedCoaId)
        ->get();

    $tipe = 'D';

    foreach ($akun as $item) {
        if (in_array(substr($item->no_akun, 0, 1), ['2', '3', '5'])) {
            $tipe = 'K';
        }
    }

    return view(
        'jurnal.buku-besar-pembantu',
        compact(
            'customers',
            'suppliers',
            'coa',
            'selectedYear',
            'selectedMonth',
            'selectedCoaId',
            'tipe',
            'selectedState',
            'ncsDetails',
            'ncsDebitTotal',
            'ncsKreditTotal'
        )
    );
}


public function detail(Request $request)
{
    $coa = Coa::where('status', 'aktif')->get();

    $selectedYear = $request->input('year', date('Y'));
    $selectedMonth = $request->input('month', date('m'));
    $selectedCoaId = $request->input('coa_id', 8);

    $startDate = '2023-01-01';
    $endDate = Carbon::create($selectedYear, $selectedMonth)->endOfMonth();

    $totalDebit = 0;
    $totalKredit = 0;
    $details = [];

    // ambil semua id customer
    $customerIds = Customer::whereNull('deleted_at')->pluck('id');

    // ambil semua surat jalan
    $suratJalanIds = SuratJalan::whereIn('id_customer', $customerIds)->pluck('id');

    // ambil transaksi
    $transaksiIds = Transaction::whereIn('id_surat_jalan', $suratJalanIds)->pluck('id');

    // ambil invoice
    $invoices = Invoice::whereIn('id_transaksi', $transaksiIds)
        ->pluck('invoice')
        ->unique();
 
    // ambil jurnal
    $jurnals = Jurnal::whereIn('invoice', $invoices)
        ->where('coa_id', $selectedCoaId)
        ->whereNotNull('id_transaksi')
        ->whereBetween('tgl', [$startDate, $endDate])
        ->get();

   $jurnalsGrouped = $jurnals->groupBy('invoice');

foreach ($jurnalsGrouped as $invoice => $rows) {

    $debitRows = $rows->where('debit', '>', 0);
    $kreditRows = $rows->where('kredit', '>', 0);

    $debit = $debitRows->sum('debit');
    $kredit = $kreditRows->sum('kredit');

    $debitRow = $debitRows->first();
    $kreditRow = $kreditRows->first();

    // hanya ambil yang debit dan kredit tidak sama
    if ($debit != $kredit) {

        $details[] = [
    'tgl_debit' => $debitRows->pluck('tgl')->implode(', '),
    'nomor_debit' => $debitRows->pluck('nomor')->implode(', '),

    'tgl_kredit' => $kreditRows->pluck('tgl')->implode(', '),
    'nomor_kredit' => $kreditRows->pluck('nomor')->implode(', '),

    'invoice' => $invoice,

    'debit' => $debit,
    'kredit' => $kredit,

    'keterangan' => trim(
        $debitRows->pluck('keterangan')->implode(', ')
        . ', ' .
        $kreditRows->pluck('keterangan')->implode(', ')
    ),
];

        $totalDebit += $debit;
        $totalKredit += $kredit;
    }
}

    return view('jurnal.buku-besar-pembantu-detail', compact(
        'details',
        'totalDebit',
        'totalKredit',
        'coa',
        'selectedYear',
        'selectedMonth',
        'selectedCoaId'
    ));
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
