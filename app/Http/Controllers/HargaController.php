<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaction;
use App\Models\Satuan;
use App\Models\Harga;

class HargaController extends Controller
{
    public function index()
    {
        $barang = Barang::with('satuan')->where('status', 'AKTIF')->get();
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

    public function Hargajqgrid(Request $request)
    {
             $searchTerm   = $request->get('searchString', '');
            $currentPage  = (int) $request->get('page', 1);
            $perPage      = (int) $request->get('rows', 10);
                    
            $query = Harga::with(['barang', 'barang.satuan'])
            ->orderBy('created_at','desc');

                    if ($request->filled('barang')) {
                        $query->whereHas('barang', function ($q) use ($request) {
                            $q->where('nama', 'LIKE', '%' . $request->barang . '%');
                        });
                    }

                    if ($request->filled('satuan')) {
                        $satuan = $request->satuan;

                        $query->whereHas('barang.satuan', function ($q) use ($satuan) {
                            $q->where('nama_satuan', 'LIKE', '%' . $satuan . '%');
                        });
                    }


                    
                    if ($request->filled('tgl')) {
                        $query->where('tgl', 'LIKE', '%' . $request->tgl . '%');
                    }

                    if ($request->filled('harga')) {
                        $query->where('harga', 'LIKE', '%' . $request->harga . '%');
                    }
                    

            // Ambil semua data
            $harga = $query->get();
            // Group berdasarkan tgl_pembayar
            
        
            $totalRecords = $harga->count();
             $paginated = $harga->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
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
                    'satuan'     => $items->barang->satuan->nama_satuan,
                    'barang'     => $items->barang->nama,
                    'status_ppn' => $items->barang->status_ppn === 'ya' ? 'PPN' : 'NON-PPN',
                    'aktif'      => $items->is_status,
                ];
            })->values();

            
        
            // Total semua nominal (dari semua grup, bukan hanya paginasi)
        
            // Return ke jqGrid
            return response()->json([
                'page'    => $currentPage,
                'total'   => ceil($totalRecords / $perPage),
                'records' => $totalRecords,
                'rows'    => $rows
            ]);
        }

        public function updateAktif(Request $request)
        {
            $idTerpilih = $request->input('id');
            // Aktifkan hanya yang dipilih
            $harga= Harga::find($idTerpilih);
            $cek = $harga->is_status;
            if ($cek == 1){
                 $harga->update(['is_status' => 0]);
            } else{
                 $harga->update(['is_status' => 1]);
            }
            $hargaValue = number_format($harga->harga, 2, ',', '.');;
            if ($harga->barang->status_ppn == 'ya'){
                $hargaValue = number_format($harga->harga * 1.11, 2, ',', '.');
            }
             if ($harga->is_status == 1){
            return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $harga->barang->nama . ' senilai ' . $hargaValue . ' sudah di aktifkan']);
             } else {
                 return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $harga->barang->nama . ' senilai ' . $hargaValue . ' sudah di non-aktifkan']);
             }
        }

        public function updateNonAktif(Request $request)
        {
            $idTerpilih = $request->input('id');
            // Aktifkan hanya yang dipilih
            $harga= Harga::find($idTerpilih);
            $harga->update(['is_status' => 0]);
            $hargaValue = number_format($harga->harga, 2, ',', '.');;
            if ($harga->barang->status_ppn == 'ya'){
                $hargaValue = number_format($harga->harga * 1.11, 2, ',', '.');
            }

            return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $harga->barang->nama . ' senilai ' . $hargaValue . ' sudah non-aktif']);
        }

}
