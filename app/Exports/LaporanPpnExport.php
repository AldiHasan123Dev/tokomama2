<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class LaporanPpnExport implements FromView, WithCustomCsvSettings
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
        $invoices = Invoice::whereBetween("tgl_invoice", [$this->start, $this->end])->groupBy('invoice')->get();
        // $invoices_of = Invoice::whereBetween("tgl_invoice", [$this->start, $this->end])->groupBy('invoice')->groupBy('id_transaksi')->get();
        // dd($invoices[0]->transaksi->jumlah_jual);
        return view('export.laporan-ppn', compact('invoices'));
        // return SuratJalan::all();
    }

    public function getCsvSettings(): array {
        return [
            'delimiter' => ',',
            'enclosure' => '',
            'escape' => '\\',
        ];
    }
}
