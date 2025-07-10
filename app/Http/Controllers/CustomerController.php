<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.customer');
    }

        public function blokir_cust()
    {
        return view('masters.blokir_cust');
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
        $data = Customer::create($request->all());

        if ($data) {
            return redirect()->route('master.customer', $data)->with('success', 'Data Master Customer berhasil ditambahkan!');
        } else {
            return redirect()->route('master.customer', $data)->with('error', 'Data Master Customer gagal ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    public function blokir(Request $request)
{
    if ($request->ajax() && $request->has('is_block')) {

        Customer::where('id', $request->id)->update(['is_blokir' => $request->is_block]);

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // dd($request);
        $data = Customer::find($request->id);
        $data->nama = $request->nama;
        $data->nama_npwp = $request->nama_npwp;
        $data->npwp = $request->npwp;
        $data->email = $request->email;
        $data->no_telp = $request->no_telp;
        $data->alamat = $request->alamat;
        $data->kota = $request->kota;
        $data->alamat_npwp = $request->alamat_npwp;
        $data->top = $request->top;
        $data->sales = $request->sales;

        if ($data->save()) {
            return redirect()->route('master.customer', $data)->with('success', 'Data Master Customer berhasil diubah!');
        } else {
            return redirect()->route('master.customer', $data)->with('error', 'Data Master Customer gagal diubah!');
        }

        return redirect()->route('master.customer');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $data = Customer::destroy(request('id'));

        if ($data) {
            return redirect()->route('master.customer')->with('success', 'Data Master Customer berhasil dihapus!');
        } else {
            return redirect()->route('master.customer')->with('error', 'Data Master Customer gagal dihapus!');
        }
    }


public function datatable()
{
    $data = Customer::query()->orderBy('id', 'desc');

  return DataTables::of($data)
    ->addIndexColumn()
    ->addColumn('status', function ($row) {
        $checkedAktif = $row->is_blokir == 0 ? 'checked' : '';
        $checkedNonAktif = $row->is_blokir == 1 ? 'checked' : '';

        $customerName = addslashes($row->nama); // hindari error JS jika nama ada tanda kutip
return '
<div class="flex items-center gap-2">
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" class="status-switch sr-only" 
            data-id="' . $row->id . '" 
            data-name="' . addslashes($row->nama) . '" 
            ' . ($row->is_blokir == 0 ? 'checked' : '') . '>
        <div class="status-wrapper w-11 h-6 rounded-full relative transition-all duration-300 ' . ($row->is_blokir == 0 ? 'bg-green-500' : 'bg-red-500') . '">
            <div class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all duration-300 transform ' . ($row->is_blokir == 0 ? 'translate-x-5' : '') . '"></div>
        </div>
        <span class="status-text ml-2 text-sm font-semibold ' . ($row->is_blokir == 0 ? 'text-green-600' : 'text-red-600') . '">' . ($row->is_blokir == 0 ? 'Aktif' : 'Blokir') . '</span>
    </label>
</div>
';



    })

        ->addColumn('aksi', function ($row) {
            return '<div class="flex gap-3 mt-2">
                <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama) . '\', \'' . addslashes($row->npwp) . '\', \'' . addslashes($row->nama_npwp) . '\', \'' . addslashes($row->email) . '\', \'' . addslashes($row->no_telp) . '\', \'' . addslashes($row->alamat) . '\', \'' . addslashes($row->alamat_npwp) . '\', \'' . addslashes($row->kota) . '\', ' . $row->top . ', \'' . addslashes($row->sales) . '\')" class="text-yellow-500 font-semibold">
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
