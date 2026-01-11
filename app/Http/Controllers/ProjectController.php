<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'projects' => $this->projectService->getAllProjects()
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->projectService->createProject($request->validated());

        return redirect()->route('dashboard');
    }

    public function show(Project $project): Response
    {
        return Inertia::render('Project/Show', [
            'project' => $this->projectService->getProjectWithTasks($project)
        ]);
    }
}
