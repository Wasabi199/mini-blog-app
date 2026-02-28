<?php

use App\Models\Post;
use App\PostStatus;
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
    public string $statusFilter = 'all';

    /**
     * Sort the posts.
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
     * Open edit modal for a post.
     */
    public function editPost(int $postId): void
    {
        $this->dispatch('openEditPostModal', postId: $postId);
    }

    /**
     * Open delete modal for a post.
     */
    public function deletePost(int $postId): void
    {
        $this->dispatch('openDeletePostModal', postId: $postId);
    }

    /**
     * View post details.
     */
    public function viewPost(int $postId): void
    {
        $this->redirect(route('posts.show', $postId));
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->with('user')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'published') {
                    $query->where('status', PostStatus::PUBLISHED);
                } elseif ($this->statusFilter === 'draft') {
                    $query->where('status', PostStatus::DRAFT);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function hasPosts(): bool
    {
        return Post::count() > 0;
    }

    /**
     * Refresh the post list.
     */
    #[On(['refresh-post-list', 'post-created', 'post-updated', 'post-deleted'])]
    public function refreshPostList(): void
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
                    placeholder="{{ __('Search posts...') }}"
                    model="search"
                />
                
                <select wire:model.live="statusFilter" class="rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2 text-sm w-32">
                    <option value="all">{{ __('All') }}</option>
                    <option value="published">{{ __('Published') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <flux:modal.trigger name="create-post-modal">
                    <flux:button variant="primary">
                        {{ __('New Post') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <x-data-table :items="$this->posts" :columns="[
        [
            'key' => 'title',
            'label' => __('Title'),
            'sortable' => true,
            'slot' => function ($post) {
                $title = e($post->title);
                $excerpt = $post->excerpt ? ' - ' . e(Str::limit($post->excerpt, 60)) : '';
                return $title . $excerpt;
            },
        ],
        [
            'key' => 'user',
            'label' => __('Author'),
            'slot' => function ($post) {
                return view('components.table.avatar-cell', [
                    'name' => $post->user->name,
                    'subtitle' => null,
                    'size' => 'sm'
                ])->render();
            },
        ],
        [
            'key' => 'status',
            'label' => __('Status'),
            'sortable' => true,
            'slot' => function ($post) {
                return view('components.table.badge', [
                    'label' => $post->status->label(),
                    'variant' => $post->status === \App\PostStatus::PUBLISHED ? 'success' : 'zinc',
                    'size' => 'sm'
                ])->render();
            },
        ],
        [
            'key' => 'published_at',
            'label' => __('Published'),
            'sortable' => true,
            'slot' => function ($post) {
                return $post->published_at ? e($post->published_at->format('M d, Y')) : '—';
            },
        ],
        [
            'key' => 'created_at',
            'label' => __('Created'),
            'sortable' => true,
            'slot' => function ($post) {
                return e($post->created_at->format('M d, Y'));
            },
        ],
    ]" :sortField="$sortField" :sortDirection="$sortDirection"
        :selectedItems="[]" :selectable="false" :hasItems="$this->hasPosts"
        :emptyState="[
            'icon' => 'book-open-text',
            'title' => __('No posts yet'),
            'description' => __('Get started by creating your first blog post.'),
            'action' => [
                'label' => __('New Post'),
                'modal' => 'create-post-modal',
            ],
        ]"
        :actions="true"
        editAction="editPost"
        deleteAction="deletePost" />
</div>
