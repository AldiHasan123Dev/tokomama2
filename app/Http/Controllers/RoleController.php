<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\SubMenu;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masters.role');
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
        Role::create($request->all());
        return back()->with('success', 'Data role berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $sub_menu = SubMenu::all()->groupBy('menu_id');
        return view('masters.role', compact('role','sub_menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $role->update([
            'name' => $request->name
        ]);
        $sub_menu_id = explode(',', $request->sub_menu_id);
        RoleMenu::where('role_id', $role->id)->delete();
        foreach ($sub_menu_id as $key => $value) {
            RoleMenu::create([
                'role_id' => $role->id,
                'menu_id' => $value
            ]);
        }
        return back()->with('success', 'Data role berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Data role berhasil dihapus');
    }
}
