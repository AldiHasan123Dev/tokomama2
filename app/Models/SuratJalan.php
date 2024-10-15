<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'surat_jalan';

    public function nsfp()
    {
        return $this->belongsTo(NSFP::class, 'id_nsfp');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_surat_jalan');
    }
}
