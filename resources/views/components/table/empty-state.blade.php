@props([
    'icon' => 'folder-git-2',
    'title' => 'No data',
    'description' => 'Get started by adding new items.',
    'actionLabel' => null,
    'actionModal' => null,
    'actionRoute' => null,
    'actionClick' => null,
])

<div class="flex flex-col items-center justify-center py-12">
    <div class="text-center">
        <x-flux::icon :name="$icon" class="mx-auto h-12 w-12 text-neutral-400" />
        
        <h3 class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
            {{ __($title) }}
        </h3>
        
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            {{ __($description) }}
        </p>
        
        @if ($actionLabel)
            <div class="mt-6">
                @if ($actionModal)
                    <flux:modal.trigger :name="$actionModal">
                        <flux:button variant="primary">
                            {{ __($actionLabel) }}
                        </flux:button>
                    </flux:modal.trigger>
                @elseif ($actionRoute)
                    <a href="{{ $actionRoute }}">
                        <flux:button variant="primary">
                            {{ __($actionLabel) }}
                        </flux:button>
                    </a>
                @elseif ($actionClick)
                    <flux:button variant="primary" wire:click="{{ $actionClick }}">
                        {{ __($actionLabel) }}
                    </flux:button>
                @endif
            </div>
        @endif
        
        {{ $slot }}
    </div>
</div>
