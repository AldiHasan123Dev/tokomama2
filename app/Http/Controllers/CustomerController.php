<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sales;
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
        $sales = Sales::all();
        return view('masters.customer', compact('sales'));
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
    $data = $request->all();

    // Ambil nama sales berdasarkan ID
    $sales = Sales::find($request->sales);
    if (!$sales) {
        return redirect()->back()->with('error', 'Sales tidak ditemukan.');
    }
    // Contoh: menyimpan ke tabel customer (atau jurnal kalau kamu simpan di sana)
    $customer = new Customer();
    $customer->nama = $data['nama'];
    $customer->npwp = $data['npwp'];
    $customer->nama_npwp = $data['nama_npwp'];
    $customer->top = $data['top'];
    $customer->id_sales = $sales->id;
    $customer->sales = $sales->nama;
    $customer->email = $data['email'];
    $customer->no_telp = $data['no_telp'];
    $customer->alamat = $data['alamat'];
    $customer->kota = $data['kota'];
    $customer->alamat_npwp = $data['alamat_npwp'];
    $customer->save();

    return redirect()->route('master.customer')->with('success', 'Data Master Customer berhasil ditambahkan!');
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
        $sales = Sales::find($request->sales);
        if (!$sales) {
            return redirect()->back()->with('error', 'Sales tidak ditemukan.');
        }
        $data->nama = $request->nama;
        $data->nama_npwp = $request->nama_npwp;
        $data->npwp = $request->npwp;
        $data->email = $request->email;
        $data->id_sales = $sales->id;
        $data->sales = $sales->nama;
        $data->no_telp = $request->no_telp;
        $data->alamat = $request->alamat;
        $data->kota = $request->kota;
        $data->alamat_npwp = $request->alamat_npwp;
        $data->top = $request->top;

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
    $escapedNama        = addslashes($row->nama);
    $escapedNpwp        = addslashes($row->npwp);
    $escapedNamaNpwp    = addslashes($row->nama_npwp);
    $escapedEmail       = addslashes($row->email);
    $escapedNoTelp      = addslashes($row->no_telp);
    $escapedAlamat      = addslashes($row->alamat);
    $escapedAlamatNpwp  = addslashes($row->alamat_npwp);
    $escapedKota        = addslashes($row->kota);
    $escapedSales       = addslashes($row->sales);

    return '<div class="flex gap-3 mt-2">
        <button onclick="getData('
        . $row->id . ', '
        . '\'' . $row->id_sales . '\', '
        . '\'' . $escapedNama . '\', '
        . '\'' . $escapedNpwp . '\', '
        . '\'' . $escapedNamaNpwp . '\', '
        . '\'' . $escapedEmail . '\', '
        . '\'' . $escapedNoTelp . '\', '
        . '\'' . $escapedAlamat . '\', '
        . '\'' . $escapedAlamatNpwp . '\', '
        . '\'' . $escapedKota . '\', '
        . $row->top . ', '
        . '\'' . $escapedSales . '\''
        . ')" class="text-yellow-500 font-semibold">
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
