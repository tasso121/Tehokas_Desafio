<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_is_not_on_alert_if_it_has_no_tasks()
    {
        $project = Project::factory()->create();

        $this->assertFalse($project->is_on_alert);
    }

    #[Test]
    public function it_is_not_on_alert_if_all_tasks_are_on_time()
    {
        $project = Project::factory()->create();
        Task::factory(5)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->addDay(),
        ]);

        $this->assertFalse($project->is_on_alert);
    }

    #[Test]
    public function it_is_not_on_alert_if_exactly_20_percent_of_tasks_are_overdue()
    {
        $project = Project::factory()->create();
        
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->subDay(),
        ]);

        Task::factory(4)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->addDay(),
        ]);

        $this->assertFalse($project->fresh()->is_on_alert);
    }

    #[Test]
    public function it_is_on_alert_if_more_than_20_percent_of_tasks_are_overdue()
    {
        $project = Project::factory()->create();
        
        Task::factory(2)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->subDay(),
        ]);

        Task::factory(3)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->addDay(),
        ]);

        $this->assertTrue($project->fresh()->is_on_alert);
    }

    #[Test]
    public function completed_tasks_are_not_considered_overdue_even_if_past_deadline()
    {
        $project = Project::factory()->create();
        
        Task::factory(2)->create([
            'project_id' => $project->id,
            'status' => 'completed',
            'deadline' => now()->subDay(),
        ]);

        Task::factory(3)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'deadline' => now()->addDay(),
        ]);

        $this->assertFalse($project->fresh()->is_on_alert);
    }
}
