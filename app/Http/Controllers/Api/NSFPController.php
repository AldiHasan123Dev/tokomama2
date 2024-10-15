<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NSFP;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;

use function Laravel\Prompts\alert;

class NSFPController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $no = str_replace(' ', '', $request->nomor);
            $res = explode('.', $no);
            $depan = $res[0] . '.' . $res[1] . '.' . $res[2] . '.';
            $res = (int)end($res);
            for ($i = 0; $i < $request->jumlah; $i++) {
                $num = $res + $i;
                NSFP::create([
                    'nomor' => $depan . '' . sprintf('%08d', $num),
                    'available' => 1
                ]);
            }
            return response('success');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // dd("berhasil masuk controller");
    }

    public function data()
    {
        $query = NSFP::query()->where('available', 1);
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<button class="text-yellow-400" onclick="getDataNSFP(' . $row->id . ', \'' . addslashes($row->nomor) . '\', \'' . addslashes($row->keterangan) . '\')"><i class="fa-solid fa-pencil"></i></button> | <button onclick="deleteData(' . $row->id . ')" id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end" ><i class="fa-solid fa-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make();
    }

    public function deleteNSFP()
    {
        NSFP::destroy(request('id'));
        return route('pajak.nsfp');
    }

    public function dataNSFPDone()
    {
        $data = NSFP::query()->where('available', 0);
        return Datatables::of($data)
            ->addIndexColumn()
            ->make();
    }

    public function deleteAllNSFP()
    {
        NSFP::where('available', 1)->delete();

        return redirect()->route('pajak.nsfp');
    }

    public function update(Request $request) {
        $data = NSFP::find($request->id);
        $data->nomor = $request->nomor;
        $data->keterangan = $request->keterangan;
        $data->save();
        return redirect()->route('pajak.nsfp');
    }
}
