<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'isActive',
        'lastLoginAt',
        'mustChangePassword',
        'passwordChangedAt',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'isActive' => 'boolean',
            'mustChangePassword' => 'boolean',
            'lastLoginAt' => 'datetime',
            'passwordChangedAt' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'userId');
    }

    public function cashierShifts()
    {
        return $this->hasMany(CashierShift::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'userId');
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdmin(): bool
    {
        return $this->isActive && in_array($this->role, [
            'SUPER_ADMIN',
            'ADMIN',
            'KASIR',
            'DEVELOPER'
        ]);
    }

    /**
     * Role Check Methods
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPER_ADMIN';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN']);
    }

    public function isKasir(): bool
    {
        return $this->role === 'KASIR';
    }

    public function isMember(): bool
    {
        return $this->role === 'MEMBER';
    }

    public function isSupplier(): bool
    {
        return $this->role === 'SUPPLIER';
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'DEVELOPER';
    }

    /**
     * Permission Check Methods
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'DEVELOPER']);
    }

    public function canManageProducts(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN', 'DEVELOPER']);
    }

    public function canManageSuppliers(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN']);
    }

    public function canProcessTransactions(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN', 'KASIR']);
    }

    public function canApproveLoans(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN']);
    }

    public function canViewReports(): bool
    {
        return in_array($this->role, ['SUPER_ADMIN', 'ADMIN', 'DEVELOPER']);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['SUPER_ADMIN', 'ADMIN']);
    }

    /**
     * Mutators
     */
    public function updateLastLogin()
    {
        $this->update(['lastLoginAt' => now()]);
    }

    public function changePassword($newPassword)
    {
        $this->update([
            'password' => $newPassword,
            'mustChangePassword' => false,
            'passwordChangedAt' => now(),
        ]);
    }
}
