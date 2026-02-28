@props([
    'label' => '',
    'variant' => 'default', // default, success, warning, danger, info, primary
    'size' => 'md', // sm, md, lg
])

@php
    $variantClasses = [
        'default' => 'bg-neutral-100 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200',
        'success' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
        'warning' => 'bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200',
        'danger' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
        'info' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
        'primary' => 'bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200',
    ];
    
    $sizeClasses = [
        'sm' => 'px-1.5 py-0.5 text-xs',
        'md' => 'px-2 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];
    
    $badgeVariant = $variantClasses[$variant] ?? $variantClasses['default'];
    $badgeSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full {$badgeVariant} {$badgeSize}"]) }}>
    {{ $label }}
</span>
