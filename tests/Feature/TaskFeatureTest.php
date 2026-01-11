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
        $column = $project->columns()->create(['name' => 'To Do']); // Create implicit column

        $response = $this->actingAs($user)->post('/tasks', [
            'project_id' => $project->id,
            'column_id' => $column->id,
            'title' => 'New Task',
            'description' => 'Describe task',
            'priority' => 'medium',
            'deadline' => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    #[Test]
    public function user_can_update_task_status()
    {
        $user = User::factory()->create();
        // Create task with factory which handles column creation if needed, or manually
        $project = Project::factory()->create();
        $col1 = $project->columns()->create(['name' => 'To Do']);
        $col2 = $project->columns()->create(['name' => 'Done']);
        
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'column_id' => $col1->id
        ]);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}", [
            'column_id' => $col2->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'column_id' => $col2->id,
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
        ])->assertSessionHasErrors(['project_id', 'title', 'column_id', 'deadline', 'priority']);
    }
}
