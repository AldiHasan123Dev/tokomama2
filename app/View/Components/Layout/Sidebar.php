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
        $sub_menu = SubMenu::whereIn('id',$menus)->get()->groupBy('menu_id');
        return view('components.layout.sidebar', compact('sub_menu'));
    }
}
