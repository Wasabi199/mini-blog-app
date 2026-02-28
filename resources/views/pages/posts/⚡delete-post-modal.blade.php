<?php

use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public ?Post $post = null;

    /**
     * Open the modal with post data.
     */
    #[On('openDeletePostModal')]
    public function open(int $postId): void
    {
        $this->post = Post::findOrFail($postId);
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['post']);
    }

    /**
     * Delete the post.
     */
    public function deletePost(): void
    {
        if (!$this->post) {
            return;
        }

        $this->post->delete();
        
        $this->close();
        
        $this->dispatch('post-deleted');
        $this->dispatch('refresh-post-list');
    }
}; ?>

<flux:modal :show="$show" name="delete-post-modal" class="md:w-96" wire:model="show">
    @if($post)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Post') }}</flux:heading>
                <flux:subheading>{{ __('Are you sure you want to delete this post?') }}</flux:subheading>
            </div>

            <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $post->title }}</p>
                @if($post->excerpt)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ Str::limit($post->excerpt, 100) }}</p>
                @endif
            </div>

            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('This action cannot be undone. The post will be permanently deleted.') }}
            </p>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button wire:click="deletePost" variant="danger">
                    {{ __('Delete Post') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
