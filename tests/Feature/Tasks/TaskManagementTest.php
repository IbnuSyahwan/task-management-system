<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests cannot access tasks', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

test('users can create, filter, search, update, and delete their own tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $visibleTask = Task::factory()->for($user)->create([
        'title' => 'Prepare assessment demo',
        'status' => 'pending',
        'priority' => 'high',
    ]);
    Task::factory()->for($user)->create(['title' => 'Write project notes', 'status' => 'completed']);
    Task::factory()->for($otherUser)->create(['title' => 'Private other user task']);

    $this->actingAs($user);

    Livewire::test('pages::tasks')
        ->assertSee('Prepare assessment demo')
        ->assertDontSee('Private other user task')
        ->call('createTask')
        ->set('title', 'Plan final submission')
        ->set('description', 'Review the README and migrations.')
        ->set('status', 'pending')
        ->set('priority', 'high')
        ->call('saveTask')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'Plan final submission',
        'priority' => 'high',
    ]);

    Livewire::test('pages::tasks')
        ->assertSee('Prepare assessment demo')
        ->assertDontSee('Private other user task')
        ->set('search', 'assessment')
        ->assertSee('Prepare assessment demo')
        ->assertDontSee('Write project notes')
        ->set('search', '')
        ->set('statusFilter', 'completed')
        ->assertSee('Write project notes')
        ->assertDontSee('Prepare assessment demo')
        ->set('statusFilter', 'all')
        ->set('priorityFilter', 'high')
        ->assertSee('Prepare assessment demo')
        ->assertDontSee('Write project notes')
        ->set('priorityFilter', 'all')
        ->call('editTask', $visibleTask->id)
        ->set('status', 'in_progress')
        ->call('saveTask')
        ->assertHasNoErrors();

    expect($visibleTask->fresh()->status)->toBe('in_progress');

    Livewire::test('pages::tasks')
        ->call('deleteTask', $visibleTask->id);

    $this->assertDatabaseMissing('tasks', ['id' => $visibleTask->id]);
});

test('task input is validated and another users task cannot be edited', function () {
    $user = User::factory()->create();
    $otherTask = Task::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::tasks')
        ->call('createTask')
        ->call('saveTask')
        ->assertHasErrors(['title']);

    expect(fn () => Livewire::test('pages::tasks')->call('editTask', $otherTask->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test('pages::tasks')->call('deleteTask', $otherTask->id))
        ->toThrow(ModelNotFoundException::class);
});
