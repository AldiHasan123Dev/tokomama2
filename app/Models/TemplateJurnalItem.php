<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateJurnalItem extends Model
{
    use HasFactory;
    protected $table = 'template_jurnal_item';
    protected $fillable = [
        'template_jurnal_id',
        'coa_debit_id',
        'coa_kredit_id',
        'keterangan'
    ];

    public function template_jurnal()
    {
        return $this->belongsTo(TemplateJurnal::class, 'template_jurnal_id', 'id');
    }

    public function coa_debit() : BelongsTo {
        return $this->belongsTo(Coa::class, 'coa_debit_id', 'id');
    }

    public function coa_kredit() : BelongsTo {
        return $this->belongsTo(Coa::class, 'coa_kredit_id', 'id');
    }
}
