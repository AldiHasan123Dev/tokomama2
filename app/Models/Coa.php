<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_akun', 'nama_akun', 'status', 'tabel'
    ];

    protected $table = 'coa';

    protected $guarded = ['id'];

    public function template_jurnal_item_coa_debit()
    {
        return $this->hasMany(TemplateJurnalItem::class, 'coa_debit_id');
    }

    public function template_jurnal_item_coa_kredit()
    {
        return $this->hasMany(TemplateJurnalItem::class, 'coa_kredit_id');
    }

    public function jurnal()
    {
        return $this->hasMany(Jurnal::class, 'coa_id');
    }
}
