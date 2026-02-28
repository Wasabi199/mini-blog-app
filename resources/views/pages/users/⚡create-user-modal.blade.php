<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public bool $show = false;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $profile_picture;

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'profile_picture']);
    }

    /**
     * Create a new user.
     */
    public function createUser(): void
    {
        $validated = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'profile_picture' => $this->profile_picture,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // 2MB max
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Upload profile picture if provided
        if ($this->profile_picture) {
            $user->uploadProfilePicture($this->profile_picture);
        }

        $this->close();
        
        $this->dispatch('user-created');
        $this->dispatch('refresh-user-list');
    }
}; ?>

<flux:modal :show="$show" name="create-user-modal" class="md:w-[600px] space-y-6" variant="flyout" wire:model="show">
    <div>
        <flux:heading size="lg">{{ __('Create New User') }}</flux:heading>
        <flux:subheading>{{ __('Add a new user to the system') }}</flux:subheading>
    </div>

    <form wire:submit="createUser" class="space-y-6">
        <div>
            <flux:label>{{ __('Profile Picture') }}</flux:label>
            <input type="file" wire:model="profile_picture" accept="image/*" 
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            @if ($profile_picture)
                <div class="mt-2">
                    <img src="{{ $profile_picture->temporaryUrl() }}" class="h-20 w-20 rounded-full object-cover">
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

        <flux:input wire:model="password" label="{{ __('Password') }}" type="password"
            placeholder="{{ __('Enter password...') }}" 
            required :error="$errors->first('password')" />

        <flux:input wire:model="password_confirmation" label="{{ __('Confirm Password') }}" type="password"
            placeholder="{{ __('Confirm password...') }}" 
            required :error="$errors->first('password_confirmation')" />

        <div class="flex gap-2 justify-end">
            <flux:modal.close>
                <flux:button variant="ghost" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary">
                {{ __('Create User') }}
            </flux:button>
        </div>
    </form>
</flux:modal>