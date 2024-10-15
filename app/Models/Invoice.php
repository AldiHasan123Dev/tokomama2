<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    protected $fillable = [
        'id_transaksi',
        'id_nsfp',
        'invoice',
        'harga',
        'jumlah',
        'subtotal',
        'no',
        'tgl_invoice',
    ];

    public function nsfp()
    {
        return $this->belongsTo(NSFP::class, 'id_nsfp', 'id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi', 'id');
    }
    public function jurnal()
    {
        return $this->hasMany(Jurnal::class, 'invoice', 'invoice'); 
    }
    
}
