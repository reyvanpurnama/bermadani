<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditBankCategoryRule extends Model
{
    protected $fillable = [
        'pattern',
        'type',
        'category',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Scope for active rules only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope ordered by priority (highest first)
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Static method to match keterangan against rules
    public static function matchKeterangan($keterangan)
    {
        $rules = self::active()->byPriority()->get();
        
        foreach ($rules as $rule) {
            if (preg_match('/' . $rule->pattern . '/i', $keterangan)) {
                return [
                    'type' => $rule->type,
                    'category' => $rule->category,
                    'matched_rule_id' => $rule->id,
                ];
            }
        }
        
        return null; // No match found
    }
}
