<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class FixedAsset extends Model
{
    protected $fillable = [
        'name',
        'category',
        'acquisition_date',
        'acquisition_cost',
        'useful_life_months',
        'salvage_value',
        'depreciation_method',
        'status',
        'disposed_at',
        'disposed_value',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'disposed_at' => 'date',
        'disposed_value' => 'decimal:2',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMonthlyDepreciationAttribute()
    {
        if ($this->useful_life_months <= 0) {
            return 0;
        }
        return ($this->acquisition_cost - $this->salvage_value) / $this->useful_life_months;
    }

    public function getAccumulatedDepreciationAttribute()
    {
        if ($this->status === 'DISPOSED' || $this->status === 'WRITTEN_OFF') {
            $endDate = $this->disposed_at ? Carbon::parse($this->disposed_at) : Carbon::now();
        } else {
            $endDate = Carbon::now();
        }

        $acquisitionDate = Carbon::parse($this->acquisition_date);
        
        $monthsElapsed = $acquisitionDate->diffInMonths($endDate);
        if ($monthsElapsed > $this->useful_life_months) {
            $monthsElapsed = $this->useful_life_months;
        }

        $depreciation = $this->monthly_depreciation * $monthsElapsed;
        
        $maxDepreciation = $this->acquisition_cost - $this->salvage_value;
        return min($depreciation, $maxDepreciation);
    }

    public function getNetBookValueAttribute()
    {
        return $this->acquisition_cost - $this->accumulated_depreciation;
    }
}
