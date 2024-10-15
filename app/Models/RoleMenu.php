<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    use HasFactory;
    protected $table = 'role_menu';
    protected $fillable = [
        'menu_id',
        'role_id',
    ];
    
    public function menu(){
        return $this->belongsTo(SubMenu::class, 'menu_id', 'id');
    }
}
