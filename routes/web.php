<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $taskQuery = Task::query()->where('user_id', Auth::id());

        return view('dashboard', [
            'taskSummary' => [
                'total' => (clone $taskQuery)->count(),
                'pending' => (clone $taskQuery)->where('status', 'pending')->count(),
                'in_progress' => (clone $taskQuery)->where('status', 'in_progress')->count(),
                'completed' => (clone $taskQuery)->where('status', 'completed')->count(),
            ],
            'recentTasks' => (clone $taskQuery)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    })->name('dashboard');
    Route::livewire('tasks', 'pages::tasks')->name('tasks.index');
});

require __DIR__.'/settings.php';
