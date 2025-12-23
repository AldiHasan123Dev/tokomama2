<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AlatBeratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('masters.alat_berat');
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
        $data = AlatBerat::create($request->all());
        if ($data) {
            return redirect()->route('master.alat_berat', $data)->with('success', 'Data Master Alat Berat berhasil ditambahkan!');
        } else {
            return redirect()->route('master.alat_berat', $data)->with('error', "Data Master Alat Berat gagal ditambahkan!");
        }
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
    public function update(Request $request)
{
    $data = AlatBerat::find($request->id);
    $data->nama_alat = $request->nama_alat;

    if ($data->save()) {
        return redirect()->route('master.alat_berat')
            ->with('success', 'Data Master Alat Berat berhasil diubah!');
    } else {
        return redirect()->route('master.alat_berat')
            ->with('error', 'Data Master Alat Berat gagal diubah!');
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        AlatBerat::destroy(request('id'));
        return route('master.alat_berat');
    }

     public function datatable()
{
    $data = AlatBerat::orderBy('id', 'desc');

    return DataTables::of($data)
        ->addIndexColumn()

        // tampilkan nama satuan dari relasi
        ->addColumn('aksi', function ($row) {
            return '
                <div class="flex gap-3 mt-2">
                    <button 
                        onclick="getData('.$row->id.', \''.addslashes($row->nama_alat).'\')" 
                        class="text-yellow-400 font-semibold"
                    >
                        <i class="fa-solid fa-pencil"></i>
                    </button>

                    <button 
                        onclick="deleteData('.$row->id.')" 
                        class="text-red-600 font-semibold"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
}

}
