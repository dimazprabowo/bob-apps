<?php

namespace App\Livewire\MasterData;

use App\Enums\RoomStatus;
use App\Livewire\Traits\HasNotification;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RoomManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public bool $filterChanged = false;

    public $showModal = false;
    public $editMode = false;
    public $roomId;
    public $name;
    public $location;
    public $capacity = 10;
    public $facilities_input = '';
    public $image;
    public $status = 'tersedia';
    public $description;

    public $showDeleteModal = false;
    public $deletingRoomId;
    public $deletingRoomName;

    public function mount()
    {
        $this->authorize('viewAny', Room::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1|max:500',
            'facilities_input' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:5120',
            'status' => 'required|string|in:' . implode(',', RoomStatus::values()),
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'name' => 'nama ruangan',
            'location' => 'lokasi',
            'capacity' => 'kapasitas',
            'facilities_input' => 'fasilitas',
            'image' => 'gambar ruangan',
            'status' => 'status',
        ];
    }

    public function updatingSearch() { $this->resetPage(); $this->filterChanged = true; }
    public function updatingStatusFilter() { $this->resetPage(); $this->filterChanged = true; }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(RoomStatus::cases())->map(fn ($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    }

    public function create()
    {
        $this->authorize('create', Room::class);
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $this->authorize('update', $room);

        $this->roomId = $room->id;
        $this->name = $room->name;
        $this->location = $room->location;
        $this->capacity = $room->capacity;
        $this->facilities_input = $room->facilities ? implode(', ', $room->facilities) : '';
        $this->status = $room->status->value;
        $this->description = $room->description;

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
            $facilities = $this->facilities_input
                ? array_map('trim', explode(',', $this->facilities_input))
                : null;

            $data = [
                'name' => $this->name,
                'location' => $this->location,
                'capacity' => $this->capacity,
                'facilities' => $facilities,
                'status' => $this->status,
                'description' => $this->description,
            ];

            if ($this->image) {
                $env = config('app.env');
                $timestamp = now()->format('YmdHis');
                $fileName = $this->image->getClientOriginalName();
                $data['image'] = $this->image->storeAs(
                    "{$env}-master-data-ruangan-{$this->name}-{$fileName}-{$timestamp}",
                    'public'
                );
            }

            if ($this->editMode) {
                $room = Room::findOrFail($this->roomId);
                $this->authorize('update', $room);
                $room->update($data);
                $message = 'Ruangan berhasil diupdate!';
            } else {
                $this->authorize('create', Room::class);
                Room::create($data);
                $message = 'Ruangan berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem.');
        }
    }

    public function confirmDelete($id)
    {
        $room = Room::findOrFail($id);
        $this->deletingRoomId = $id;
        $this->deletingRoomName = $room->name;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $room = Room::findOrFail($this->deletingRoomId);
            $this->authorize('delete', $room);
            $room->delete();
            $this->notifySuccess('Ruangan berhasil dihapus!');
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
            'roomId', 'name', 'location', 'capacity', 'facilities_input',
            'image', 'status', 'description',
        ]);
        $this->capacity = 10;
        $this->status = RoomStatus::Available->value;
    }

    public function render()
    {
        $query = Room::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $rooms = $query->orderBy('name')->paginate(15);

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$rooms->total()} data ruangan.");
            $this->filterChanged = false;
        }

        return view('livewire.master-data.room-management', ['rooms' => $rooms]);
    }
}
