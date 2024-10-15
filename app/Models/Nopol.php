<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nopol extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'nopol';
    protected $guarded = ['id'];
}
