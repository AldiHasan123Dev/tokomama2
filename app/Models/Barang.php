<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'barang';
    protected $guarded = ['id'];

    public function satuan() {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
    public function hargaAktif()
{
    return $this->hasMany(Harga::class, 'id_barang')->where('is_status', 1);
}

}
