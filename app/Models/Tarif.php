<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarif extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tarif';

    protected $fillable = [
        'id_ab',
        'tarif',
        'status',
        'created_by',
        'updated_by',
    ];

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'id_ab');
    }
}
