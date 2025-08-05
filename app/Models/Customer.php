<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'customer';
    protected $guarded = ['id'];
    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_customer'); // Sesuaikan dengan nama kolom yang tepat
    }
    
}
