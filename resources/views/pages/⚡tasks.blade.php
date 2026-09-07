<?php

use App\Models\Task;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;

new #[Title('Tasks')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $priorityFilter = 'all';
    public bool $showForm = false;
    public ?int $editingTaskId = null;
    public string $title = '';
    public string $description = '';
    public string $status = 'pending';
    public string $priority = 'medium';
    public ?string $due_date = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'priorityFilter']);
        $this->resetPage();
    }

    public function createTask(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editTask(int $taskId): void
    {
        $task = $this->ownedTasks()->findOrFail($taskId);

        $this->resetValidation();
        $this->editingTaskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->status = $task->status;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date?->format('Y-m-d');
        $this->showForm = true;
    }

    public function saveTask(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        if ($this->editingTaskId) {
            $this->ownedTasks()->findOrFail($this->editingTaskId)->update($validated);
            Flux::toast(variant: 'success', text: __('Task updated.'));
        } else {
            Task::query()->create([...$validated, 'user_id' => Auth::id()]);
            Flux::toast(variant: 'success', text: __('Task created.'));
        }

        $this->resetForm();
    }

    public function deleteTask(int $taskId): void
    {
        $this->ownedTasks()->findOrFail($taskId)->delete();
        $this->resetPage();

        Flux::toast(variant: 'success', text: __('Task deleted.'));
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingTaskId', 'title', 'description', 'due_date']);
        $this->status = 'pending';
        $this->priority = 'medium';
        $this->showForm = false;
        $this->resetValidation();
    }

    private function ownedTasks()
    {
        return Task::query()->ownedBy(Auth::user());
    }

    #[Computed]
    public function tasks(): LengthAwarePaginator
    {
        return $this->ownedTasks()
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $query->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            }))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->priorityFilter !== 'all', fn ($query) => $query->where('priority', $this->priorityFilter))
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->latest()
            ->paginate(8);
    }

    #[Computed]
    public function summary(): array
    {
        return [
            'total' => $this->ownedTasks()->count(),
            'pending' => $this->ownedTasks()->where('status', 'pending')->count(),
            'in_progress' => $this->ownedTasks()->where('status', 'in_progress')->count(),
            'completed' => $this->ownedTasks()->where('status', 'completed')->count(),
        ];
    }
}; ?>

