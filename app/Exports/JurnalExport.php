<?php

namespace App\Exports;

use App\Models\Coa;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JurnalExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // Ambil request COA jika ada
        $coaId = request('coa', null);

        if ($coaId) {
            // Jika ada request COA, ekspor hanya COA tersebut
            $coa = Coa::find($coaId);
            if ($coa) {
                $sheets[] = new JurnalSheetExport($coa);
            }
        } else {
            // Jika tidak ada request COA, ekspor semua COA aktif
            $coaList = Coa::where('status', 'aktif')->get();
            foreach ($coaList as $coa) {
                $sheets[] = new JurnalSheetExport($coa);
            }
        }

        return $sheets;
    }
}
