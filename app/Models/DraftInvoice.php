<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DraftInvoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'draft_invoices';

    /**
     * Kolom yang bisa diisi (mass assignable)
     */
    protected $fillable = [
        'invoice_id',
        'id_sj',
        'id_transaksi',
        'tanggal',
        'harga',
        'jumlah',
        'subtotal',
        'draft_no',
        'no',
    ];

    /**
     * Kolom tanggal yang otomatis di-cast sebagai Carbon instance
     */
    protected $dates = ['deleted_at'];

    /**
     * Relasi ke tabel invoices
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relasi ke tabel surat jalan
     */
    public function suratJalan() 
    {
        return $this->belongsTo(SuratJalan::class, 'id_sj');
    }

    /**
     * Relasi ke tabel transaksi
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi');
    }
}
