<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    protected $fillable = [
        'title',
        'name',
        'icon',
        'url',
        'order',
    ];

    public function sub_menu()
    {
        return $this->hasMany(SubMenu::class,'menu_id');
    }
}
