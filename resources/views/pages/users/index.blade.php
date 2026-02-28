<x-layouts::app :title="__('Users')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Users') }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Manage your application users') }}</p>
            </div>
        </div>

        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <livewire:pages::users.user-list />
        </div>
    </div>

    <livewire:pages::users.create-user-modal />
    <livewire:pages::users.edit-user-modal />
    <livewire:pages::users.delete-user-modal />
</x-layouts::app>