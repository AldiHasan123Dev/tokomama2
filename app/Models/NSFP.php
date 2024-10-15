<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class NSFP extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nsfp';
    protected $fillable = [
        'nomor',
        'status',
        'invoice',
        'keterangan',
        'available',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }



    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $model->created_by = Auth::id();
    //         $model->updated_by = Auth::id();
    //     });
    //     static::saving(function ($model) {
    //         $model->updated_by = Auth::id();
    //     });
    // }
}
