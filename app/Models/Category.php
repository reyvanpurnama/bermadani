<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'order',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'order' => 'integer',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'categoryId');
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
