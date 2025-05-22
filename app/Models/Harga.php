<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Harga extends Model
{
    use SoftDeletes;

    protected $table = 'harga';

    protected $fillable = [
        'id_barang',
        'harga',
        'tgl',
        'is_status',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['tgl', 'deleted_at'];

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // (Opsional) relasi ke user pembuat dan pengubah data
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
