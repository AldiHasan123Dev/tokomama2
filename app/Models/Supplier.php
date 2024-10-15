<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nama',
        'nama_npwp',
        'npwp',
        'nik',
        'email',
        'kota',
        'no_telp',
        'alamat',
        'alamat_npwp',
    ];

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'id_supplier');
    }
}
