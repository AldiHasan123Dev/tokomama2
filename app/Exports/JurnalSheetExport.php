<?php

namespace App\Exports;

use App\Models\Jurnal;
use App\Models\Coa;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class JurnalSheetExport implements FromView, WithTitle
{
    private $coa;

    public function __construct($coa)
    {
        $this->coa = $coa; // Langsung menerima model COA
    }

    public function view(): View
    {
        // Menyusun nama bulan untuk tampilan
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Ambil parameter bulan dan tahun dari request
        $month = request('month', null); // Jika tidak ada bulan, gunakan null
        $year = request('year', date('Y'));   // Default ke tahun sekarang jika tidak ada input

        // Ambil data jurnal berdasarkan filter tahun, bulan, dan COA
        $data = Jurnal::whereYear('tgl', $year) // Filter berdasarkan tahun
            ->when($month, function ($query, $month) { // Menambahkan filter bulan hanya jika ada parameter month
                return $query->whereMonth('tgl', $month);
            })
            ->where('coa_id', $this->coa->id)
            ->orderBy('tgl', 'asc')
            ->orderByRaw("FIELD(tipe, 'BBM', 'BBK', 'BKM', 'BKK', 'BBMO', 'BBKO', 'JNL') ASC")
            ->orderBy('created_at', 'asc')
            ->orderBy('no', 'asc')
            ->get();

        // Tentukan tipe berdasarkan kode akun
        $tipe = 'D';
        if (in_array(substr($this->coa->no_akun, 0, 1), ['2', '3', '5'])) {
            $tipe = 'C';
        }

        // Menghitung saldo untuk setiap bulan
        $saldo = [];
        foreach ($months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon("$year-" . sprintf('%02d', $bln) . "-01");
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');

            // Saldo awal dihitung pada bulan pertama
            if ($idx == 0) {
                // Hitung saldo awal berdasarkan tipe (Debit/Kredit)
                $saldo_awal = Jurnal::where('coa_id', $this->coa->id)
                    ->where('tgl', '<', $now)
                    ->sum($tipe == 'D' ? 'debit' : 'kredit') -
                    Jurnal::where('coa_id', $this->coa->id)
                        ->where('tgl', '<', $now)
                        ->sum($tipe == 'D' ? 'kredit' : 'debit');
            } else {
                $saldo_awal = $saldo['saldo_akhir'][$idx - 1]; // Ambil saldo akhir bulan sebelumnya
            }

            // Debit dan Kredit selama bulan berjalan
            $debit = Jurnal::where('coa_id', $this->coa->id)
                ->whereBetween('tgl', [$now, $last])
                ->sum('debit');
            $credit = Jurnal::where('coa_id', $this->coa->id)
                ->whereBetween('tgl', [$now, $last])
                ->sum('kredit');

            // Update saldo bulanan
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            $saldo['debit'][$idx] = $debit;
            $saldo['kredit'][$idx] = $credit;

            // Saldo akhir per bulan
            if ($tipe == 'D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit;
            }
        }

        // Ambil saldo awal untuk bulan yang dipilih, jika tidak ada bulan, ambil saldo awal untuk bulan pertama
        $saldo_awal = $month ? $saldo['saldo_awal'][$month - 1] : $saldo['saldo_awal'][0];

        // Kirim data ke view untuk di-render
        return view('jurnal.buku-besar-export', [
            'coa' => $this->coa,
            'months' => $months,
            'saldo' => $saldo,
            'saldo_awal' => $saldo_awal,
            'data' => $data,
            'tipe' => $tipe,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function title(): string
    {
        // Pastikan nama sheet tidak lebih dari 31 karakter
        $title = $this->coa ? $this->coa->no_akun : 'Unknown COA';
        return substr($title, 0, 31);
    }
}
