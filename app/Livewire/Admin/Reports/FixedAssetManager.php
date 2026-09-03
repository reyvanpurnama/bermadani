<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\FixedAsset;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.admin')]
class FixedAssetManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $assetId = null;

    // Form fields
    public string $name = '';
    public string $category = 'PERALATAN';
    public string $acquisition_date = '';
    public $acquisition_cost = 0;
    public int $useful_life_months = 60;
    public $salvage_value = 0;
    public string $notes = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:PERALATAN,KENDARAAN,BANGUNAN,LAINNYA',
        'acquisition_date' => 'required|date',
        'acquisition_cost' => 'required|numeric|min:0',
        'useful_life_months' => 'required|integer|min:1',
        'salvage_value' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->acquisition_date = date('Y-m-d');
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->assetId = null;
        $this->name = '';
        $this->category = 'PERALATAN';
        $this->acquisition_date = date('Y-m-d');
        $this->acquisition_cost = 0;
        $this->useful_life_months = 60;
        $this->salvage_value = 0;
        $this->notes = '';
        $this->resetValidation();
    }

    public function edit(int $id)
    {
        $asset = FixedAsset::findOrFail($id);
        $this->assetId = $asset->id;
        $this->name = $asset->name;
        $this->category = $asset->category;
        $this->acquisition_date = $asset->acquisition_date ? $asset->acquisition_date->format('Y-m-d') : date('Y-m-d');
        $this->acquisition_cost = $asset->acquisition_cost;
        $this->useful_life_months = $asset->useful_life_months;
        $this->salvage_value = $asset->salvage_value;
        $this->notes = $asset->notes ?? '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        FixedAsset::updateOrCreate(
            ['id' => $this->assetId],
            [
                'name' => $this->name,
                'category' => $this->category,
                'acquisition_date' => $this->acquisition_date,
                'acquisition_cost' => $this->acquisition_cost,
                'useful_life_months' => $this->useful_life_months,
                'salvage_value' => $this->salvage_value ?? 0,
                'notes' => $this->notes,
                'created_by' => Auth::id(),
            ]
        );

        session()->flash('message', $this->assetId ? 'Aset tetap berhasil diperbarui!' : 'Aset tetap baru berhasil ditambahkan!');
        $this->closeModal();
    }

    public function delete(int $id)
    {
        FixedAsset::findOrFail($id)->delete();
        session()->flash('message', 'Aset tetap berhasil dihapus.');
    }

    public function render()
    {
        $assets = FixedAsset::orderBy('acquisition_date', 'desc')->paginate(10);
        $totalCost = FixedAsset::where('status', 'ACTIVE')->sum('acquisition_cost');
        $totalDepreciated = FixedAsset::where('status', 'ACTIVE')->get()->sum('accumulated_depreciation');
        $totalNetBook = $totalCost - $totalDepreciated;

        return view('livewire.admin.reports.fixed-asset-manager', [
            'assets' => $assets,
            'totalCost' => $totalCost,
            'totalDepreciated' => $totalDepreciated,
            'totalNetBook' => $totalNetBook,
        ]);
    }
}
