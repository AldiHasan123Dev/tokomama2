<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomersAB extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'customers_ab';
    protected $guarded = ['id'];

     public function invoiceAb()
    {
        return $this->hasMany(invoiceAb::class, 'penerima'); // Sesuaikan dengan nama kolom yang tepat
    }
    public function customersAb()
    {
        return $this->hasMany(CustomersAb::class, 'customers_id'); // Sesuaikan dengan nama kolom yang tepat
    }
}
