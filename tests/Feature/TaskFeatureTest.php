<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_a_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'project_id' => $project->id,
            'title' => 'New Task',
            'description' => 'Describe task',
            'status' => 'pending',
            'deadline' => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    #[Test]
    public function user_can_update_task_status()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function user_can_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function task_validation_checks()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', [
            'title' => '',
        ])->assertSessionHasErrors(['project_id', 'title', 'status', 'deadline']);
    }
}
