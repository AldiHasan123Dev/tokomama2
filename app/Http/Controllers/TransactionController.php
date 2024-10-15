<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Transaction $transaction)
    {
        return response($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $transaksi = Transaction::find($request->id);
        $transaksi->update($request->all());
        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function dataTable()
    {
        $query = Transaction::query();
        $query->with(['barang', 'suratJalan']);
        if(request('tarif')){
            $query->where('harga_jual','>',0);
            $query->where('harga_beli','>',0);
        }
        if(request('non_tarif')){
            $query->where('harga_jual',0);
            $query->orWhere('harga_beli',0);
        }

        return DataTables::of($query->get())
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                if (Invoice::where('id_transaksi', $row->id)->get() == "[]") {
                    return '<button type="button" class="modal-toggle w-20 btn text-semibold text-white bg-green-700" onclick="inputTarif(' . $row->id . ',' . $row->harga_jual . ',' . $row->harga_beli . ',' . $row->margin . ',' . $row->jumlah_jual . ', \'' . addslashes($row->barang->nama) . '\', \'' . addslashes($row->satuan_jual) . '\')">Edit Harga</button>';
                } else {
                    return "-";
                }
            })
            ->addColumn('barang', function ($row) {
                return $row->barang->nama;
            })
            ->addColumn('profit', function ($row) {
                return $row->margin == 0 ? '-' : number_format($row->margin);
            })
            ->addColumn('harga_jual', function ($row) {
                return $row->harga_jual == 0 ? '-' : number_format($row->harga_jual);
            })
            ->addColumn('harga_beli', function ($row) {
                return $row->harga_beli == 0 ? '-' : number_format($row->harga_beli);
            })
            ->addColumn('nomor_surat', function ($row) {
                return $row->suratJalan->nomor_surat;
            })
            ->addColumn('nama_kapal', function ($row) {
                return $row->suratJalan->nama_kapal;
            })
            ->addColumn('no_cont', function ($row) {
                return $row->suratJalan->no_cont;
            })
            ->addColumn('no_seal', function ($row) {
                return $row->suratJalan->no_seal;
            })
            ->addColumn('no_pol', function ($row) {
                return $row->suratJalan->no_pol;
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }
}
