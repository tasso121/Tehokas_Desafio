<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Dashboard', [
            'projects' => \App\Models\Project::latest()->get()
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        \App\Models\Project::create($validated);

        return redirect()->route('dashboard');
    }

    public function show(\App\Models\Project $project)
    {
        return \Inertia\Inertia::render('Project/Show', [
            'project' => $project->load('tasks')
        ]);
    }
}
