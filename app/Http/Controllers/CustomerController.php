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
