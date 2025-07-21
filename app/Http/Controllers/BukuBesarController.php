<?php

namespace App\Http\Controllers;

use App\Exports\JurnalExport;
use App\Models\BukuBesar;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\TemplateJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = TemplateJurnal::all();
        $nopol = Nopol::where('status', 'aktif')->get();
        $coa = Coa::where('status', 'aktif')->get();
        $coa_id = null;
        if (isset($_GET['coa'])) {
            $coa_id = $_GET['coa'];
        }
        $coa_find = Coa::find($coa_id);

        $coa_by_id = $coa_id;

//        dd($this->saldo());

        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        if ($coa_id) {
            // Jika coa_id ada
            if ($coa_id == null) {
                // Jika coa_id adalah 1, query tanpa filter coa_id
                $data = Jurnal::whereMonth('jurnal.tgl', $month)
                    ->whereYear('jurnal.tgl', $year)
                    ->orderBy('tgl', 'asc')
                    ->orderByRaw("FIELD(tipe, 'BBM', 'BBK', 'BKM', 'BKK', 'BBMO', 'BBKO', 'JNL', 'BBMN', 'BBKN') ASC")
                    ->orderBy('created_at', 'asc')
                    ->orderBy('no', 'asc')
                    ->get();
            } else {
                // Jika coa_id tidak sama dengan 1, filter berdasarkan coa_id
                $data = Jurnal::whereMonth('jurnal.tgl', $month)
                    ->whereYear('jurnal.tgl', $year)
                    ->where('jurnal.coa_id', $coa_id)
                    ->orderBy('tgl', 'asc')
                    ->orderByRaw("FIELD(tipe, 'BBM', 'BBK', 'BKM', 'BKK', 'BBMO', 'BBKO', 'JNL', 'BBMN', 'BBKN') ASC")
                    ->orderBy('created_at', 'asc')
                    ->orderBy('no', 'asc')
                    ->get();
            }
        } else {
            // Jika coa_id tidak ada (null), query tanpa filter coa_id
            $data = Jurnal::whereMonth('jurnal.tgl', $month)
                ->whereYear('jurnal.tgl', $year)
                ->orderBy('tgl', 'asc')
                ->orderByRaw("FIELD(tipe, 'BBM', 'BBK', 'BKM', 'BKK', 'BBMO', 'BBKO', 'JNL','BBMN', 'BBKN') ASC")
                ->orderBy('created_at', 'asc')
                ->orderBy('no', 'asc')
                ->get();
        }
        
        

        $tipe = 'D';

// Cek jika $coa_find atau $coa_find->no_akun adalah null
if ($coa_find && $coa_find->no_akun) {
    if (substr($coa_find->no_akun, 0, 1) == '2' || substr($coa_find->no_akun, 0, 1) == '3' || substr($coa_find->no_akun, 0, 1) == '5') {
        $tipe = 'C';
    }
} else {
    // Jika $coa_find atau $coa_find->no_akun null, tentukan tipe default lainnya, jika perlu
    // Misalnya, set tipe ke 'D' jika diperlukan
    $tipe = 'D'; // atau sesuaikan dengan tipe lain jika ada kebutuhan
}


        $saldo = array();

        foreach ($months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($year.'-'.sprintf('%02d',$bln).'-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            // $start = '2022-12-01';
            $des = $c->endOfMonth()->format('Y-m-d');
            // dd($start,$des,$last);
            if($idx==0){
                if ($tipe=='D') {
                    $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',['2023-12-01',$des])->sum('debit') - Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',['2023-12-01',$des])->sum('kredit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',['2023-12-01',$last])->sum('kredit') - Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',['2023-12-01',$last])->sum('debit');
                }
            } else {
                $start = $now;
                $saldo_awal =  $saldo['saldo_akhir'][$idx-1];
            }
            $debit = Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',[$now,$last])->sum('debit');
            $credit = Jurnal::where('coa_id',$coa_id)->whereBetween('tgl',[$now,$last])->sum('kredit');
            $isBiayaPendapatan = isset($coa_find) && in_array(substr($coa_find->no_akun, 0, 1), ['5', '6']);
            if ($isBiayaPendapatan) {
                $saldo_awal = 0;
            }
            if ($tipe=='D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal ) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit ;
            }
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            $saldo['debit'][$idx] = $debit;
            $saldo['kredit'][$idx] = $credit;
        }

        $m = (int)$month;
        $saldo_awal = $saldo['saldo_awal'][$m-1];

        return view('jurnal.buku-besar', compact('templates','nopol', 'coa', 'months', 'saldo','saldo_awal', 'coa', 'coa_id', 'year', 'coa_by_id', 'data', 'tipe'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BukuBesar $bukuBesar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BukuBesar $bukuBesar)
    {
        //
    }

    public function export()
    {
        // Menambahkan SweetAlert session untuk notifikasi
        session()->flash('export_success', 'Proses ekspor berhasil, file buku besar sedang diunduh.');
    
        return Excel::download(new JurnalExport, 'buku_besar.xlsx');
    }
    
}
