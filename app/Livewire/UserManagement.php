<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';

    // Form Data
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'KASIR';
    public $isActive = true;

    // Modal States
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $userToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($this->userId ? ',' . $this->userId : ''),
            'role' => 'required|in:ADMIN,KASIR',
            'isActive' => 'boolean',
        ];

        if (!$this->editMode || $this->password) {
            $rules['password'] = 'required|string|min:6';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'role.required' => 'Role wajib dipilih',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function canManage(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper();
    }

    public function openCreateModal()
    {
        if (!$this->canManage()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki akses untuk menambah user']);
            return;
        }

        $this->reset(['userId', 'name', 'email', 'password', 'role', 'isActive']);
        $this->role = 'KASIR';
        $this->isActive = true;
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        if (!$this->canManage()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengedit user']);
            return;
        }

        $user = User::findOrFail($id);

        // Cannot edit SUPER_ADMIN or DEVELOPER
        if (in_array($user->role, ['SUPER_ADMIN', 'DEVELOPER'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak dapat mengedit user ini']);
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->isActive = $user->isActive;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        if (!$this->canManage()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki akses']);
            return;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'isActive' => $this->isActive,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
            $data['mustChangePassword'] = true;
        }

        if ($this->editMode) {
            $user = User::findOrFail($this->userId);
            $user->update($data);

            ActivityLog::log(
                'UPDATE',
                'User',
                "Memperbarui user: {$user->name} ({$user->role})",
                $user
            );

            $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil diperbarui']);
        } else {
            $user = User::create($data);

            ActivityLog::log(
                'CREATE',
                'User',
                "Membuat user baru: {$user->name} ({$user->role})",
                $user
            );

            $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil ditambahkan']);
        }

        $this->showModal = false;
        $this->reset(['userId', 'name', 'email', 'password', 'role', 'isActive']);
    }

    public function confirmDelete($id)
    {
        if (!$this->canManage()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki akses untuk menghapus user']);
            return;
        }

        $user = User::findOrFail($id);

        // Cannot delete SUPER_ADMIN, DEVELOPER, or self
        if (in_array($user->role, ['SUPER_ADMIN', 'DEVELOPER']) || $user->id === auth()->id()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak dapat menghapus user ini']);
            return;
        }

        $this->userToDelete = $user;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (!$this->canManage() || !$this->userToDelete) {
            return;
        }

        $user = $this->userToDelete;

        ActivityLog::log(
            'DELETE',
            'User',
            "Menghapus user: {$user->name} ({$user->role})",
            null
        );

        $user->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'User berhasil dihapus']);
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function toggleStatus($id)
    {
        if (!$this->canManage()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki akses']);
            return;
        }

        $user = User::findOrFail($id);

        // Cannot toggle SUPER_ADMIN, DEVELOPER, or self
        if (in_array($user->role, ['SUPER_ADMIN', 'DEVELOPER']) || $user->id === auth()->id()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak dapat mengubah status user ini']);
            return;
        }

        $user->update(['isActive' => !$user->isActive]);

        $status = $user->isActive ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log(
            'UPDATE',
            'User',
            "User {$user->name} {$status}",
            $user
        );

        $this->dispatch('notify', ['type' => 'success', 'message' => "User berhasil {$status}"]);
    }

    public function render()
    {
        $users = User::query()
            ->where('role', '!=', 'DEVELOPER') // Hide developer from list
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('isActive', $this->filterStatus === '1');
            })
            ->orderByRaw("FIELD(role, 'SUPER_ADMIN', 'ADMIN', 'KASIR', 'SUPPLIER')")
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.user-management', [
            'users' => $users,
            'canManage' => $this->canManage(),
        ]);
    }
}
