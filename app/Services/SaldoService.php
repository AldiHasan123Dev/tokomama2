<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\Coa;
use Carbon\Carbon;

class SaldoService
{
    public function getSaldoAwal($month, $year, $coa_id)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $tipe = 'D';
        $coa = Coa::find($coa_id);
        if (substr($coa->no_akun, 0, 1) == '2' || substr($coa->no_akun, 0, 1) == '3' || substr($coa->no_akun, 0, 1) == '5') {
            $tipe = 'C';
        }

        $saldo = [
            'saldo_awal' => [],
            'debit' => [],
            'kredit' => [],
            'saldo_akhir' => []
        ];

        foreach ($months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($year . '-' . sprintf('%02d', $bln) . '-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            $des = $c->endOfMonth()->format('Y-m-d');

            if ($idx == 0) {
                if ($tipe == 'D') {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', ['2023-12-01', $des])->sum('debit') - Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', ['2023-05-01', $des])->sum('kredit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', ['2023-12-01', $last])->sum('kredit') - Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', ['2023-05-01', $last])->sum('debit');
                }
            } else {
                $start = $now;
                $saldo_awal = $saldo['saldo_akhir'][$idx - 1];
            }
            $debit = Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', [$now, $last])->sum('debit');
            $credit = Jurnal::where('coa_id', $coa_id)->whereBetween('tgl', [$now, $last])->sum('kredit');
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($tipe == 'D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit;
            }
            $saldo['debit'][$idx] = $debit;
            $saldo['kredit'][$idx] = $credit;
        }

        return $saldo;
    }
}
