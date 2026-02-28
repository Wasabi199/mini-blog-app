@props([
    'items' => null,
    'columns' => [],
    'sortField' => null,
    'sortDirection' => 'asc',
    'selectedItems' => [],
    'selectable' => false,
    'hasItems' => false,
    'emptyState' => null,
    'actions' => true,
    'editAction' => 'editUser',
    'deleteAction' => 'deleteUser',
])

@php
    // Default empty state if not provided
    if (!$emptyState) {
        $emptyState = [
            'icon' => 'folder-git-2',
            'title' => __('No data'),
            'description' => __('Get started by adding new items.'),
            'action' => [
                'label' => __('Add Item'),
                'modal' => 'create-item-modal',
            ],
        ];
    }
@endphp

<div class="h-full overflow-auto">
    @if ($hasItems)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        @if ($selectable)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                <!-- Selection column header (no select all checkbox) -->
                            </th>
                        @endif

                        @foreach ($columns as $column)
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider 
                                    {{ $column['sortable'] ?? false ? 'cursor-pointer' : '' }}"
                                @if($column['sortable'] ?? false) wire:click="sortBy('{{ $column['key'] }}')" @endif
                            >
                                <div class="flex items-center gap-1">
                                    {{ $column['label'] }}
                                    @if (($column['sortable'] ?? false) && $sortField === $column['key'])
                                        <x-flux::icon.chevrons-up-down class="size-4" />
                                    @endif
                                </div>
                            </th>
                        @endforeach

                        @if ($actions)
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($items as $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            @if ($selectable)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input 
                                        type="checkbox" 
                                        value="{{ $item->id }}"
                                        wire:model="selectedItems"
                                        class="h-4 w-4 rounded border-neutral-300 dark:border-neutral-600 text-primary-600 dark:text-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 dark:bg-neutral-800"
                                    >
                                </td>
                            @endif

                            @foreach ($columns as $column)
                                <td class="px-6 py-4 whitespace-nowrap {{ $column['class'] ?? '' }}">
                                    @if (isset($column['slot']))
                                        {!! $column['slot']($item) !!}
                                    @else
                                        {{ data_get($item, $column['key']) }}
                                    @endif
                                </td>
                            @endforeach

                             @if ($actions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <x-table.action-buttons 
                                        :itemId="$item->id"
                                        :editAction="$editAction"
                                        :deleteAction="$deleteAction"
                                    />
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-6 py-4">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <x-table.empty-state 
            :icon="$emptyState['icon']"
            :title="$emptyState['title']"
            :description="$emptyState['description']"
            :actionLabel="$emptyState['action']['label'] ?? null"
            :actionModal="$emptyState['action']['modal'] ?? null"
            :actionRoute="$emptyState['action']['route'] ?? null"
            :actionClick="$emptyState['action']['click'] ?? null"
        />
    @endif
</div>