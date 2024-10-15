<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'id_surat_jalan',
        'id_barang',
        'harga_beli',
        'jumlah_beli',
        'satuan_beli',
        'harga_jual',
        'jumlah_jual',
        'sisa',
        'satuan_jual',
        'margin',
        'keterangan',
        'id_supplier'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'id_surat_jalan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_transaksi');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'id_transaksi', 'id');
    }
    // Di model Transaksi
    public function Jurnal()
    {
        return $this->hasOne(Jurnal::class, 'id_transaksi', 'id'); // Pastikan ini sesuai dengan struktur database Anda
    }
    
}
