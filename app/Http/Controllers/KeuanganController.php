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
        dd($request->all());
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
        // Ambil data dengan eager loading dan urutkan berdasarkan 'created_at' DESC
        $query = Invoice::with(['nsfp', 'transaksi'])
            ->orderBy('created_at', 'desc'); // Menentukan urutan descending
    
        // Tambahkan logika pencarian jika _search adalah true
        if ($request->_search === 'true') {
            // Ambil parameter pencarian
            $searchField = $request['searchField'];
            $searchString = $request['searchString'];
    
            if ($searchField && $searchString) {
                $query->where(function ($q) use ($searchField, $searchString) {
                    // Melakukan pencarian berdasarkan kolom yang relevan
                    if ($searchField === 'invoice') {
                        $q->where('invoice', 'LIKE', "%{$searchString}%");
                    } elseif ($searchField === 'nsfp') {
                        // Jika 'nsfp' adalah relasi, gunakan join atau whereHas
                        $q->whereHas('nsfp', function($query) use ($searchString) {
                            $query->where('nomor', 'LIKE', "%{$searchString}%");
                        });
                    }
                    // Tambahkan kondisi pencarian lainnya jika diperlukan
                });
            }
        }
    
        // Menghitung total record
        $totalRecords = $query->count();
    
        // Ambil data dengan pagination
        $data = $query->skip(($request->page - 1) * $request->rows)
                      ->take($request->rows)
                      ->get();
    
        // Membuat array untuk data yang akan dikembalikan
        $result = [];
        $index = ($request->page - 1) * $request->rows;
        foreach ($data as $row) {
            $index++;
            $result[] = [
                'DT_RowIndex' => $index, // atau sesuai dengan ID unik Anda
                'nsfp' => $row->nsfp->nomor ?? '-',
                'invoice' => $row->invoice ?? '-',
                'subtotal' => number_format($row->subtotal, 0, ',', '.'),
                'ppn' => $this->calculatePPN($row),
                'total' => $this->calculateTotal($row),
            ];
        }
    
        // Mengembalikan data dalam format yang sesuai untuk jqGrid
        return response()->json([
            'current_page' => $request->page,
            'last_page' => ceil($totalRecords / $request->rows),
            'total' => $totalRecords,
            'data' => $result,
        ]);
    }
    
    // Fungsi untuk menghitung PPN
    private function calculatePPN($row)
    {
        $barang = $row->transaksi->barang ?? null;
        if ($barang && $barang->status_ppn === 'ya') {
            return number_format($row->subtotal * ($barang->value_ppn / 100), 0, ',', '.');
        } else {
            return 0;
        }
    }
    
    // Fungsi untuk menghitung Total
    private function calculateTotal($row)
    {
        $subtotal = $row->subtotal;
        $barang = $row->transaksi->barang ?? null;
    
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
