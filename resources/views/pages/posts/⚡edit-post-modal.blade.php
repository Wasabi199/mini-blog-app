<?php

use App\Models\Post;
use App\Models\Tag;
use App\PostStatus;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public bool $show = false;
    public ?Post $post = null;
    public string $title = '';
    public string $excerpt = '';
    public string $content = '';
    public int $status = 0;
    public bool $publishNow = false;
    public string $publishedAt = '';
    public array $selectedTags = [];
    public $thumbnail;

    /**
     * Open the modal with post data.
     */
    #[On('openEditPostModal')]
    public function open(int $postId): void
    {
        $this->post = Post::with('tags')->findOrFail($postId);
        $this->title = $this->post->title;
        $this->excerpt = $this->post->excerpt ?? '';
        $this->content = $this->post->content;
        $this->status = $this->post->status->value;
        $this->publishNow = $this->post->published_at !== null && $this->post->published_at->isPast();
        $this->publishedAt = $this->post->published_at && $this->post->published_at->isFuture() 
            ? $this->post->published_at->format('Y-m-d\TH:i') 
            : '';
        $this->selectedTags = $this->post->tags->pluck('id')->toArray();

        $this->resetValidation();
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
        $this->reset(['post', 'title', 'excerpt', 'content', 'status', 'publishNow', 'publishedAt', 'selectedTags', 'thumbnail']);
    }

    /**
     * Get all available tags.
     */
    #[Computed]
    public function availableTags()
    {
        return Tag::orderBy('name')->get();
    }

    /**
     * Update the post.
     */
    public function updatePost(): void
    {
        if (!$this->post) {
            return;
        }

        $validated = Validator::make([
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'publishedAt' => $this->publishedAt,
            'selectedTags' => $this->selectedTags,
            'thumbnail' => $this->thumbnail,
        ], [
            'title' => ['required', 'string', 'max:255', 'unique:posts,title,' . $this->post->id],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:0,1'],
            'publishedAt' => ['nullable', 'date', 'after_or_equal:now'],
            'selectedTags' => ['nullable', 'array'],
            'selectedTags.*' => ['exists:tags,id'],
            'thumbnail' => ['nullable', 'image', 'max:2048'], // 2MB max
        ])->validate();

        // Determine published_at based on status and user input
        $publishedAt = null;
        if ($this->status == PostStatus::PUBLISHED->value) {
            if ($this->publishNow) {
                $publishedAt = $this->post->published_at ?? now();
            }
        } elseif ($this->status == PostStatus::DRAFT->value && !empty($validated['publishedAt'])) {
            $publishedAt = $validated['publishedAt'];
        }

        $this->post->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);

        // Sync tags
        $this->post->tags()->sync($validated['selectedTags'] ?? []);

        // Upload thumbnail if provided
        if ($this->thumbnail) {
            $this->post->uploadThumbnail($this->thumbnail);
        }

        $this->close();
        
        $this->dispatch('post-updated');
        $this->dispatch('refresh-post-list');
    }
}; ?>

<flux:modal :show="$show" name="edit-post-modal" class="md:w-[600px] space-y-6" variant="flyout" wire:model="show">
    @if($post)
        <div>
            <flux:heading size="lg">{{ __('Edit Post') }}</flux:heading>
            <flux:subheading>{{ __('Update your blog post') }}</flux:subheading>
        </div>

        <form wire:submit="updatePost" class="space-y-6">
            <flux:input wire:model="title" label="{{ __('Title') }}" placeholder="{{ __('Enter post title...') }}"
                required :error="$errors->first('title')" />

            <flux:textarea wire:model="excerpt" label="{{ __('Excerpt') }}" 
                placeholder="{{ __('Brief summary of your post (optional)...') }}" 
                rows="2" :error="$errors->first('excerpt')" />

            <flux:textarea wire:model="content" label="{{ __('Content') }}" 
                placeholder="{{ __('Write your post content...') }}" 
                rows="10" 
                required :error="$errors->first('content')" />

            <div>
                <flux:label>{{ __('Thumbnail Image') }}</flux:label>
                @if ($post->thumbnail())
                    <div class="mb-2">
                        <img src="{{ $post->thumbnail_url }}" class="h-32 w-auto rounded-lg object-cover">
                        <p class="text-sm text-gray-500 mt-1">{{ __('Current thumbnail') }}</p>
                    </div>
                @endif
                <input type="file" wire:model="thumbnail" accept="image/*" 
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($thumbnail)
                    <div class="mt-2">
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="h-32 w-auto rounded-lg object-cover">
                        <p class="text-sm text-gray-500 mt-1">{{ __('New thumbnail preview') }}</p>
                    </div>
                @endif
                @error('thumbnail')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ __('Tags') }}</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->availableTags as $tag)
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="selectedTags"
                                    value="{{ $tag->id }}"
                                    class="rounded border-zinc-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-zinc-600 dark:bg-zinc-800"
                                />
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <flux:select wire:model.live="status" label="{{ __('Status') }}" required>
                    <option value="{{ PostStatus::DRAFT->value }}">{{ __('Draft') }}</option>
                    <option value="{{ PostStatus::PUBLISHED->value }}">{{ __('Published') }}</option>
                </flux:select>

                @if($status == PostStatus::PUBLISHED->value)
                    <flux:checkbox wire:model="publishNow" label="{{ __('Publish immediately') }}" />
                @endif

                @if($status == PostStatus::DRAFT->value)
                    <flux:input 
                        wire:model="publishedAt" 
                        type="datetime-local" 
                        label="{{ __('Schedule Publish Date (Optional)') }}" 
                        placeholder="{{ __('Select date and time...') }}"
                        :error="$errors->first('publishedAt')" 
                    />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 -mt-2">{{ __('Leave empty to keep as draft. Post will auto-publish at the scheduled time.') }}</p>
                @endif
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost" type="button">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Update Post') }}
                </flux:button>
            </div>
        </form>
    @endif
</flux:modal>
