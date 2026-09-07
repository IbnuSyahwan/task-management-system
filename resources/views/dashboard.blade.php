<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto w-full max-w-7xl space-y-8">
        <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">
            <div class="pointer-events-none absolute -right-10 -top-16 size-48 rounded-full border-[18px] border-lime-200/60 dark:border-lime-400/10"></div>
            <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                    <flux:text class="font-medium uppercase tracking-[0.18em] text-lime-700 dark:text-lime-300">{{ __('Today at a glance') }}</flux:text>
                    <flux:heading size="xl" class="mt-2">{{ __('Good to see you, :name.', ['name' => auth()->user()->name]) }}</flux:heading>
                    <flux:text class="mt-2 max-w-xl">{{ __('Keep your priorities visible and your next action clear.') }}</flux:text>
            </div>
                <flux:button variant="primary" icon="arrow-right" :href="route('tasks.index')" wire:navigate>{{ __('Open tasks') }}</flux:button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['label' => 'Total tasks', 'value' => $taskSummary['total'], 'icon' => 'squares-2x2', 'color' => 'text-zinc-500'], ['label' => 'Pending', 'value' => $taskSummary['pending'], 'icon' => 'clock', 'color' => 'text-amber-600'], ['label' => 'In progress', 'value' => $taskSummary['in_progress'], 'icon' => 'arrow-path', 'color' => 'text-sky-600'], ['label' => 'Completed', 'value' => $taskSummary['completed'], 'icon' => 'check-circle', 'color' => 'text-emerald-600']] as $stat)
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <flux:text>{{ __($stat['label']) }}</flux:text>
                        <flux:icon :name="$stat['icon']" class="size-5 {{ $stat['color'] }}" />
                    </div>
                    <flux:heading size="xl" class="mt-4">{{ $stat['value'] }}</flux:heading>
                </div>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                <div>
                    <flux:heading size="sm">{{ __('Recently added') }}</flux:heading>
                    <flux:text size="sm" class="mt-1">{{ __('Your latest work at a glance.') }}</flux:text>
                </div>
                <flux:link :href="route('tasks.index')" wire:navigate>{{ __('View all') }}</flux:link>
            </div>
            @forelse ($recentTasks as $task)
                <div class="flex items-center justify-between gap-4 border-b border-zinc-200 px-5 py-4 last:border-b-0 dark:border-zinc-700">
                    <div class="min-w-0">
                        <flux:heading size="sm" class="truncate">{{ $task->title }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ $task->due_date ? __('Due :date', ['date' => $task->due_date->format('M j, Y')]) : __('No due date') }}</flux:text>
                    </div>
                    <flux:badge :color="$task->status === 'completed' ? 'emerald' : ($task->status === 'in_progress' ? 'sky' : 'amber')">{{ \Illuminate\Support\Str::headline($task->status) }}</flux:badge>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <flux:text>{{ __('No tasks yet. Create your first task to get started.') }}</flux:text>
                    <flux:button class="mt-4" variant="primary" :href="route('tasks.index')" wire:navigate>{{ __('Create a task') }}</flux:button>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>
