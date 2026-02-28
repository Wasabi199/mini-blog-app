<?php

use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public string $name = '';
    public string $description = '';

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['name', 'description']);
    }

    /**
     * Create a new tag.
     */
    public function createTag(): void
    {
        $validated = Validator::make([
            'name' => $this->name,
            'description' => $this->description,
        ], [
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ])->validate();

        Tag::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $this->close();
        
        $this->dispatch('tag-created');
        $this->dispatch('refresh-tag-list');
    }
}; ?>

<flux:modal :show="$show" name="create-tag-modal" class="md:w-[600px] space-y-6" variant="flyout" wire:model="show">
    <div>
        <flux:heading size="lg">{{ __('Create New Tag') }}</flux:heading>
        <flux:subheading>{{ __('Add a new tag for organizing posts') }}</flux:subheading>
    </div>

    <form wire:submit="createTag" class="space-y-6">
        <flux:input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('Enter tag name...') }}"
            required :error="$errors->first('name')" />

        <flux:textarea wire:model="description" label="{{ __('Description') }}" 
            placeholder="{{ __('Brief description of the tag (optional)...') }}" 
            rows="3" :error="$errors->first('description')" />

        <div class="flex gap-2 justify-end">
            <flux:modal.close>
                <flux:button variant="ghost" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary">
                {{ __('Create Tag') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
