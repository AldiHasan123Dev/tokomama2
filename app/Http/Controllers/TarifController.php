<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\AlatBerat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TarifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $alatBerats = AlatBerat::get();
        return view('masters.tarif', compact('alatBerats'));
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
        $id_ab = $request->id_ab;
        $alatBerat = AlatBerat::find($id_ab);
        $errors = [];

    if ($request->tarif === null) {
        $errors['tarif'] = 'Harga wajib diisi.';
    }
    if ($request->id_ab === null) {
        $errors['id_ab'] = 'Barang wajib diisi.';
    }

     $tarif = (double) preg_replace('/[^0-9]/', '', $request->tarif);
    if (!empty($errors)) {
        return redirect()->back()
            ->withErrors($errors)
            ->withInput();
    }
        Tarif::create([
            'id_ab'       => $alatBerat->id,
            'status'      => 1,
            'tarif'      => $tarif,
            'created_by' => auth()->id(),
        ]);

     return redirect()->route('master.tarif')->with('success', 'Data Master Harga berhasil ditambahkan!');
    }

    public function updateTarifAktif(Request $request)
        {
            $idTerpilih = $request->input('id');
            // Aktifkan hanya yang dipilih
            $tarif= Tarif::find($idTerpilih);
            $cek = $tarif->status;
            if ($cek == 1){
                 $tarif->update(['status' => 0]);
            } else{
                 $tarif->update(['status' => 1]);
            }
            $tarifValue = number_format($tarif->tarif, 2, ',', '.');;
             if ($tarif->status == 1){
            return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $tarif->alatBerat->nama_alat . ' senilai ' . $tarifValue . ' sudah di aktifkan']);
             } else {
                 return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $tarif->alatBerat->nama_alat . ' senilai ' . $tarifValue . ' sudah di non-aktifkan']);
             }
        }

         public function updateTarifNonAktif(Request $request)
        {
            $idTerpilih = $request->input('id');
            // Aktifkan hanya yang dipilih
            $tarif= Tarif::find($idTerpilih);
            $tarif->update(['is_status' => 0]);
            $tarifValue = number_format($tarif->tarif, 2, ',', '.');;

            return response()->json(['success' => true, 'message' => 'Harga Untuk ' . $tarif->alatBerat->nama_alat . ' senilai ' . $tarifValue . ' sudah non-aktif']);
        }

    public function Tarifjqgrid(Request $request)
    {
             $searchTerm   = $request->get('searchString', '');
            $currentPage  = (int) $request->get('page', 1);
            $perPage      = (int) $request->get('rows', 10);
                    
            $query = Tarif::with(['alatBerat'])
            ->orderBy('created_at','desc');

                    if ($request->filled('alat_berat')) {
                        $query->whereHas('alatBerat', function ($q) use ($request) {
                            $q->where('nama_alat', 'LIKE', '%' . $request->alat_berat . '%');
                        });
                    }
                    

                    if ($request->filled('tarif')) {
                        $query->where('tarif', 'LIKE', '%' . $request->tarif . '%');
                    }
                    

            // Ambil semua data
            $harga = $query->get();
            // Group berdasarkan tgl_pembayar
            
        
            $totalRecords = $harga->count();
             $paginated = $harga->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
            // Format data baris
            $rows = $paginated->map(function ($items, $key) {
                $tarif = number_format($items->tarif, 2, ',', '.');
                return [
                    'id'         => $items->id,
                    'tarif'      => $tarif,
                    'alat_berat'     => $items->alatBerat->nama_alat,
                    'aktif'      => $items->status,
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

    /**
     * Display the specified resource.
     */
    public function show(Tarif $tarif)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarif $tarif)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarif $tarif)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarif $tarif)
    {
        //
    }
}
