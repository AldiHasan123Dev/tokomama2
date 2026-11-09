<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaInv extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel jika tidak menggunakan nama default
    protected $table = 'biaya_inv';

    // Tentukan kolom yang bisa diisi
    protected $fillable = [
        'id_trans',
        'id_inv',
        'nominal',
        'tgl_pembayar',
        'tipe',
    ];

    // Relasi dengan tabel transaksi (many to one)
    public function transaksi()
    {
        return $this->belongsTo(Transaction::class, 'id_trans');
    }

    // Relasi dengan tabel invoice (many to one)
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_inv');
    }
}
