<?php
namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('jurnal.coa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Coa::create($request->all());

        if ($data) {
            return redirect()->route('jurnal.coa')->with('success', 'Data COA berhasil ditambahkan!');
        } else {
            return redirect()->route('jurnal.coa')->with('error', 'Data COA gagal ditambahkan!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coa $coa)
    {
        $data = $request->all();
        $coa->update($data);

        if ($coa->update($data)) {
            return redirect()->route('jurnal.coa')->with('success', 'Data COA berhasil diubah!');
        } else {
            return redirect()->route('jurnal.coa')->with('error', 'Data COA gagal diubah!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coa $coa)
    {
        $coa->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Fetch data for DataTables.
     */
    public function dataTable()
    {
        $data = Coa::query();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<div class="flex gap-3 mt-2">
                            <button onclick="getData(' . $row->id . ', \'' . addslashes($row->no_akun) . '\', \'' . addslashes($row->nama_akun) . '\', \'' . addslashes($row->status) . '\')" class="text-yellow-300 font-semibold mb-3 self-end"><i class="fa-solid fa-pencil"></i></button> |
                            <button onclick="deleteData(' . $row->id . ')" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make();
    }
}
