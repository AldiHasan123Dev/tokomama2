<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('masters.menu', compact('roles'));
    }
}
