<?php

namespace App\View\Components\Layout;

use App\Models\RoleMenu;
use App\Models\SubMenu;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $role_id = Auth::user()->role_id;
        $menus = RoleMenu::where('role_id',$role_id)->pluck('menu_id')->toArray();
        $sub_menu = SubMenu::whereIn('sub_menu.id', $menus)
        ->join('menu', 'sub_menu.menu_id', '=', 'menu.id')
        ->select('sub_menu.*', 'menu.order as menu_order') // Ambil semua kolom dari sub_menu dan kolom order dari menu
        ->orderBy('menu.order') // Urutkan berdasarkan 'order' dari tabel menu
        ->orderBy('sub_menu.order') // Urutkan berdasarkan 'order' dari tabel sub_menu
        ->get()
        ->groupBy('menu_id');
        return view('components.layout.sidebar', compact('sub_menu'));
    }
}
