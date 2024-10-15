<?php

namespace App\Exports;
use App\Models\Coa;
use App\Models\Jurnal;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NcsExport implements FromArray, WithHeadings
{
    protected $exportData;

    public function __construct(array $exportData)
    {
        $this->exportData = $exportData;
    }

    public function array(): array
    {
        return $this->exportData;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Nomor',
            'Keterangan',
            'Debit',
            'Kredit',
            'Saldo',
            'Tanggal',
            'Nomor',
            'Keterangan'
        ];
    }

   
}
