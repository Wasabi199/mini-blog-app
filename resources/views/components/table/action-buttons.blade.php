@props([
    'editAction' => null,
    'deleteAction' => null,
    'editLabel' => 'Edit',
    'deleteLabel' => 'Delete',
    'itemId' => null,
])

<div class="flex items-center gap-2">
    @if ($editAction)
        <flux:button 
            variant="ghost" 
            wire:click="{{ $editAction }}({{ $itemId }})"
            wire:target="{{ $editAction }}({{ $itemId }})"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="{{ $editAction }}({{ $itemId }})">
                {{ __($editLabel) }}
            </span>
            <span wire:loading wire:target="{{ $editAction }}({{ $itemId }})" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Loading...') }}
            </span>
        </flux:button>
    @endif
    
    @if ($deleteAction)
        <flux:button 
            variant="outline" 
            wire:click="{{ $deleteAction }}({{ $itemId }})"
            wire:target="{{ $deleteAction }}({{ $itemId }})"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="{{ $deleteAction }}({{ $itemId }})">
                {{ __($deleteLabel) }}
            </span>
            <span wire:loading wire:target="{{ $deleteAction }}({{ $itemId }})" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Loading...') }}
            </span>
        </flux:button>
    @endif
    
    {{ $slot }}
</div>
