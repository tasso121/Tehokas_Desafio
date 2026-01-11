<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ProjectFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_access_dashboard()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_users_can_view_dashboard_with_projects()
    {
        $user = User::factory()->create();
        $projects = Project::factory(3)->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('projects', 3)
            );
    }

    #[Test]
    public function user_can_create_a_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'New Project',
            'description' => 'Project Description',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    #[Test]
    public function user_cannot_create_invalid_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function user_can_view_project_kanban()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $this->actingAs($user)
            ->get("/projects/{$project->id}")
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Project/Show')
                ->where('project.id', $project->id)
            );
    }
}
