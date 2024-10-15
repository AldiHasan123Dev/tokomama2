<?php

namespace App\Http\Controllers;
use App\Models\Coa;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class LabaRugi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;
    
        // Get the month and year from the request, or use the current month and year as defaults
        $bulan = $request->input('bulan', $currentMonth);
        $tahun = $request->input('tahun', $currentYear);
    
        $coa1 = Coa::where('tabel', 'A')->get();
        $coa2 = Coa::where('tabel', 'B')->get();
        $coa3 = Coa::where('tabel', 'C')->get();
        $coa4 = Coa::where('tabel', 'D')->get();
        $coa5 = Coa::where('tabel', 'E')->get();
        $coa6 = Coa::where('tabel', 'F')->get();
        $coa7 = Coa::where('tabel', 'G')->get();
    
        $totals = [];
        $coaId1 = $coa1->pluck('id')->toArray();
        $coaId2 = $coa2->pluck('id')->toArray();
        $coaId3 = $coa3->pluck('id')->toArray();
        $coaId4 = $coa4->pluck('id')->toArray();
        $coaId5 = $coa5->pluck('id')->toArray();
        $coaId6 = $coa6->pluck('id')->toArray();
        $coaId7 = $coa7->pluck('id')->toArray();
        $allCoaIds = array_merge($coaId1, $coaId2, $coaId3, $coaId4, $coaId5, $coaId6, $coaId7);
    
        foreach ($allCoaIds as $coaId) {
            // Filter based on month and year
            $debit = Jurnal::where('coa_id', $coaId)
                ->whereMonth('tgl', $bulan)
                ->whereYear('tgl', $tahun)
                ->sum('debit');
    
            $kredit = Jurnal::where('coa_id', $coaId)
                ->whereMonth('tgl', $bulan)
                ->whereYear('tgl', $tahun)
                ->sum('kredit');
    
            $totals[$coaId] = [
                'debit' => $debit,
                'kredit' => $kredit,
                'selisih' => $debit - $kredit,
                'pendapatan' => $kredit - $debit
            ];
        }
    
        // Calculate totals based on filtered COAs
        $totalA = array_sum(array_column(array_intersect_key($totals, array_flip($coaId1)), 'pendapatan'));
        $totalB = array_sum(array_column(array_intersect_key($totals, array_flip($coaId2)), 'selisih'));
        $totalC = array_sum(array_column(array_intersect_key($totals, array_flip($coaId3)), 'selisih'));
        $totalD = array_sum(array_column(array_intersect_key($totals, array_flip($coaId4)), 'selisih'));
        $totalE = array_sum(array_column(array_intersect_key($totals, array_flip($coaId5)), 'selisih'));
        $totalF = array_sum(array_column(array_intersect_key($totals, array_flip($coaId6)), 'selisih'));
        $totalG = array_sum(array_column(array_intersect_key($totals, array_flip($coaId7)), 'selisih'));
    
        // Return view with necessary data
        return view('jurnal.jurnal-lr', compact(
            'coa1', 'coa2', 'coa3', 'coa4', 'coa5', 'coa6', 'coa7',
            'totals', 'totalA', 'totalB', 'totalC', 'totalD', 'totalE', 'totalF', 'totalG',
            'bulan', 'tahun' // Pass bulan and tahun to the view
        ));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
