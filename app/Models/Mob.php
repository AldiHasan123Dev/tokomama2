<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mob';

    protected $fillable = [
        'id_order',
        'ket',
        'nominal',
        'created_by',
        'updated_by',
    ];

     public function orders()
    {
        return $this->belongsTo(Orders::class, 'id_order');
    }
}
