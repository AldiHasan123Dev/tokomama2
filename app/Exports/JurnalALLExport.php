<?php

namespace App\Exports;

use App\Models\Jurnal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class JurnalALLExport implements FromView
{
    protected $mulai;
    protected $sampai;

    public function __construct($mulai, $sampai)
    {
        $this->mulai = $mulai;
        $this->sampai = $sampai;
    }

    public function view(): View
    {
        // Menggunakan Carbon untuk mengatur waktu mulai dan sampai
        $mulai = Carbon::parse($this->mulai)->toImmutable(); // Tanggal tidak bisa diubah lagi
        $sampai = Carbon::parse($this->sampai)->toImmutable(); // Tanggal tidak bisa diubah lagi
        


        // Ambil data jurnal berdasarkan rentang tanggal
        $jurnal = Jurnal::join('coa', 'jurnal.coa_id', '=', 'coa.id')
            ->whereBetween('jurnal.tgl', [$mulai, $sampai]) // Rentang tanggal
            ->select('jurnal.*', 'coa.no_akun', 'coa.nama_akun')
            ->orderBy('jurnal.tgl')
            ->get();
            // dd($jurnal,$mulai,$sampai);

            return view('export.laporan-jurnal', [
                'jurnal' => $jurnal,
                'mulai' => $this->mulai,
                'sampai' => $this->sampai,
            ]);
    }
}
