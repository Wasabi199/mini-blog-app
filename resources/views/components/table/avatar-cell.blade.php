@props([
    'name' => '',
    'subtitle' => null,
    'size' => 'md', // sm, md, lg
    'imageUrl' => null,
])

@php
    $initial = strtoupper(substr($name, 0, 1));
    
    $sizeClasses = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-12 w-12 text-base',
    ];
    
    $avatarSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="flex items-center">
    <div class="flex-shrink-0 {{ $avatarSize }}">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $name }}" class="{{ $avatarSize }} rounded-full object-cover">
        @else
            <div class="{{ $avatarSize }} rounded-full bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center">
                <span class="font-medium text-neutral-700 dark:text-neutral-300">
                    {{ $initial }}
                </span>
            </div>
        @endif
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium text-neutral-900 dark:text-white">
            {{ $name }}
        </div>
        @if ($subtitle)
            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                {{ $subtitle }}
            </div>
        @endif
    </div>
</div>
