<?php

namespace App\Livewire\Developer;

use App\Models\WorkLog;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class WorkLogManager extends Component
{
    use WithPagination;

    // Form inputs
    public $developerName;
    public $date;
    public $startTime;
    public $endTime;
    public $hoursWorked;
    public $description;

    // Filters
    public $filterMonth;
    public $filterYear;
    public $filterDeveloper = '';

    // Modal
    public $showForm = false;
    public $editingId = null;

    protected $rules = [
        'developerName' => 'required|string|min:2|max:100',
        'date' => 'required|date',
        'hoursWorked' => 'required|numeric|min:0.5|max:24',
        'description' => 'required|string|min:5|max:500',
        'startTime' => 'nullable',
        'endTime' => 'nullable',
    ];

    public function mount()
    {
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
        $this->date = now()->format('Y-m-d');
        $this->developerName = '';
    }

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->developerName = '';
        $this->date = now()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->hoursWorked = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function editLog($id)
    {
        $log = WorkLog::where('status', 'PENDING')->find($id);

        if (!$log) {
            session()->flash('error', 'Log tidak ditemukan atau sudah diproses.');
            return;
        }

        $this->editingId = $id;
        $this->developerName = $log->developerName;
        $this->date = $log->date->format('Y-m-d');
        $this->startTime = $log->startTime;
        $this->endTime = $log->endTime;
        $this->hoursWorked = $log->hoursWorked;
        $this->description = $log->description;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'userId' => auth()->id(),
            'developerName' => $this->developerName,
            'date' => $this->date,
            'startTime' => $this->startTime ?: null,
            'endTime' => $this->endTime ?: null,
            'hoursWorked' => $this->hoursWorked,
            'description' => $this->description,
            'hourlyRate' => 6000.00,
            'status' => 'PENDING',
        ];

        if ($this->editingId) {
            $log = WorkLog::where('status', 'PENDING')->find($this->editingId);

            if ($log) {
                $log->update($data);
                session()->flash('success', 'Log kerja berhasil diperbarui.');
            }
        } else {
            WorkLog::create($data);
            session()->flash('success', 'Log kerja berhasil disimpan.');
        }

        $this->closeForm();
    }

    public function deleteLog($id)
    {
        $log = WorkLog::where('status', 'PENDING')->find($id);

        if ($log) {
            $log->delete();
            session()->flash('success', 'Log kerja berhasil dihapus.');
        } else {
            session()->flash('error', 'Hanya log dengan status PENDING yang bisa dihapus.');
        }
    }

    public function getDeveloperNamesProperty()
    {
        return WorkLog::select('developerName')
            ->distinct()
            ->orderBy('developerName')
            ->pluck('developerName');
    }

    public function getLogsProperty()
    {
        return WorkLog::whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = WorkLog::whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper));

        $totalHours = (clone $query)->sum('hoursWorked');
        $totalAmount = (clone $query)->sum('totalAmount');
        $pending = (clone $query)->where('status', 'PENDING')->sum('totalAmount');
        $approved = (clone $query)->where('status', 'APPROVED')->sum('totalAmount');
        $paid = (clone $query)->where('status', 'PAID')->sum('totalAmount');

        return [
            'totalHours' => $totalHours,
            'totalAmount' => $totalAmount,
            'pending' => $pending,
            'approved' => $approved,
            'paid' => $paid,
        ];
    }

    public function render()
    {
        return view('livewire.developer.work-log-manager', [
            'logs' => $this->logs,
            'stats' => $this->stats,
            'developerNames' => $this->developerNames,
        ])->layout('layouts.admin');
    }
}
