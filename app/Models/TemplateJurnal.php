<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateJurnal extends Model
{
    use HasFactory;
    protected $table = 'template_jurnal';
    protected $fillable = [
        'nama',
    ];

    public function template_jurnal_item()
    {
        return $this->hasMany(TemplateJurnalItem::class, 'template_jurnal_id');
    }
}
