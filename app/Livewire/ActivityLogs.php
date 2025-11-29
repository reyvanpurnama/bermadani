<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    public $filterUser = '';
    public $filterModule = '';
    public $filterAction = '';
    public $filterDate = '';

    public function updatingFilterUser()
    {
        $this->resetPage();
    }

    public function updatingFilterModule()
    {
        $this->resetPage();
    }

    public function updatingFilterAction()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['filterUser', 'filterModule', 'filterAction', 'filterDate']);
    }

    public function getUsersProperty()
    {
        return User::whereIn('role', ['SUPER_ADMIN', 'ADMIN', 'DEVELOPER', 'KASIR'])
            ->orderBy('name')
            ->get();
    }

    public function getModulesProperty()
    {
        return ['Auth', 'Transaction', 'ManualTransaction', 'Product', 'Category', 'User'];
    }

    public function getActionsProperty()
    {
        return ['LOGIN', 'LOGOUT', 'CREATE', 'UPDATE', 'DELETE', 'COMPLETE', 'CANCEL'];
    }

    public function getLogsProperty()
    {
        return ActivityLog::with('user')
            ->when($this->filterUser, fn($q) => $q->where('user_id', $this->filterUser))
            ->when($this->filterModule, fn($q) => $q->where('module', $this->filterModule))
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->when($this->filterDate, fn($q) => $q->whereDate('created_at', $this->filterDate))
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.activity-logs');
    }
}
