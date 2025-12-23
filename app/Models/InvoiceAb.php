<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAb extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoice_ab';
    protected $guarded = ['id'];

     public function customersAb()
    {
        return $this->belongsTo(CustomersAB::class, 'penerima');
    }
}
 