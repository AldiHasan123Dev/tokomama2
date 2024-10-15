<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'jurnal';
    protected $fillable = [
        'coa_id',
        'nomor',
        'tgl',
        'keterangan',
        'keterangan_buku_besar_pembantu',
        'debit',
        'kredit',
        'invoice',
        'invoice_external',
        'id_transaksi',
        'nopol',
        'container',
        'tipe',
        'no',
        'created_by',
        'updated_by',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id'); 
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id'); // Sesuaikan nama kolom kunci
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'invoice');
}

    public function transaksi()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi');
    }
    
}
