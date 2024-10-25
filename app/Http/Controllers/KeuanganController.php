<?php

namespace App\Http\Controllers;

use App\Exports\OmzetExport;
use App\Http\Resources\OmzetResurce;
use App\Http\Resources\TransactionResource;
use App\Models\Barang;
use App\Models\Invoice;
use App\Models\Jurnal;
use App\Models\NSFP;
use App\Models\Satuan;

use App\Models\SuratJalan;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class KeuanganController extends Controller
{
    function index()
    {
        return redirect('keuangan.surat-jalan');
    }

    function suratJalan()
    {
        $masterBarangs = Barang::all();
        $no = SuratJalan::whereYear('created_at', date('Y'))->max('no') + 1;
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n", strtotime(date('Y-m-d'))); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number];
        $nomor = sprintf('%03d', $no) . '/SJ/SB-' . $month_roman . '/' . date('Y');
        return view('keuangan.surat-jalan', compact('masterBarangs', 'nomor', 'no'));
    }

    function suratJalanStore(Request $request): RedirectResponse
    {
        SuratJalan::create($request->all());
        return redirect()->route('keuangan.pre-invoice');
    }

    function invoice()
    {
        return view('keuangan.invoice');
    }

    function preInvoice()
    {
        return view('keuangan.pre-invoice');
    }

    function invoiceDraf(SuratJalan $surat_jalan)
    {
        return view('keuangan.draf_invoice', compact('surat_jalan'));
    }

    public function submitInvoice(SuratJalan $surat_jalan)
    {
        $nsfp = NSFP::where('available', '1')->orderBy('nomor')->first();
        if (!$nsfp) {
            return back()->with('error', 'NSFP Belum Tersedia, pastikan nomor NSFP tersedia.');
        }
        $data['invoice'] = str_replace('/SJ/', '/INV/', $surat_jalan->nomor_surat);
        $data['tgl_invoice'] = date('Y-m-d');
        $data['id_nsfp'] = $nsfp->id;
        $data['ppn'] = floatval(request('total')) * 0.1;
        $data['subtotal'] = floatval(request('total'));
        $data['total'] = floatval(request('total')) + $data['ppn'];
        $surat_jalan->update($data);
        $nsfp->update(['available' => '0', 'invoice' => $data['invoice']]);
        return redirect()->route('keuangan.invoice.cetak', $surat_jalan);
    }

    public function cetakInvoice()
{
    $invoice = request('invoice');
    $data = Invoice::where('invoice', $invoice)
   
    ->get();
    $dateTime = new DateTime($data[0]->tgl_invoice);
    $formattedDate = $dateTime->format('d F Y');
    
    $id_transaksi = $data[0]->transaksi->id;
    $transaksi = Transaction::where('id', $id_transaksi) ->first(); //keteran
    
    // Mencari id_surat_jalan dari transaksi
    $id_surat_jalan = $transaksi->id_surat_jalan;
    
    // Mencari no_count dari tabel surat_jalan berdasarkan id_surat_jalan
    $suratJalan = SuratJalan::where('id', $id_surat_jalan)
    
    ->first();
    $no_cont = $suratJalan ? $suratJalan->no_cont : null;
    
    
    $id_barang = $transaksi->id_barang;

    $barang = Barang::where('id', $id_barang)->first();
    
    $satuan = Satuan::where('id', $barang->id_satuan)->first();
    
    // Pass $no_count ke view
    $pdf = Pdf::loadView('keuangan/invoice_pdf', compact('data', 'invoice', 'barang', 'formattedDate', 'transaksi', 'satuan', 'no_cont'))->setPaper('a4', 'potrait');
    return $pdf->stream('invoice_pdf.pdf');
}


    public function cetakInvoicesp()
    {
        $invoice = request('invoice');
        $data = Invoice::where('invoice', request('invoice'))->get();
        $dateTime = new DateTime($data[0]->tgl_invoice);
        $formattedDate = $dateTime->format('d F Y');
        $id_transaksi = $data[0]->transaksi->id;
        $transaksi = Transaction::where('id', $id_transaksi)->first(); //keteran
        // dd($transaksi);
        // Mencari id_surat_jalan dari transaksi
        $id_surat_jalan = $transaksi->id_surat_jalan;
        // Mencari no_count dari tabel surat_jalan berdasarkan id_surat_jalan
        $suratJalan = SuratJalan::where('id', $id_surat_jalan) ->first();
        $no_cont = $suratJalan ? $suratJalan->no_cont : null;

        $id_barang = $transaksi->id_barang;
        $barang = Barang::where('id', $id_barang)->first();
        // dd($barang->id_satuan);
        $satuan = Satuan::where('id', $barang->id_satuan)->first();
        // dd($satuan->nama_satuan);
        // dd($data, $invoice, $barang, $formattedDate, $transaksi, $satuan->nama_satuan, $transaksi->satuan_jual, $transaksi->keterangan);
        $pdf = Pdf::loadView('keuangan/sp_pdf', compact('data','invoice', 'barang', 'formattedDate', 'transaksi', 'satuan', 'no_cont'))->setPaper('a4', 'potrait');
        return $pdf->stream('sp_pdf.pdf');
    }
    function generatePDF($id)
    {
        $surat_jalan = SuratJalan::where('id', $id)->get();
        $pdf = Pdf::loadView('keuangan/invoice_pdf', compact('surat_jalan'))->setPaper('a4', 'potrait');
        return $pdf->stream('invoice_pdf.pdf');
    }

    public function dataTable(Request $request)
    {
        // Ambil parameter pencarian
        $searchTerm = $request->get('searchString', ''); // Ganti 'searchString' sesuai parameter yang diinginkan
    
        // Ambil data dengan urutan berdasarkan 'created_at' DESC dan groupBy 'invoice'
        $data = Invoice::with(['nsfp', 'transaksi.barang']) // Eager loading relasi yang diperlukan
            ->orderBy('created_at', 'desc');
    
        // Tambahkan filter pencarian
        if (!empty($searchTerm)) {
            $data->where(function($query) use ($searchTerm) {
                $query->whereHas('nsfp', function($q) use ($searchTerm) {
                    $q->where('nomor', 'like', "%{$searchTerm}%");
                })
                ->orWhere('invoice', 'like', "%{$searchTerm}%");
            });
        }
    
        // Ambil semua data yang sudah difilter
        $data = $data->get()->groupBy('invoice');
        
        // Hitung total record sebelum pagination
        $totalRecords = $data->count();
    
        // Ambil data untuk pagination
        $currentPage = $request->page; // Ambil halaman saat ini dari request, default 1
        $perPage = $request->rows; // Ambil jumlah baris per halaman dari request, default 10
    
        // Hitung indeks untuk mengambil data yang benar
        $index = ($currentPage - 1) * $perPage; // Indeks awal
    
        // Slice data untuk pagination
        $paginatedData = $data->slice($index)->values();
    
        // Membuat array hasil untuk response JSON
        $result = $paginatedData->map(function ($row) use (&$index) {
            // Increment index untuk setiap baris
            $index++;
    
            return [
                'DT_RowIndex' => $index,
                'nsfp' => $row->first()->nsfp->nomor ?? '-',
                'invoice' => $row->first()->invoice ?? '-',
                'subtotal' => number_format($row->sum('subtotal'), 0, ',', '.'),
                'ppn' => $this->calculatePPN($row),
                'total' => $this->calculateTotal($row),
                'index' => $index // Menyertakan index dalam hasil
            ];
        });
    
        return response()->json([
               'current_page' => $request->page, // Halaman saat ini
            'last_page' => ceil($totalRecords / $request->rows), // Total halaman
            'total' => $totalRecords, // Total records
            'data' => $result, // Data untuk halaman ini
        ]);
    }
    
    // Fungsi untuk menghitung PPN
    private function calculatePPN($row)
    {
        $barang = $row->first()->transaksi->barang ?? null; // Ambil data barang
        if ($barang && $barang->status_ppn === 'ya') {
            return number_format($row->sum('subtotal') * ($barang->value_ppn / 100), 0, ',', '.');
        } else {
            return 0;
        }
    }
    
    // Fungsi untuk menghitung Total
    private function calculateTotal($row)
    {
        $subtotal = $row->sum('subtotal');
        $barang = $row->first()->transaksi->barang ?? null; // Ambil data barang
    
        if ($barang && $barang->status_ppn === 'ya') {
            return number_format($subtotal + ($subtotal * ($barang->value_ppn / 100)), 0, ',', '.');
        } else {
            return number_format($subtotal, 0, ',', '.');
        }
    }
    

    
    

    public function omzet()
    {
        $Jurnal = Jurnal::with(['invoice'])->get();
        // dd($Jurnal);
        return view('keuangan.omzet');
    }
    //     $jurnals = Jurnal::with('invoice') // Pastikan relasi sudah didefinisikan di model Jurnal
    // ->selectRaw('MONTH(tgl) as month, COUNT(invoice) as invoice_count, SUM(debit) as total') // Ganti 'created_at' dan 'invoice.id' dengan nama kolom yang sesuai
    // ->groupBy('month')
    // ->orderBy('month')
    // ->get();
    // $jurnalData = $jurnals->map(function($jurnal) {
    //     return [
    //         'month' => $jurnal->month,
    //         'invoice_count' => $jurnal->invoice_count,
    //         'total' => $jurnal->total
    //     ];
    // });
    //         $invoicesByMonth = [];
    // foreach ($invoices as $invoice) {
    //     $invoicesByMonth[$invoice->month] = [
    //         'invoice_count' => $invoice->invoice_count,
    //         'total' => $invoice->total,
    //     ];
    // }
    public function omzet_total()
    {
        // Mendapatkan tanggal saat ini
        $date = date('Y-m-d');
        $month_number = date("n", strtotime($date));
    
        // Mendapatkan data invoice dan transaksi terkait
        $invoices = Invoice::selectRaw('
        DATE_FORMAT(i.tgl_invoice, "%M") as month, 
        YEAR(i.tgl_invoice) as year, 
        COUNT(DISTINCT i.invoice) as invoice_count, 
        SUM(t.harga_beli * t.jumlah_beli) as total_harga_beli, 
        SUM(
            CASE 
                WHEN b.status_ppn = "ya" THEN t.harga_jual * t.jumlah_jual * 1.11
                ELSE t.harga_jual * t.jumlah_jual
            END
        ) as total_harga_jual')
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id_transaksi', '=', 't.id')
    ->join('barang as b', 't.id_barang', '=', 'b.id') // Join dengan tabel barang untuk mendapatkan status_ppn
    ->groupBy('year', 'month') // Grup berdasarkan tahun dan bulan
    ->orderBy('year', 'asc') // Urutkan berdasarkan tahun secara ascending
    ->orderByRaw('MONTH(i.tgl_invoice) ASC') // Urutkan berdasarkan bulan secara ascending
    ->get();

    
        // Mengorganisir data berdasarkan tahun
        $invoiceData = [];
        foreach ($invoices as $invoice) {
            $total_profit = $invoice->total_harga_jual - $invoice->total_harga_beli;
    
            // Hitung persentase profit
            $total_profit_percentage = 0;
            if ($invoice->total_harga_beli > 0) {
                $total_profit_percentage = round(($total_profit / $invoice->total_harga_beli) * 100, 2);
            }
    
            // Menyimpan data per tahun
            $invoiceData[$invoice->year][] = [
                'month' => $invoice->month,
                'invoice_count' => $invoice->invoice_count,
                'total_harga_beli' => $invoice->total_harga_beli / 1000,
                'total_harga_jual' => $invoice->total_harga_jual / 1000,
                'total_profit' => $total_profit / 1000,
                'total_profit_percentage' => $total_profit_percentage,
            ];
        }
    
        // Menghitung total per tahun
        $summaryData = [];
        foreach ($invoiceData as $year => $dataPerYear) {
            $summaryData[$year] = [
                'total_invoice_count' => 0,
                'total_harga_beli' => 0,
                'total_harga_jual' => 0,
                'total_profit' => 0,
                'total_profit_percentage' => 0,
            ];
    
            foreach ($dataPerYear as $data) {
                // Menambahkan total per tahun
                $summaryData[$year]['total_invoice_count'] += $data['invoice_count'];
                $summaryData[$year]['total_harga_beli'] += $data['total_harga_beli'];
                $summaryData[$year]['total_harga_jual'] += $data['total_harga_jual'];
            }
    
            // Hitung total profit
            $summaryData[$year]['total_profit'] = $summaryData[$year]['total_harga_jual'] - $summaryData[$year]['total_harga_beli'];
    
            // Hitung persentase total profit
            if ($summaryData[$year]['total_harga_beli'] > 0) {
                $summaryData[$year]['total_profit_percentage'] = 
                    round(($summaryData[$year]['total_profit'] / $summaryData[$year]['total_harga_beli']) * 100, 2);
            }
        }
    
        // Menyediakan array bulan
        $months = [
            'January', 'February', 'March', 'April', 'May', 
            'June', 'July', 'August', 'September', 
            'October', 'November', 'December'
        ];
    
        // Mengembalikan view dengan data
        return view('keuangan.omzet-total', compact('invoiceData', 'months', 'summaryData'));
    }        
    
    public function dataOmzeTotal()
{
    $date = date('Y-m-d');
    $month_number = date("n", strtotime($date));

    $invoices = Invoice::selectRaw('DATE_FORMAT(i.tgl_invoice, "%M") as month, 
                                 YEAR(i.tgl_invoice) as year, 
                                 COUNT(i.invoice) as invoice_count, 
                                 SUM(t.harga_beli) as total_harga_beli, 
                                 SUM(t.harga_jual) as total_harga_jual')
    ->from('invoice as i')
    ->join('transaksi as t', 'i.id', '=', 't.id')
    ->groupBy('month', 'year') // Tambahkan 'year' di sini
    ->orderByRaw('MONTH(i.tgl_invoice)') // Urutkan berdasarkan bulan
    ->get();

$invoiceData = $invoices->map(function($invoice) {
    $total_profit = $invoice->total_harga_jual - $invoice->total_harga_beli; // Hitung total profit
    return [
        'month' => $invoice->month,
        'year' => $invoice->year, // Tambahkan tahun
        'invoice_count' => $invoice->invoice_count,
        'total_harga_beli' => $invoice->total_harga_beli,
        'total_harga_jual' => $invoice->total_harga_jual,
        'total_profit' => $total_profit, // Tambahkan total profit
    ];
});


    // Menghitung total invoice dari hasil yang dikelompokkan
    $totalHargaBeli = $invoiceData->sum('total_harga_beli');
    $totalHargaJual = $invoiceData->sum('total_harga_jual');
    $totalInvoices = $invoiceData->sum('invoice_count');
    $totalProfit = $totalHargaJual - $totalHargaBeli;

    return response()->json([
        'data' => $invoiceData,
        'total_hargabeli' => $totalHargaBeli,
        'totalhargajual' => $totalHargaJual,
        'totalProfit' => $totalProfit,
        'total_invoices' => $totalInvoices,
    ]);
}


    public function dataTableOmzet()
    {

        $query = Invoice::get();
        $data = OmzetResurce::collection($query);
        $res = $data->toArray(request());
        // dd($res);
        return DataTables::of($res)
            ->addIndexColumn()
            ->toJson();
    }

    public function OmzetExportExcel(Request $request)
    {
        if ($request->start == null || $request->end == null) {
            return back()->with('error', 'Silahkan atur nilai rentang data.');
        }
        return Excel::download(new OmzetExport($request->start, $request->end), 'laporan-omzet.xlsx');
    }
}
