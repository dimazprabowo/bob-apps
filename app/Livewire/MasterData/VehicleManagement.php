<?php

namespace App\Livewire\MasterData;

use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use App\Livewire\Traits\HasNotification;
use App\Models\Vehicle;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class VehicleManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public bool $filterChanged = false;

    public $showModal = false;
    public $editMode = false;
    public $vehicleId;
    public $name;
    public $plate_number;
    public $category = 'operasional_harian';
    public $status = 'tersedia';
    public $contract_date;
    public $contract_expiry;
    public $contract_company;
    public $tax_expiry;
    public $stnk_expiry;
    public $image;
    public $description;

    public $showDeleteModal = false;
    public $deletingVehicleId;
    public $deletingVehicleName;

    public function mount()
    {
        $this->authorize('viewAny', Vehicle::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'plate_number' => ['required', 'string', 'max:20', $this->editMode ? 'unique:vehicles,plate_number,' . $this->vehicleId : 'unique:vehicles,plate_number'],
            'category' => 'required|string|in:' . implode(',', VehicleCategory::values()),
            'status' => 'required|string|in:' . implode(',', VehicleStatus::values()),
            'contract_date' => 'nullable|date',
            'contract_expiry' => 'nullable|date',
            'contract_company' => 'nullable|string|max:255',
            'tax_expiry' => 'nullable|date',
            'stnk_expiry' => 'nullable|date',
            'image' => 'nullable|image|max:5120',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'name' => 'nama kendaraan',
            'plate_number' => 'plat nomor',
            'category' => 'kategori',
            'status' => 'status',
            'tax_expiry' => 'tanggal pajak',
            'stnk_expiry' => 'tanggal STNK',
            'image' => 'gambar kendaraan',
        ];
    }

    public function updatingSearch() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingStatusFilter() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingCategoryFilter() { $this->resetPage(); $this->filterChanged = true; }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->categoryFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(VehicleStatus::cases())->map(fn ($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    }

    public function getCategoryOptionsProperty(): array
    {
        return collect(VehicleCategory::cases())->map(fn ($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    }

    public function create()
    {
        $this->authorize('create', Vehicle::class);
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->authorize('update', $vehicle);

        $this->vehicleId = $vehicle->id;
        $this->name = $vehicle->name;
        $this->plate_number = $vehicle->plate_number;
        $this->category = $vehicle->category->value;
        $this->status = $vehicle->status->value;
        $this->contract_date = $vehicle->contract_date?->format('Y-m-d');
        $this->contract_expiry = $vehicle->contract_expiry?->format('Y-m-d');
        $this->contract_company = $vehicle->contract_company;
        $this->tax_expiry = $vehicle->tax_expiry?->format('Y-m-d');
        $this->stnk_expiry = $vehicle->stnk_expiry?->format('Y-m-d');
        $this->description = $vehicle->description;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        try {
            $data = [
                'name' => $this->name,
                'plate_number' => strtoupper($this->plate_number),
                'category' => $this->category,
                'status' => $this->status,
                'contract_date' => $this->contract_date,
                'contract_expiry' => $this->contract_expiry,
                'contract_company' => $this->contract_company,
                'tax_expiry' => $this->tax_expiry,
                'stnk_expiry' => $this->stnk_expiry,
                'description' => $this->description,
            ];

            if ($this->image) {
                $env = config('app.env');
                $timestamp = now()->format('YmdHis');
                $fileName = $this->image->getClientOriginalName();
                $data['image'] = $this->image->storeAs(
                    "{$env}-master-data-armada-{$this->plate_number}-{$fileName}-{$timestamp}",
                    'public'
                );
            }

            if ($this->editMode) {
                $vehicle = Vehicle::findOrFail($this->vehicleId);
                $this->authorize('update', $vehicle);
                $vehicle->update($data);
                $message = 'Armada berhasil diupdate!';
            } else {
                $this->authorize('create', Vehicle::class);
                Vehicle::create($data);
                $message = 'Armada berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->deletingVehicleId = $id;
        $this->deletingVehicleName = $vehicle->name;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $vehicle = Vehicle::findOrFail($this->deletingVehicleId);
            $this->authorize('delete', $vehicle);
            $vehicle->delete();
            $this->notifySuccess('Armada berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'vehicleId', 'name', 'plate_number', 'category', 'status',
            'contract_date', 'contract_expiry', 'contract_company',
            'tax_expiry', 'stnk_expiry', 'image', 'description',
        ]);
        $this->category = VehicleCategory::OperationalDaily->value;
        $this->status = VehicleStatus::Available->value;
    }

    public function render()
    {
        $query = Vehicle::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('plate_number', 'like', "%{$this->search}%")
                  ->orWhere('contract_company', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        $vehicles = $query->orderBy('name')->paginate(15);

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$vehicles->total()} data armada.");
            $this->filterChanged = false;
        }

        return view('livewire.master-data.vehicle-management', ['vehicles' => $vehicles]);
    }
}
