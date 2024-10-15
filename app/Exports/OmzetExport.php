<?php

namespace App\Exports;

use App\Http\Resources\OmzetResurce;
use App\Models\Invoice;
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
        $omzet = Invoice::whereBetween('tgl_invoice', [$this->start, $this->end])->get();
        // dd($omzet[0]);
        // $surat_jalan = SuratJalan::whereBetween('tgl_invoice', [$this->start, $this->end])->orderBy('tgl_invoice')->get();
        // dd($surat_jalan);
        return view('export.laporan-omzet', compact('omzet'));
        // return SuratJalan::all();
    }
}
