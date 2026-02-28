@props([
    'email' => '',
    'verified' => false,
])

<div>
    <div class="text-sm text-neutral-900 dark:text-white">{{ $email }}</div>
    <div class="text-xs {{ $verified ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
        {{ $verified ? __('Verified') : __('Unverified') }}
    </div>
</div>
