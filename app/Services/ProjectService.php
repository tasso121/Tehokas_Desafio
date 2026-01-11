<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function getAllProjects(): Collection
    {
        return Project::latest()->get();
    }

    public function createProject(array $data): Project
    {
        return Project::create($data);
    }

    public function getProjectWithTasks(Project $project): Project
    {
        return $project->load('tasks');
    }
}
