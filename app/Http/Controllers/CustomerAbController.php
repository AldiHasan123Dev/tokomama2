<?php

namespace App\Http\Controllers;

use App\Models\CustomersAb;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;
use Yajra\DataTables\DataTables;

class CustomerAbController extends Controller
{
    public function index()
    {
        return view('masters.customer-ab');
    }
        public function store(Request $request)
    {
        $data = $request->all();
        // Contoh: menyimpan ke tabel customer (atau jurnal kalau kamu simpan di sana)
        $customer = new CustomersAb();
        $customer->nama = $data['nama'];
        $customer->npwp = $data['npwp'];
        $customer->nama_npwp = $data['nama_npwp'];
        $customer->no_telp = $data['no_telp'];
        $customer->alamat = $data['alamat'];
        $customer->nik = $data['nik'];
        $customer->kota = $data['kota'];
        $customer->alamat_npwp = $data['alamat_npwp'];
        $customer->save();

        return redirect()->route('master.customer_ab')->with('success', 'Data Master Customer Alat Berat berhasil ditambahkan!');
    }

     public function update(Request $request, CustomersAb $customer)
    {
        // dd($request);
        $data = CustomersAb::find($request->id);
        $data->nama = $request->nama;
        $data->nama_npwp = $request->nama_npwp;
        $data->npwp = $request->npwp;
        $data->no_telp = $request->no_telp;
        $data->alamat = $request->alamat;
        $data->kota = $request->kota;
        $data->nik = $request->nik;
        $data->alamat_npwp = $request->alamat_npwp;

        if ($data->save()) {
            return redirect()->route('master.customer_ab', $data)->with('success', 'Data Master Customer Alat Berat berhasil diubah!');
        } else {
            return redirect()->route('master.customer_ab', $data)->with('error', 'Data Master Customer Alat Berat gagal diubah!');
        }

        return redirect()->route('master.customer_ab');
    }

     public function destroy()
    {
        $data = CustomersAb::destroy(request('id'));

        if ($data) {
            return redirect()->route('master.customer')->with('success', 'Data Master Customer berhasil dihapus!');
        } else {
            return redirect()->route('master.customer')->with('error', 'Data Master Customer gagal dihapus!');
        }
    }

        public function datatable()
    {
        $data = CustomersAb::query()->orderBy('id', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
    ->addColumn('aksi', function ($row) {
        $escapedNama        = addslashes($row->nama);
        $escapedNpwp        = addslashes($row->npwp);
        $escapedNamaNpwp    = addslashes($row->nama_npwp);
        $escapedNik       = addslashes($row->nik);
        $escapedNoTelp      = addslashes($row->no_telp);
        $escapedAlamat      = addslashes($row->alamat);
        $escapedAlamatNpwp  = addslashes($row->alamat_npwp);
        $escapedKota        = addslashes($row->kota);

        return '<div class="flex gap-3 mt-2">
            <button onclick="getData('
            . $row->id . ', '
            . '\'' . $escapedNama . '\', '
            . '\'' . $escapedNpwp . '\', '
            . '\'' . $escapedNamaNpwp . '\', '
            . '\'' . $escapedNik . '\', '
            . '\'' . $escapedNoTelp . '\', '
            . '\'' . $escapedAlamat . '\', '
            . '\'' . $escapedAlamatNpwp . '\', '
            . '\'' . $escapedKota . '\', '
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
