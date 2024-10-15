<?php

namespace App\Exports;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Nopol;
use App\Models\TemplateJurnal;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JurnalExport implements FromView
{
    public function view(): View
    {
        $templates = TemplateJurnal::all();
        $nopol = Nopol::where('status', 'aktif')->get();
        $coa = Coa::where('status', 'aktif')->get();
        $coa_id = 1;
        if (isset($_GET['coa'])) {
            $coa_id = $_GET['coa'];
        }
        $coa_find = Coa::find($coa_id);

        $coa_by_id = Coa::where('id', $coa_id)->first();

//        dd($this->saldo());

        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        if (isset($_GET['coa'])) {
            $data = Jurnal::whereMonth('jurnal.created_at',$month)->whereYear('jurnal.created_at',$year)->where('jurnal.coa_id',$coa_id)->orderBy('created_at','asc')->orderBy('no', 'asc')->orderBy('tipe', 'asc')->get();
        } else {
            $data = Jurnal::orderBy('created_at','asc')->orderBy('no', 'asc')->orderBy('tipe', 'asc')->get();
        }

        $tipe = 'D';
        if(substr($coa_find->no_akun,0,1)=='2'||substr($coa_find->no_akun,0,1)=='3'||substr($coa_find->no_akun,0,1)=='5'){
            $tipe = 'C';
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
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($tipe=='D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal ) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit ;
            }
            $saldo['debit'][$idx] = $debit;
            $saldo['kredit'][$idx] = $credit;
        }

        $m = (int)$month;
        $saldo_awal = $saldo['saldo_awal'][$m-1];

        return view('jurnal.buku-besar-export', compact('templates','nopol', 'coa', 'months', 'saldo','saldo_awal', 'coa', 'coa_id', 'year', 'coa_by_id', 'data', 'tipe', 'month'));
    }
}
