<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPpnExport;
use App\Exports\LaporanPpnExportExcel;
use App\Http\Resources\SuratJalanResource;
use App\Models\Invoice;
use App\Models\NSFP;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Svg\Tag\Rect;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class PajakController extends Controller
{
    public function index()
    {
        $data = DB::table('nsfp')->get();

        return view('pajak.nsfp',  compact('data'));
    }

    public function lapPpn()
    {
        return view('pajak.laporan-ppn');
    }

    public function nsfpAvailable()
    {
        $data = DB::table('nsfp')->get();
        return $data;
    }

    public function PPNExportExcel(Request $request)
    {
        // dd($request->start);
        return Excel::download(new LaporanPpnExportExcel($request->start, $request->end), 'laporan-ppn.xlsx');
    }

    public function PPNExportCsv(Request $request)
    {
        // dd($request->start);
        return Excel::download(new LaporanPpnExport($request->start, $request->end), 'laporan-ppn.csv');
    }

    public function datatable(Request $request)
    {

        // dd($request);
        // dd($suratJalan);
        /**
         * Data yang dibutuhkan
         * Surat Jalan
         * Customer customer diambil dari relasi surat jalan
         * Nsfp 
         * 
         * kemudian di passing ke databable
         */

        $data1 = Invoice::get();
        $data = SuratJalanResource::collection($data1);
        $res = $data->toArray(request());
        return DataTables::of($res)
            ->addIndexColumn()
            ->toJson();
    }
}
