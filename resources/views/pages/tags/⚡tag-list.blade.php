<?php

use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * Sort the tags.
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
     * Open edit modal for a tag.
     */
    public function editTag(int $tagId): void
    {
        $this->dispatch('openEditTagModal', tagId: $tagId);
    }

    /**
     * Open delete modal for a tag.
     */
    public function deleteTag(int $tagId): void
    {
        $this->dispatch('openDeleteTagModal', tagId: $tagId);
    }

    #[Computed]
    public function tags(): LengthAwarePaginator
    {
        return Tag::query()
            ->withCount('posts')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function hasTags(): bool
    {
        return Tag::count() > 0;
    }

    /**
     * Refresh the tag list.
     */
    #[On(['refresh-tag-list', 'tag-created', 'tag-updated', 'tag-deleted'])]
    public function refreshTagList(): void
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
                    placeholder="{{ __('Search tags...') }}"
                    model="search"
                />
            </div>

            <div class="flex items-center gap-3">
                <flux:modal.trigger name="create-tag-modal">
                    <flux:button variant="primary">
                        {{ __('New Tag') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <x-data-table :items="$this->tags" :columns="[
        [
            'key' => 'name',
            'label' => __('Name'),
            'sortable' => true,
            'slot' => function ($tag) {
                $name = e($tag->name);
                $description = $tag->description ? ' - ' . e(Str::limit($tag->description, 60)) : '';
                return $name . $description;
            },
        ],
        [
            'key' => 'slug',
            'label' => __('Slug'),
            'sortable' => true,
            'slot' => function ($tag) {
                return e($tag->slug);
            },
        ],
        [
            'key' => 'posts_count',
            'label' => __('Posts'),
            'sortable' => true,
            'slot' => function ($tag) {
                return view('components.table.badge', [
                    'label' => $tag->posts_count,
                    'variant' => 'zinc',
                    'size' => 'sm'
                ])->render();
            },
        ],
        [
            'key' => 'created_at',
            'label' => __('Created'),
            'sortable' => true,
            'slot' => function ($tag) {
                return e($tag->created_at->format('M d, Y'));
            },
        ],
    ]" :sortField="$sortField" :sortDirection="$sortDirection"
        :selectedItems="[]" :selectable="false" :hasItems="$this->hasTags"
        :emptyState="[
            'icon' => 'book-open-text',
            'title' => __('No tags yet'),
            'description' => __('Get started by creating your first tag.'),
            'action' => [
                'label' => __('New Tag'),
                'modal' => 'create-tag-modal',
            ],
        ]"
        :actions="true"
        editAction="editTag"
        deleteAction="deleteTag" />
</div>
