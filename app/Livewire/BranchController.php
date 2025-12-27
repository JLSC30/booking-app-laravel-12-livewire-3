<?php

namespace App\Livewire;

use App\Models\Branch;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class BranchController extends Component
{
    use AuthorizesRequests, Toast, WithPagination;

    public $name;

    public $address;

    public $is_active = true;

    public $branchId = null;

    public $searchInput = '';

    public $showDeleteModal = false;

    public $deleteBranchId = null;

    public $expanded = [];

    protected $updatesQueryString = ['searchInput' => ['except' => '']];

    public function mount()
    {
        $this->authorize('viewAny', Branch::class);
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Reset pagination when search changes
    }

    public function render()
    {
        $query = Branch::latest();

        if ($this->searchInput) {
            $query->where('name', 'like', '%'.$this->searchInput.'%');
        }

        return view('livewire.branch.branch-index', [
            'branches' => $query->paginate(10),
            'headers' => $this->getHeaders(),
        ]);
    }

    private function getHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Branch Name'],
        ];
    }

    // --- FORM ---
    public function loadBranch(Branch $branch)
    {
        $this->authorize('update', $branch);
        $this->branchId = $branch->id;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->is_active = $branch->is_active;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|unique:branches,name,'.($this->branchId ?? 'NULL'),
            'address' => 'string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;

        if ($this->branchId) {
            Branch::find($this->branchId)->update($validated);
            $this->success('Branch updated successfully.');
        } else {
            Branch::create($validated);
            $this->success('Branch created successfully.');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->branchId = null;
        $this->name = '';
        $this->address = '';
        $this->is_active = true;
    }

    // --- DELETE MODAL ---
    public function delete()
    {
        $this->authorize('delete', Branch::findOrFail($this->deleteBranchId));
        Branch::findOrFail($this->deleteBranchId)->delete();
        $this->success('Branch deleted successfully.');
        $this->resetForm();
        $this->closeDeleteModal(); // This will close the modal and reset
    }

    public function confirmDelete($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $this->deleteBranchId = $branch->id;
        $this->name = $branch->name;
        $this->showDeleteModal = true; // only set here
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteBranchId = null;
        $this->name = '';
    }
}
