<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CalkEntry extends Model
{
    protected $fillable = [
        'fiscal_year',
        'section_key',
        'content',
        'updated_by',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
