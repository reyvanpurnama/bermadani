<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooperativeSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'group', 'label'];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, $default = null): ?string
    {
        return cache()->remember("coop_setting_{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Set a setting value (and bust the cache).
     */
    public static function setValue(string $key, string $value, ?string $group = null, ?string $label = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'group' => $group,
                'label' => $label,
            ])
        );
        cache()->forget("coop_setting_{$key}");
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
