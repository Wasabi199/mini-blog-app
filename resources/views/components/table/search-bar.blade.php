@props([
    'placeholder' => 'Search...',
    'model' => 'search',
])

<div class="w-full sm:w-auto">
    <flux:input 
        wire:model.live.debounce.300ms="{{ $model }}" 
        :placeholder="$placeholder"
        class="w-full sm:w-64" 
        icon="magnifying-glass" 
        {{ $attributes }}
    />
</div>