<section class="mx-auto w-full max-w-7xl space-y-8">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <flux:badge color="lime" class="mb-3">{{ __('Workspace') }}</flux:badge>
            <flux:heading size="xl">{{ __('Your tasks') }}</flux:heading>
            <flux:text class="mt-2 max-w-xl">{{ __('Plan the work, keep momentum, and see what needs attention next.') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="createTask">{{ __('New task') }}</flux:button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['label' => 'Total tasks', 'value' => $this->summary['total'], 'color' => 'zinc'], ['label' => 'Pending', 'value' => $this->summary['pending'], 'color' => 'amber'], ['label' => 'In progress', 'value' => $this->summary['in_progress'], 'color' => 'sky'], ['label' => 'Completed', 'value' => $this->summary['completed'], 'color' => 'emerald']] as $stat)
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text>{{ __($stat['label']) }}</flux:text>
                <div class="mt-3 flex items-end justify-between">
                    <flux:heading size="xl">{{ $stat['value'] }}</flux:heading>
                    <flux:badge :color="$stat['color']">{{ $stat['label'] }}</flux:badge>
                </div>
            </div>
        @endforeach
    </div>

    @if ($showForm)
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <flux:heading>{{ $editingTaskId ? __('Edit task') : __('Create a task') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Capture the outcome, then give it a clear next step.') }}</flux:text>
                </div>
                <flux:button variant="ghost" icon="x-mark" wire:click="cancelForm" aria-label="{{ __('Close form') }}" />
            </div>

            <form wire:submit="saveTask" class="grid gap-5 md:grid-cols-2">
                <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Prepare project presentation') }}" required />
                <div>
                    <flux:label>{{ __('Due date') }}</flux:label>
                    <input wire:model="due_date" type="date" class="mt-2 block w-full rounded-lg border-zinc-300 bg-transparent px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
                    @error('due_date') <flux:error name="due_date" /> @enderror
                </div>
                <div class="md:col-span-2">
                    <flux:textarea wire:model="description" :label="__('Description')" rows="3" placeholder="{{ __('Add useful context or acceptance criteria...') }}" />
                </div>
                <div>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <select wire:model="status" class="mt-2 block w-full rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="in_progress">{{ __('In progress') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                    </select>
                </div>
                <div>
                    <flux:label>{{ __('Priority') }}</flux:label>
                    <select wire:model="priority" class="mt-2 block w-full rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                        <option value="low">{{ __('Low') }}</option>
                        <option value="medium">{{ __('Medium') }}</option>
                        <option value="high">{{ __('High') }}</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-3 md:col-span-2">
                    <flux:text wire:loading wire:target="saveTask" class="mr-auto">{{ __('Saving...') }}</flux:text>
                    <flux:button type="button" variant="ghost" wire:click="cancelForm">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveTask">
                        <span wire:loading.remove wire:target="saveTask">{{ $editingTaskId ? __('Update task') : __('Create task') }}</span>
                        <span wire:loading wire:target="saveTask">{{ __('Saving...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    @endif

    <div class="space-y-5">
        <div class="flex flex-col gap-3 md:flex-row">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search tasks...')" aria-label="{{ __('Search tasks') }}" />
            <select wire:model.live="statusFilter" class="min-w-44 rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                <option value="all">{{ __('All statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="in_progress">{{ __('In progress') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
            </select>
            <select wire:model.live="priorityFilter" class="min-w-44 rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                <option value="all">{{ __('All priorities') }}</option>
                <option value="low">{{ __('Low priority') }}</option>
                <option value="medium">{{ __('Medium priority') }}</option>
                <option value="high">{{ __('High priority') }}</option>
            </select>
            @if ($search !== '' || $statusFilter !== 'all' || $priorityFilter !== 'all')
                <flux:button variant="ghost" icon="x-mark" wire:click="resetFilters" class="shrink-0">{{ __('Clear') }}</flux:button>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            @forelse ($this->tasks as $task)
                <article wire:key="task-{{ $task->id }}" class="flex flex-col gap-4 border-b border-zinc-200 p-5 last:border-b-0 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading size="sm" class="truncate">{{ $task->title }}</flux:heading>
                            <flux:badge :color="$task->status === 'completed' ? 'emerald' : ($task->status === 'in_progress' ? 'sky' : 'amber')">{{ Str::headline($task->status) }}</flux:badge>
                            <flux:badge :color="$task->priority === 'high' ? 'red' : ($task->priority === 'low' ? 'zinc' : 'amber')">{{ Str::headline($task->priority) }}</flux:badge>
                        </div>
                        @if ($task->description)
                            <flux:text class="mt-2 line-clamp-2">{{ $task->description }}</flux:text>
                        @endif
                        <flux:text size="sm" class="mt-2">{{ $task->due_date ? __('Due :date', ['date' => $task->due_date->format('M j, Y')]) : __('No due date') }}</flux:text>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <flux:button variant="ghost" icon="pencil-square" wire:click="editTask({{ $task->id }})" aria-label="{{ __('Edit task') }}" />
                        <flux:button variant="ghost" icon="trash" wire:click="deleteTask({{ $task->id }})" wire:confirm="{{ __('Delete this task?') }}" aria-label="{{ __('Delete task') }}" />
                    </div>
                </article>
            @empty
                <div class="px-6 py-16 text-center">
                    <flux:icon name="clipboard-document-list" class="mx-auto size-10 text-zinc-400" />
                    <flux:heading size="sm" class="mt-4">{{ __('No tasks found') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Create a task or adjust your filters to see work here.') }}</flux:text>
                </div>
            @endforelse
        </div>

        <div>{{ $this->tasks->links() }}</div>
    </div>
</section>