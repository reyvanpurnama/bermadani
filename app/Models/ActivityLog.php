<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'loggable_type',
        'loggable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    // Helper method to create log entry
    public static function log(
        string $action,
        string $module,
        string $description,
        $loggable = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ) {
        // Log for Admin, SuperAdmin, and Kasir (not Supplier or Developer)
        $user = auth()->user();
        if (!$user || $user->isSupplier() || $user->isDeveloper()) {
            return null;
        }

        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Quick log methods
    public static function logLogin()
    {
        $user = auth()->user();
        if (!$user || $user->isSupplier() || $user->isDeveloper()) {
            return null;
        }

        return self::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'module' => 'Auth',
            'description' => "User {$user->name} ({$user->role}) logged in",
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logLogout()
    {
        $user = auth()->user();
        if (!$user || $user->isSupplier() || $user->isDeveloper()) {
            return null;
        }

        return self::create([
            'user_id' => $user->id,
            'action' => 'LOGOUT',
            'module' => 'Auth',
            'description' => "User {$user->name} ({$user->role}) logged out",
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Scope for filtering
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Get action badge color
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'LOGIN' => 'emerald',
            'LOGOUT' => 'slate',
            'CREATE' => 'blue',
            'UPDATE' => 'amber',
            'DELETE' => 'rose',
            'COMPLETE' => 'emerald',
            'CANCEL' => 'rose',
            default => 'slate',
        };
    }

    // Get module icon
    public function getModuleIconAttribute()
    {
        return match($this->module) {
            'Auth' => 'bx-log-in',
            'Transaction' => 'bx-receipt',
            'ManualTransaction' => 'bx-wallet',
            'Product' => 'bx-package',
            'Category' => 'bx-category',
            'User' => 'bx-user',
            default => 'bx-history',
        };
    }
}
