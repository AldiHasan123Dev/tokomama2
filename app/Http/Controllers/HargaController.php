<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Harga;

class HargaController extends Controller
{
    public function index()
    {
        $barang = Barang::with('satuan')->where('status','AKTIF')->get();
        return view('masters.harga', compact('barang'));
    }

     public function store(Request $request)
    {
        $id_barang = $request->id_barang;
        $barang = Barang::find($id_barang);
       $errors = [];

    if ($request->harga === null) {
        $errors['harga'] = 'Harga wajib diisi.';
    }

    if ($request->tgl === null) {
        $errors['tgl'] = 'Tanggal Harga wajib diisi.';
    }

    if ($request->id_barang === null) {
        $errors['id_barang'] = 'Barang wajib diisi.';
    }

    if (!empty($errors)) {
        return redirect()->back()
            ->withErrors($errors)
            ->withInput();
    }
        if ($barang->status_ppn == 'ya'){
            $harga = (double) preg_replace('/[^0-9]/', '', $request->harga);
            $harga = round($harga / 1.11 ,4);
        } else{
            $harga = (double) preg_replace('/[^0-9]/', '', $request->harga);
        }
        Harga::create([
            'id_barang'       => $barang->id,
            'tgl' => $request->tgl,
            'harga'      => $harga,
            'created_by' => auth()->id(),
        ]);

     return redirect()->route('master.harga')->with('success', 'Data Master Harga berhasil ditambahkan!');
    }

    public function Hargajqgrid()
    {
            $page    = request('page', 1);
            $limit   = request('rows', 10);
            $sidx    = request('sidx', 'harga.id');
            $sord    = request('sord', 'asc');
            $search  = request('_search') === 'true';
                    
            $query = Harga::with(['barang', 'barang.satuan']);

            // Filter tgl_pembayar (wajib / selalu ada)
            if (request()->filled('non_aktif')) {
                $query->where('is_status',0);
            }
            if (request()->filled('aktif')) {
                $query->where('is_status',1);
            }
            // Ambil semua data
            $harga = $query->get();
            // Group berdasarkan tgl_pembayar
            
        
            $totalRecords = $harga->count();
            $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 0;
            if ($page > $totalPages) $page = $totalPages;
        
            $paginated = $harga->slice(($page - 1) * $limit, $limit);
        
            // Format data baris
            $rows = $paginated->map(function ($items, $key) {
                $harga = number_format($items->harga, 2, ',', '.');
                if ($items->barang->status_ppn == 'ya'){
                    $harga = number_format($items->harga * 1.11, 2, ',', '.'); // misal formatting harga
                }
                return [
                    'id'         => $items->id,
                    'tgl'        => $items->tgl,
                    'harga'      => $harga,
                    'barang'     => $items->barang->nama . ' || ' . $items->barang->satuan->nama_satuan,
                    'status_ppn' => $items->barang->status_ppn,
                    'aktif'      => $items->is_status,
                ];
            })->values();

            
        
            // Total semua nominal (dari semua grup, bukan hanya paginasi)
        
            // Return ke jqGrid
            return response()->json([
                'page'    => $page,
                'total'   => $totalPages,
                'records' => $totalRecords,
                'rows'    => $rows
            ]);
        }
}
