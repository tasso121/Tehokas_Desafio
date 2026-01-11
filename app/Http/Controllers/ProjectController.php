<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

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
            'projects' => $this->projectService->getAllProjects(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->projectService->createProject($request->validated());

        return redirect()->route('dashboard');
    }

    public function show(Project $project)
    {
        $project->load(['columns.tasks' => function ($query) {
            $query->orderBy('order')->orderBy('created_at');
        }]);

        return Inertia::render('Project/Show', [
            'project' => $project,
        ]);
    }
}
