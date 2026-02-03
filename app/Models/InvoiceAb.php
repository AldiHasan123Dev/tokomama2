<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceAb extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoice_ab';
    protected $guarded = ['id'];

     public function customersAb()
    {
        return $this->belongsTo(CustomersAB::class, 'penerima');
    }
   public function order()
{
    return $this->belongsTo(Orders::class, 'id_order');
}
}
 