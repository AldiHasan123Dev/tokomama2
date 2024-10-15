<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanPpnExportExcel implements FromView
{
    private $start;
    private $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $invoices = Invoice::whereBetween("tgl_invoice", [$this->start, $this->end])->get();
        return view('export.laporan-ppn-excel', compact('invoices'));
    }
}
