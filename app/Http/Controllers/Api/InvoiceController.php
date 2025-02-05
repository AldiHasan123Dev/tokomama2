<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\NSFP;
use App\Models\SuratJalan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class InvoiceController extends Controller
{
    public function dataTable()
    {
        // $data = SuratJalan::query()->whereNull('invoice');
        $query = Transaction::query()
        ->leftJoin('invoice', 'transaksi.id', '=', 'invoice.id_transaksi') 
        ->whereNull('invoice.id_transaksi')
        ->whereNotNull('id_surat_jalan')
        ->where('harga_beli', '>', 0)
        ->where('sisa', '>',0)
        ->orderBy('transaksi.created_at', 'desc')
        ->select([
            'transaksi.id', // Gunakan alias agar tidak tertimpa oleh invoice.id
            'transaksi.*' // Pilih semua kolom dari transaksi
        ])
        ->get();
    
    $data = TransactionResource::collection($query);
    $res = $data->toArray(request());
    

        $data = TransactionResource::collection($query);
        $res =  $data->toArray(request());


        return DataTables::of($res)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" name="id_transaksi[]" id="id" value="' . $row['id'] . '">';
            })
            ->addColumn('aksi', function ($row) {
                return '<form method=' . 'GET' . ' action = ""><button class="btn btn-xs btn-success" type=submit>Ambil</button></form>';
            })
            ->rawColumns(['aksi','checkbox'])
            ->make(true);
    }

    public function ambil(Request $request)
    {

        $suratJalan = SuratJalan::find($request->id);
        // $suratJalan->status = 'tarik';

        // // mengubah nomor surat jalan menjadi nomor invoice
        // $nomor = str_replace(' ', '', $suratJalan->nomor_surat);
        // $noExplode = explode('/', $nomor);
        // $one = $noExplode[0];
        // $two = str_replace('SJ', 'INV', $noExplode[1]);
        // $three = str_replace('-', '/', $noExplode[2]);
        // $four = $noExplode[3];
        // $nomorInvoice = $one . '/' . $two . '/' . $three . '/' . $four;
        // $suratJalan->invoice = $nomorInvoice;

        // //tanggal current
        // $suratJalan->tgl_invoice = Carbon::now();

        // // update table nsfp
        // $nsfp = NSFP::where('available', '1')->first();
        // $nsfp->invoice = $nomorInvoice;
        // $nsfp->available = 0;
        // // dd($nsfp);

        // // save
        // $suratJalan->save();
        // $nsfp->save();

        return redirect()->route('keuangan.invoice');
    }
}
