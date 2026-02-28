<?php

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public ?User $user = null;

    /**
     * Open the modal with user data.
     */
    #[On('openDeleteUserModal')]
    public function open(int $userId): void
    {
        $this->user = User::findOrFail($userId);
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['user']);
    }

    /**
     * Delete the user.
     */
    public function deleteUser(): void
    {
        if (!$this->user) {
            return;
        }

        $this->user->delete();
        
        $this->close();
        
        $this->dispatch('user-deleted');
        $this->dispatch('refresh-user-list');
    }
}; ?>

<flux:modal :show="$show" name="delete-user-modal" class="md:w-96" wire:model="show">
    @if($user)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                <flux:subheading>{{ __('Are you sure you want to delete this user?') }}</flux:subheading>
            </div>

            <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $user->name }}</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ $user->email }}</p>
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('This action cannot be undone. The user will be permanently deleted.') }}
            </p>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button wire:click="deleteUser" variant="danger">
                    {{ __('Delete User') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>