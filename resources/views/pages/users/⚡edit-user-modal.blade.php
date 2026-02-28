<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public bool $show = false;
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $profile_picture;

    /**
     * Open the modal with user data.
     */
    #[On('openEditUserModal')]
    public function open(int $userId): void
    {
        $this->user = User::findOrFail($userId);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->resetValidation();
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['user', 'name', 'email', 'password', 'password_confirmation', 'profile_picture']);
    }

    /**
     * Update the user.
     */
    public function updateUser(): void
    {
        if (!$this->user) {
            return;
        }

        $validated = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'profile_picture' => $this->profile_picture,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // 2MB max
        ])->validate();

        $this->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        if (!empty($validated['password'])) {
            $this->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Upload profile picture if provided
        if ($this->profile_picture) {
            $this->user->uploadProfilePicture($this->profile_picture);
        }

        $this->close();
        
        $this->dispatch('user-updated');
        $this->dispatch('refresh-user-list');
    }
}; ?>

<flux:modal :show="$show" name="edit-user-modal" class="md:w-[600px] space-y-6" variant="flyout" wire:model="show">
    @if($user)
        <div>
            <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
            <flux:subheading>{{ __('Update user information') }}</flux:subheading>
        </div>

        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:label>{{ __('Profile Picture') }}</flux:label>
                @if ($user->profilePicture())
                    <div class="mb-2">
                        <img src="{{ $user->profile_picture_url }}" class="h-20 w-20 rounded-full object-cover">
                        <p class="text-sm text-gray-500 mt-1">{{ __('Current profile picture') }}</p>
                    </div>
                @endif
                <input type="file" wire:model="profile_picture" accept="image/*" 
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($profile_picture)
                    <div class="mt-2">
                        <img src="{{ $profile_picture->temporaryUrl() }}" class="h-20 w-20 rounded-full object-cover">
                        <p class="text-sm text-gray-500 mt-1">{{ __('New profile picture preview') }}</p>
                    </div>
                @endif
                @error('profile_picture')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            <flux:input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('Enter user name...') }}"
                required :error="$errors->first('name')" />

            <flux:input wire:model="email" label="{{ __('Email') }}" type="email"
                placeholder="{{ __('Enter email address...') }}" 
                required :error="$errors->first('email')" />

            <flux:input wire:model="password" label="{{ __('New Password (optional)') }}" type="password"
                placeholder="{{ __('Enter new password...') }}" 
                :error="$errors->first('password')" />

            <flux:input wire:model="password_confirmation" label="{{ __('Confirm New Password') }}" type="password"
                placeholder="{{ __('Confirm new password...') }}" 
                :error="$errors->first('password_confirmation')" />

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost" type="button">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Update User') }}
                </flux:button>
            </div>
        </form>
    @endif
</flux:modal>