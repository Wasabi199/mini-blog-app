@php
    use App\PostStatus;
@endphp

<x-layouts::app :title="$post->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        {{-- Header with Back Button --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('posts.index') }}" class="text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white">
                    <flux:icon.arrow-left class="size-5" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</h1>
                    <div class="mt-1 flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <span>{{ __('By') }} {{ $post->user->name }}</span>
                        <span>•</span>
                        <span>{{ $post->created_at->format('M d, Y') }}</span>
                        @if($post->published_at)
                            <span>•</span>
                            <span>{{ __('Published') }} {{ $post->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-table.badge 
                    :label="$post->status->label()" 
                    :variant="$post->status === PostStatus::PUBLISHED ? 'success' : 'zinc'"
                    size="md" 
                />
                
                @can('update', $post)
                    <flux:button 
                        variant="ghost" 
                        size="sm"
                        onclick="Livewire.dispatch('openEditPostModal', { postId: {{ $post->id }} })"
                    >
                        {{ __('Edit') }}
                    </flux:button>
                @endcan

                @can('delete', $post)
                    <flux:button 
                        variant="danger" 
                        size="sm"
                        onclick="Livewire.dispatch('openDeletePostModal', { postId: {{ $post->id }} })"
                    >
                        {{ __('Delete') }}
                    </flux:button>
                @endcan
            </div>
        </div>

        {{-- Post Content --}}
        <div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <div class="p-8 max-w-4xl mx-auto">
                @if($post->excerpt)
                    <div class="mb-6 text-lg text-neutral-600 dark:text-neutral-400 italic border-l-4 border-neutral-300 dark:border-neutral-700 pl-4">
                        {{ $post->excerpt }}
                    </div>
                @endif

                <div class="prose prose-neutral dark:prose-invert max-w-none">
                    {!! nl2br(e($post->content)) !!}
                </div>

                {{-- Post Meta --}}
                <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400">
                        <div>
                            {{ __('Last updated') }}: {{ $post->updated_at->diffForHumans() }}
                        </div>
                        @if($post->deleted_at)
                            <div class="text-red-600 dark:text-red-400">
                                {{ __('Deleted') }}: {{ $post->deleted_at->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:pages::posts.edit-post-modal />
    <livewire:pages::posts.delete-post-modal />
</x-layouts::app>
