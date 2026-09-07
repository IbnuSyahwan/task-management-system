<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $tasks = [
            [
                'title' => 'Review project requirements',
                'description' => 'Confirm the assessment checklist and acceptance criteria.',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => now()->subDays(2)->toDateString(),
            ],
            [
                'title' => 'Polish task dashboard',
                'description' => 'Check responsive spacing, empty states, and filter behavior.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addDays(2)->toDateString(),
            ],
            [
                'title' => 'Write setup documentation',
                'description' => 'Document SQLite setup, migrations, seeding, and local development.',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(5)->toDateString(),
            ],
            [
                'title' => 'Add feature test coverage',
                'description' => 'Cover task ownership, validation, search, filtering, and CRUD behavior.',
                'status' => 'completed',
                'priority' => 'medium',
                'due_date' => now()->subDay()->toDateString(),
            ],
            [
                'title' => 'Prepare repository submission',
                'description' => 'Review the final diff and confirm migrations and README are included.',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(10)->toDateString(),
            ],
        ];

        foreach ($tasks as $task) {
            Task::query()->updateOrCreate(
                ['user_id' => $user->id, 'title' => $task['title']],
                $task,
            );
        }
    }
}
