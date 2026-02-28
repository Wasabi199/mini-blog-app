<?php

use App\Models\Tag;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public ?Tag $tag = null;

    /**
     * Open the modal with tag data.
     */
    #[On('openDeleteTagModal')]
    public function open(int $tagId): void
    {
        $this->tag = Tag::withCount('posts')->findOrFail($tagId);
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['tag']);
    }

    /**
     * Delete the tag.
     */
    public function deleteTag(): void
    {
        if (!$this->tag) {
            return;
        }

        $this->tag->delete();
        
        $this->close();
        
        $this->dispatch('tag-deleted');
        $this->dispatch('refresh-tag-list');
    }
}; ?>

<flux:modal :show="$show" name="delete-tag-modal" class="md:w-96" wire:model="show">
    @if($tag)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Tag') }}</flux:heading>
                <flux:subheading>{{ __('Are you sure you want to delete this tag?') }}</flux:subheading>
            </div>

            <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $tag->name }}</p>
                @if($tag->description)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ Str::limit($tag->description, 100) }}</p>
                @endif
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                    {{ __('Used in :count posts', ['count' => $tag->posts_count]) }}
                </p>
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('This action cannot be undone. The tag will be permanently deleted and removed from all posts.') }}
            </p>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button wire:click="deleteTag" variant="danger">
                    {{ __('Delete Tag') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
