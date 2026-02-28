@props([
    'position' => 'top-right', // top-right, top-left, top-center, bottom-right, etc.
])

@php
    $positionClasses = match($position) {
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'top-center' => 'top-4 left-1/2 transform -translate-x-1/2',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'bottom-center' => 'bottom-4 left-1/2 transform -translate-x-1/2',
        default => 'top-4 right-4',
    };
@endphp

<div 
    x-data="{
        messages: [],
        addMessage(event) {
            const message = {
                id: Date.now(),
                type: event.detail.type || 'success',
                text: event.detail.message || 'Operation completed successfully.',
                duration: event.detail.duration || 3000
            };
            
            this.messages.push(message);
            
            setTimeout(() => {
                this.removeMessage(message.id);
            }, message.duration);
        },
        removeMessage(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    x-init="
        // Listen for flash events
        Livewire.on('flash-message', (event) => addMessage(event));
        Livewire.on('user-created', () => addMessage({detail: {type: 'success', message: '{{ __('User created successfully.') }}'}}));
        Livewire.on('user-updated', () => addMessage({detail: {type: 'success', message: '{{ __('User updated successfully.') }}'}}));
        Livewire.on('user-deleted', () => addMessage({detail: {type: 'success', message: '{{ __('User deleted successfully.') }}'}}));
        Livewire.on('users-deleted', () => addMessage({detail: {type: 'success', message: '{{ __('Selected users deleted successfully.') }}'}}));
        Livewire.on('post-created', () => addMessage({detail: {type: 'success', message: '{{ __('Post created successfully!') }}'}}));
        Livewire.on('post-updated', () => addMessage({detail: {type: 'success', message: '{{ __('Post updated successfully!') }}'}}));
        Livewire.on('post-deleted', () => addMessage({detail: {type: 'success', message: '{{ __('Post deleted successfully!') }}'}}));
        Livewire.on('tag-created', () => addMessage({detail: {type: 'success', message: '{{ __('Tag created successfully!') }}'}}));
        Livewire.on('tag-updated', () => addMessage({detail: {type: 'success', message: '{{ __('Tag updated successfully!') }}'}}));
        Livewire.on('tag-deleted', () => addMessage({detail: {type: 'success', message: '{{ __('Tag deleted successfully!') }}'}}));
    "
    class="fixed z-50 {{ $positionClasses }} space-y-2"
    {{ $attributes }}
>
    <template x-for="message in messages" :key="message.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{
                'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': message.type === 'success',
                'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200': message.type === 'info',
                'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200': message.type === 'warning',
                'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': message.type === 'error',
            }"
            class="rounded-lg border px-4 py-3 shadow-lg max-w-sm"
        >
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <svg 
                        :class="{
                            'text-green-500 dark:text-green-400': message.type === 'success',
                            'text-blue-500 dark:text-blue-400': message.type === 'info',
                            'text-amber-500 dark:text-amber-400': message.type === 'warning',
                            'text-red-500 dark:text-red-400': message.type === 'error',
                        }"
                        class="h-5 w-5 mr-2" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <template x-if="message.type === 'success'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </template>
                        <template x-if="message.type === 'info'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </template>
                        <template x-if="message.type === 'warning'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </template>
                        <template x-if="message.type === 'error'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </template>
                    </svg>
                    <span x-text="message.text" class="text-sm font-medium"></span>
                </div>
                <button 
                    @click="removeMessage(message.id)"
                    class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>