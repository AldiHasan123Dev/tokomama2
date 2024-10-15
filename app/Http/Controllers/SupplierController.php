<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.supplier');
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
        $data = Supplier::create($request->all());

        if ($data) {
            return redirect()->route('master.supplier', $data)->with('success', 'Data Master Supplier berhasil ditambahkan!');
        } else {
            return redirect()->route('master.supplier', $data)->with('error', 'Data Master Supplier gagal ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = Supplier::find($request->id);
        $data->nama = $request->nama;
        $data->npwp = $request->npwp;
        $data->email = $request->email;
        $data->no_telp = $request->no_telp;
        $data->alamat = $request->alamat;
        $data->kota = $request->kota;
        $data->alamat_npwp = $request->alamat_npwp;

        if ($data->save()) {
            return redirect()->route('master.supplier', $data)->with('success', 'Data Master Supplier berhasil diubah!');
        } else {
            return redirect()->route('master.supplier', $data)->with('error', 'Data Master Supplier berhasil diubah!');
        }

        return redirect()->route('master.supplier');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $data = Supplier::destroy(request('id'));

        if ($data) {
            return redirect()->route('master.supplier')->with('success', 'Data Master Supplier berhasil dihapus!');
        } else {
            return redirect()->route('master.supplier')->with('error', 'Data Master Supplier gagal dihapus!');
        }
    }

    public function datatable()
    {

        $data = Supplier::query()->orderBy('id', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<div class="flex gap-3 mt-2">
            <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama) . '\', \'' . addslashes($row->npwp) . '\', \'' . addslashes($row->nama_npwp) . '\', \'' . addslashes($row->email) . '\', \'' . addslashes($row->no_telp) . '\', \'' . addslashes($row->alamat) . '\', \'' . addslashes($row->alamat_npwp) . '\', \'' . addslashes($row->kota) . '\')" id="delete-faktur-all" class="text-yellow-300 font-semibold mb-3 self-end" ><i class="fa-solid fa-pencil"></i></button> |
            <button onclick="deleteData(' . $row->id . ')" id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end" ><i class="fa-solid fa-trash"></i></button>
            </div>';
            })
            ->rawColumns(['aksi'])
            ->make();
    }
}
