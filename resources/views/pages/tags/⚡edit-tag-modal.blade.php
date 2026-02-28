<?php

use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public ?Tag $tag = null;
    public string $name = '';
    public string $description = '';

    /**
     * Open the modal with tag data.
     */
    #[On('openEditTagModal')]
    public function open(int $tagId): void
    {
        $this->tag = Tag::findOrFail($tagId);
        $this->name = $this->tag->name;
        $this->description = $this->tag->description ?? '';

        $this->resetValidation();
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['tag', 'name', 'description']);
    }

    /**
     * Update the tag.
     */
    public function updateTag(): void
    {
        if (!$this->tag) {
            return;
        }

        $validated = Validator::make([
            'name' => $this->name,
            'description' => $this->description,
        ], [
            'name' => ['required', 'string', 'max:255', 'unique:tags,name,' . $this->tag->id],
            'description' => ['nullable', 'string', 'max:500'],
        ])->validate();

        $this->tag->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $this->close();
        
        $this->dispatch('tag-updated');
        $this->dispatch('refresh-tag-list');
    }
}; ?>

<flux:modal :show="$show" name="edit-tag-modal" class="md:w-[600px] space-y-6" variant="flyout" wire:model="show">
    @if($tag)
        <div>
            <flux:heading size="lg">{{ __('Edit Tag') }}</flux:heading>
            <flux:subheading>{{ __('Update tag information') }}</flux:subheading>
        </div>

        <form wire:submit="updateTag" class="space-y-6">
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
                    {{ __('Update Tag') }}
                </flux:button>
            </div>
        </form>
    @endif
</flux:modal>
