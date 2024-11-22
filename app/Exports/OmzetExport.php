<?php

namespace App\Exports;

use App\Http\Resources\OmzetResurce;
use App\Models\Invoice;
use App\Models\SuratJalan;
use App\Models\Jurnal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OmzetExport implements FromView
{
    private $start;
    private $end;
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        $omzet = Invoice::with('NSFP')  // Menambahkan eager loading untuk NSFP
        ->join('jurnal', 'invoice.id_transaksi', '=', 'jurnal.id_transaksi')  // Join antara tabel invoice dan jurnal berdasarkan id_transaksi
        ->whereBetween('tgl_invoice', [$this->start, $this->end])  // Memilih kolom yang dibutuhkan dari kedua tabel
        ->get();
        $invoice = $omzet->pluck('invoice');
        $jurnal = Jurnal::whereIn('invoice', $invoice)->orderBy('tgl')->get();
        return view('export.laporan-omzet', compact('omzet'));
        // return SuratJalan::all();
    }
}
