<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class UserController extends Component
{
    use AuthorizesRequests, Toast, WithPagination;

    public $name;

    public $email;

    public $is_admin = false;

    public $password;

    public $password_confirmation;

    public $designation;

    public $must_change_password = true;

    public $userId;

    public $searchInput = '';

    public $showDeleteModal = false;

    public $deleteUserId = null;

    public ?int $branch_searchable_id = null;

    public Collection $branchesSearchable;

    public $expanded = [];

    protected $updatesQueryString = ['searchInput' => ['except' => '']];

    public function mount()
    {
        $this->authorize('viewAny', User::class);
        $this->branchesSearchable = collect();
        $this->search();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function search(string $value = '')
    {
        $this->searchBranches($value);
    }

    public function searchBranches(string $value = '')
    {
        $selectedOption = $this->branch_searchable_id
            ? Branch::where('id', $this->branch_searchable_id)->get()
            : collect();

        $this->branchesSearchable = Branch::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption)
            ->unique('id');
    }

    public function render()
    {
        $query = User::latest();

        if ($this->searchInput) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchInput.'%')
                    ->orWhere('email', 'like', '%'.$this->searchInput.'%');
            });
        }

        return view('livewire.user.user-index', [
            'users' => $query->paginate(10),
            'headers' => $this->getHeaders(),
        ]);
    }

    private function getHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'designation', 'label' => 'Designation', 'class' => 'hidden sm:table-cell'],
        ];
    }

    // --- FORM ---
    public function loadUser(User $user)
    {
        $this->authorize('update', $user);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_admin = $user->is_admin;
        $this->designation = $user->designation;
        $this->must_change_password = $user->must_change_password;

        // Load the branch using Eloquent relationship
        $this->branch_searchable_id = $user->branches->first()?->id;
    }

    protected function passwordRules(): array
    {
        return [
            'string',
            Password::default(),  // Keeps your strong password requirements (min 8, etc.)
            'confirmed',
        ];
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.($this->userId ?? 'NULL'),
            'is_admin' => 'nullable|boolean',
            'must_change_password' => 'nullable|boolean',
            'designation' => 'required|string',
            'branch_searchable_id' => 'nullable|exists:branches,id', // ← ADD THIS
        ];

        // Password rules
        if (! $this->userId) {
            $rules['password'] = ['required', ...$this->passwordRules()];
        } else {
            $rules['password'] = ['nullable', ...$this->passwordRules()];
        }

        $validated = $this->validate($rules); // Now includes branch_searchable_id if sent

        // Handle password
        $data = $validated;
        $data['is_admin'] = $data['is_admin'] ?? false;
        $data['must_change_password'] = $data['must_change_password'] ?? true;

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Get branch ID safely — prefer validated, fallback to property
        $branchId = $validated['branch_searchable_id'] ?? $this->branch_searchable_id;

        DB::transaction(function () use ($data, $branchId) {
            if ($this->userId) {
                // Update existing user
                $user = User::findOrFail($this->userId);
                $user->update($data);

                // Sync branch (handles both assign and detach)
                if ($branchId) {
                    $user->branches()->sync([$branchId]);
                } else {
                    $user->branches()->detach();
                }

                $this->success('User updated successfully.');
            } else {
                // Create new user
                $user = User::create($data);

                if ($branchId) {
                    $user->branches()->attach($branchId);
                }

                $this->success('User created successfully.');
            }
        });

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_admin = false;
        $this->must_change_password = true;
        $this->designation = '';
    }

    // --- DELETE MODAL ---
    public function delete()
    {
        $user = User::findOrFail($this->deleteUserId);
        $this->authorize('delete', $user);

        DB::transaction(function () use ($user) {
            // Detach all branches first
            $user->branches()->detach();

            // Delete user
            $user->delete();
        });

        $this->success('User deleted successfully.');
        $this->resetForm();
        $this->closeDeleteModal(); // This will close the modal and reset
    }

    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        $this->deleteUserId = $user->id;
        $this->name = $user->name;
        $this->showDeleteModal = true; // only set here
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
        $this->name = '';
    }
}
