<?php

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    /**
     * Sort the users.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Open edit modal for a user.
     */
    public function editUser(int $userId): void
    {
        $this->dispatch('openEditUserModal', userId: $userId);
    }

    /**
     * Open delete modal for a user.
     */
    public function deleteUser(int $userId): void
    {
        $this->dispatch('openDeleteUserModal', userId: $userId);
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function hasUsers(): bool
    {
        return User::count() > 0;
    }

    /**
     * Refresh the user list.
     */
    #[On(['refresh-user-list', 'user-created', 'user-updated', 'user-deleted'])]
    public function refreshUserList(): void
    {
        $this->resetPage();
    }
}; ?>

<div class="h-full overflow-auto">
    {{-- Header with Search and Actions --}}
    <div class="border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-table.search-bar 
                    placeholder="{{ __('Search users...') }}"
                    model="search"
                />
            </div>

            <div class="flex items-center gap-3">
                <flux:modal.trigger name="create-user-modal">
                    <flux:button variant="primary">
                        {{ __('Add User') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <x-data-table :items="$this->users" :columns="[
        [
            'key' => 'name',
            'label' => __('Name'),
            'sortable' => true,
            'slot' => function ($user) {
                return view('components.table.avatar-cell', [
                    'name' => $user->name,
                    'subtitle' => null,
                    'size' => 'sm',
                    'imageUrl' => $user->profile_picture_url
                ])->render();
            },
        ],
        [
            'key' => 'email',
            'label' => __('Email'),
            'sortable' => true,
            'slot' => function ($user) {
                return view('components.table.email-cell', [
                    'email' => $user->email,
                    'verified' => (bool) $user->email_verified_at,
                ])->render();
            },
        ],
        [
            'key' => 'role',
            'label' => __('Role'),
            'slot' => function ($user) {
                return view('components.table.badge', [
                    'label' => __('User'),
                    'variant' => 'info',
                    'size' => 'sm'
                ])->render();
            },
        ],
        [
            'key' => 'created_at',
            'label' => __('Created'),
            'sortable' => true,
            'slot' => function ($user) {
                return e($user->created_at->format('M d, Y'));
            },
        ],
    ]" :sortField="$sortField" :sortDirection="$sortDirection"
        :selectedItems="[]" :selectable="false" :hasItems="$this->hasUsers"
        :emptyState="[
            'icon' => 'folder-git-2',
            'title' => __('No users yet'),
            'description' => __('Get started by creating your first user.'),
            'action' => [
                'label' => __('Add User'),
                'modal' => 'create-user-modal',
            ],
        ]"
        :actions="true"
        editAction="editUser"
        deleteAction="deleteUser" />
</div>
