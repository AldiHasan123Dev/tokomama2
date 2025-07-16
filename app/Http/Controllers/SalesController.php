<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;
use Yajra\DataTables\DataTables;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.sales');
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
        $data = Sales::create($request->all());

        if ($data) {
            return redirect()->route('master.sales', $data)->with('success', 'Data Master Sales berhasil ditambahkan!');
        } else {
            return redirect()->route('master.sales', $data)->with('error', 'Data Master Sales gagal ditambahkan!');
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
  public function update(Request $request, Sales $sales)
{
    $data = Sales::find($request->id);
    $data->nama = $request->nama;

    if ($data->save()) {
        // Setelah nama berhasil diubah, update ke tabel customer yang terkait
        $data->customer()->update(['sales' => $data->nama]);

        return redirect()->route('master.sales')->with('success', 'Data Master Sales berhasil diubah!');
    } else {
        return redirect()->route('master.sales')->with('error', 'Data Master Sales gagal diubah!');
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function datatable()
{
    $data = Sales::query()->orderBy('id', 'desc');

  return DataTables::of($data)
    ->addIndexColumn()
        ->addColumn('aksi', function ($row) {
            return '<div class="flex gap-3 mt-2">
                <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama) . '\')" class="text-yellow-500 font-semibold">
                    <i class="fa-solid fa-pencil"></i>
                </button>
                <button onclick="deleteData(' . $row->id . ')" class="text-red-600 font-semibold">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>';
        })
        ->rawColumns(['aksi', 'status']) // penting agar HTML tidak di-escape
        ->make();
}

}
