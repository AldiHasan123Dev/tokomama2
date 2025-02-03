<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'tgl_beli',
        'tgl_jual',
        'vol_bm',
        'vol_bk',
        'is_active',
        'sisa'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
