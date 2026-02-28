@props([
    'users' => null,
    'sortField' => 'name',
    'sortDirection' => 'asc',
    'hasUsers' => false,
])

@if ($hasUsers)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('name')">
                        <div class="flex items-center gap-1">
                            {{ __('Name') }}
                            @if ($sortField === 'name')
                                <x-flux::icon.chevrons-up-down class="size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('email')">
                        <div class="flex items-center gap-1">
                            {{ __('Email') }}
                            @if ($sortField === 'email')
                                <x-flux::icon.chevrons-up-down class="size-4" />
                            @endif
                        </div>
                    </th>
                  
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('created_at')">
                        <div class="flex items-center gap-1">
                            {{ __('Created') }}
                            @if ($sortField === 'created_at')
                                <x-flux::icon.chevrons-up-down class="size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach ($users as $user)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div
                                        class="h-10 w-10 rounded-full bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center">
                                        <span class="font-medium text-neutral-700 dark:text-neutral-300">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-white">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-neutral-900 dark:text-white">{{ $user->email }}</div>
                            @if ($user->email_verified_at)
                                <div class="text-xs text-green-600 dark:text-green-400">
                                    {{ __('Verified') }}
                                </div>
                            @else
                                <div class="text-xs text-amber-600 dark:text-amber-400">
                                    {{ __('Unverified') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <flux:button variant="ghost" size="sm" id="edit-user-{{ $user->id }}"
                                    wire:click="editUser({{ $user->id }})">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button variant="outline" size="sm" id="delete-user-{{ $user->id }}"
                                    wire:click="deleteUser({{ $user->id }})">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-6 py-4">
        {{ $users->links() }}
    </div>
@else
    <div class="flex flex-col items-center justify-center py-12">
        <div class="text-center">
            <x-flux::icon.folder-git-2 class="mx-auto h-12 w-12 text-neutral-400" />
            <h3 class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">{{ __('No users') }}</h3>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Get started by creating a new user.') }}</p>
            <div class="mt-6">
                <flux:modal.trigger name="create-user-modal">
                    <flux:button variant="primary">
                        {{ __('Add User') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>
@endif