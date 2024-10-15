<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalTemplate;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Template\Template;
use Yajra\DataTables\DataTables;

class TemplateJurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('jurnal.template-jurnal');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coa = Coa::where('status', 'aktif')->get();
        return view('jurnal.create-jurnal-template', compact('coa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request->keterangan);
        DB::transaction(function () use ($request) {
            $result = TemplateJurnal::create([
                'nama' => $request->nama
            ]);
            $idTemplateJurnal = TemplateJurnal::latest('id')->first();

            // dd($request->all());

            if ($result) {
                for ($i = 0; $i < $request->counter; $i++) {
                    //  dd($request->keterangan[$i]);
                    TemplateJurnalItem::create([
                        'template_jurnal_id' => $idTemplateJurnal->id,
                        'coa_debit_id' => $request->coa_debit_id[$i],
                        'coa_kredit_id' => $request->coa_kredit_id[$i],
                        'keterangan' => $request->keterangan[$i]
                    ]);
                }
            }
        });

        return to_route('jurnal.template-jurnal.create')->with('success', 'Data berhasil tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TemplateJurnal $templateJurnal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateJurnal $templateJurnal, Request $request)
    {
        $jurnalTemplate = TemplateJurnalItem::where('template_jurnal_id', $request->id)->get();
        $coa = Coa::where('status', 'aktif')->get();
        // dd($jurnalTemplate);
        // return to_route('jurnal.template-jurnal.editView', ['jurnalTemplate' => $jurnalTemplate, 'coa' => $coa]);
        return view('jurnal.edit-jurnal-template', compact('jurnalTemplate', 'coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemplateJurnal $templateJurnal)
    {
        // $data = TemplateJurnal::find($request);
        // $data->nama = $request->nama;
        // dd($request);
        TemplateJurnal::where('id', $request->nama_template_id)->update(['nama' => $request->nama]);

        // for ($i=0; $i < count($request->id); $i++) { 
        //     TemplateJurnalItem::where('id', $request->id[$i])->update([
        //         'coa_debit_id' => $request->coa_debit_id[$i],
        //         'coa_kredit_id' => $request->coa_kredit_id[$i],
        //         'keterangan' => $request->keterangan[$i]
        //     ]);
        // }

        // for ($i=0; $i < count($request->id); $i++) { 
        //     // TemplateJurnalItem::where('id', $request->id[$i])->update([
        //     //     'template_jurnal_id' => $request->nama_template_id,
        //     //     'coa_debit_id' => $request->coa_debit_id[$i],
        //     //     'coa_kredit_id' => $request->coa_kredit_id[$i],
        //     //     'keterangan' => $request->keterangan[$i]
        //     // ]);

        //     TemplateJurnalItem::upsert([
        //         'template_jurnal_id' => $request->nama_template_id,
        //         'coa_debit_id' => $request->coa_debit_id[$i],
        //         'coa_kredit_id' => $request->coa_kredit_id[$i],
        //         'keterangan' => $request->keterangan[$i]
        //     ], uniqueBy: ['template_jurnal_id'], update: ['coa_debit_id', 'coa_kredit_id', 'keterangan']);
        // }

        // Ambil template jurnal yang akan diupdate
    $templateJurnal = TemplateJurnalItem::findOrFail($request->id);

    // Ambil id item yang sudah ada dari request
    $existingIds = $request->input('id', []);

    // Data yang akan diupdate
    for ($i = 0; $i < count($existingIds); $i++) {
        TemplateJurnalItem::where('id', $existingIds[$i])->update([
            'template_jurnal_id' => $request->input('nama_template_id'),
            'coa_debit_id' => $request->input('coa_debit_id')[$i],
            'coa_kredit_id' => $request->input('coa_kredit_id')[$i],
            'keterangan' => $request->input('keterangan')[$i],
        ]);
    }

    // Data baru yang akan ditambahkan
    for ($i = count($existingIds); $i < count($request->input('coa_debit_id', [])); $i++) {
        TemplateJurnalItem::create([
            'template_jurnal_id' => $request->input('nama_template_id'),
            'coa_debit_id' => $request->input('coa_debit_id')[$i],
            'coa_kredit_id' => $request->input('coa_kredit_id')[$i],
            'keterangan' => $request->input('keterangan')[$i],
        ]);
    }

    // Redirect atau kembali dengan pesan sukses
    // return redirect()->route('route.name')->with('success', 'Data updated successfully.');


        return to_route('jurnal.template-jurnal');
        // if ($data->save()) {
        //     return redirect()->route('jurnal.template-jurnal')->with('success', 'Nama Template Jurnal berhasil diubah!');
        // } else {
        //     return redirect()->route('jurnal.template-jurnal')->with('error', 'Nama Template Jurnal gagal diubah!');
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemplateJurnal $templateJurnal)
    {
        // $result = TemplateJurnalItem::where('template_jurnal_id', request('id'));
        TemplateJurnal::destroy(request('id'));
        return route('jurnal.template-jurnal');
    }

    public function datatable()
    {
        $data = TemplateJurnal::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '
                <div class="flex gap-3 mt-2">
                <form action="/template-jurnal-edit" method="post">
                    ' . csrf_field() . '
                    <input type="hidden" name="id" value="' . $row->id . '">
                    <button onclick="getData(' . $row->id . ', \'' . addslashes($row->nama) . '\')" id="delete-faktur-all" class="text-yellow-300 font-semibold mb-3 self-end" ><i class="fa-solid fa-pencil"></i></button> </form> |
                    <button onclick="deleteData(' . $row->id . ')"  id="delete-faktur-all" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make();
    }
}
