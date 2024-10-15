<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.satuan');
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
        $data = Satuan::create($request->all());

        if ($data) {
            return redirect()->route('satuan.index', $data)->with('success', 'Data Master Satuan berhasil ditambahkan!');
        } else {
            return redirect()->route('satuan.index', $data)->with('error', 'Data Master Satuan gagal ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Satuan $satuan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satuan $satuan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $satuan)
    {
        $data = $request->all();
        $satuan->update($data);

        if ($satuan->update($data)) {
            return redirect()->route('satuan.index')->with('success', 'Data Master Satuan berhasil diubah!');
        } else {
            return redirect()->route('satuan.index')->with('error', 'Data Master Satuan gagal diubah!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satuan $satuan)
    {
        $satuan->delete();
        return route('ekspedisi.index');
    }

    public function dataTable() {
        $data = Satuan::query();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<div class="flex gap-3 mt-2">
                            <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama_satuan) . '\')" id="delete-faktur-all" class="text-yellow-300 font-semibold mb-3 self-end" ><i class="fa-solid fa-pencil"></i></button> |
                            <button onclick="deleteData(' . $row->id . ')"  id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make();
    }
}
