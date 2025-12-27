<?php

namespace App\Livewire;

use App\Models\Service;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ServiceController extends Component
{
    use AuthorizesRequests, Toast, WithPagination;

    public $name;

    public $description;

    public $price = 0;

    public $duration = 0;

    public $serviceId;

    public $searchInput = '';

    public $showDeleteModal = false;

    public $deleteServiceId = null;

    public $expanded = [];

    protected $updatesQueryString = ['searchInput' => ['except' => '']];

    public function mount()
    {
        $this->authorize('viewAny', Service::class);
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Reset pagination when search changes
    }

    public function render()
    {
        $query = Service::latest();

        if ($this->searchInput) {
            $query->where('name', 'like', '%'.$this->searchInput.'%');
        }

        return view('livewire.service.service-index', [
            'services' => $query->paginate(10),
            'headers' => $this->getHeaders(),
        ]);
    }

    private function getHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Service Name'],
            ['key' => 'price', 'label' => 'Price', 'class' => 'hidden sm:table-cell'],
        ];
    }

    // --- FORM ---
    public function loadService(Service $service)
    {
        $this->authorize('update', $service);
        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->description = $service->description;
        $this->price = $service->price;
        $this->duration = $service->duration;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|unique:services,name,'.($this->serviceId ?? 'NULL'),
            'description' => 'string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
        ]);

        if ($this->serviceId) {
            Service::find($this->serviceId)->update($validated);
            $this->success('Service updated successfully.');
        } else {
            Service::create($validated);
            $this->success('Service created successfully.');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->serviceId = null;
        $this->name = '';
        $this->description = '';
        $this->price = 0;
        $this->duration = 0;
    }

    // --- DELETE MODAL ---
    public function delete()
    {
        $this->authorize('delete', Service::findOrFail($this->deleteServiceId));
        Service::findOrFail($this->deleteServiceId)->delete();
        $this->success('Service deleted successfully.');
        $this->resetForm();
        $this->closeDeleteModal(); // This will close the modal and reset
    }

    public function confirmDelete($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $this->deleteServiceId = $service->id;
        $this->name = $service->name;
        $this->showDeleteModal = true; // only set here
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteServiceId = null;
        $this->name = '';
    }
}
