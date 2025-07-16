<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
       use HasFactory, SoftDeletes;
    protected $table = 'sales';
    protected $guarded = ['id'];
    public function customer()
    {
        return $this->hasMany(Customer::class, 'id_sales'); // Sesuaikan dengan nama kolom yang tepat
    }
}
