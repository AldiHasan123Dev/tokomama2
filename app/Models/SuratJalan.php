<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SuratJalan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'surat_jalan';

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
        static::saving(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

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
