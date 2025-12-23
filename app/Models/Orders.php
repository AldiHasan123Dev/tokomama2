<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'tarif_id',
        'customers_id',
        'tanggal_order',
        'kode_invoice',
        'created_by',
        'updated_by',
    ];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }
    public function customersAb()
    {
        return $this->belongsTo(CustomersAb::class, 'customers_id');
    }
    
}
