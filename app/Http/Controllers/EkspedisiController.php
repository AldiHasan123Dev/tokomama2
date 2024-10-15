<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EkspedisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.ekspedisi');
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
        $data = $request->all();
        $ekspedisi = Ekspedisi::create($data);

        if ($ekspedisi) {
            return redirect()->route('ekspedisi.index')->with('success', 'Data Master Ekspedisi berhasil ditambahkan!');
        } else {
            return redirect()->route('ekspedisi.index')->with('error', 'Data Master Ekspedisi gagal ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ekspedisi $ekspedisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ekspedisi $ekspedisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ekspedisi $ekspedisi)
    {
        $data = $request->all();
        $ekspedisi->update($data);

        if ($ekspedisi->update($data)) {
            return redirect()->route('ekspedisi.index')->with('success', 'Data Master Ekspedisi berhasil diubah!');
        } else {
            return redirect()->route('ekspedisi.index')->with('error', 'Data Master Ekspedisi gagal diubah!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ekspedisi $ekspedisi)
    {
        $ekspedisi->delete();
        return redirect()->route('ekspedisi.index');
    }

    public function dataTable()
    {
        $data = Ekspedisi::query()->orderBy('id', 'desc');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<div class="flex gap-3 mt-2">
                            <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama) . '\', \'' . addslashes($row->email) . '\', \'' . addslashes($row->no_telp) . '\', \'' . addslashes($row->alamat) . '\', \'' . addslashes($row->kota) . '\', \'' . addslashes($row->pic) . '\', \'' . addslashes($row->fax) . '\')" id="delete-faktur-all" class="text-yellow-300 font-semibold mb-3 self-end" ><i class="fa-solid fa-pencil"></i></button>
                            <button onclick="deleteData(' . $row->id . ')"  id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make();
    }
}
