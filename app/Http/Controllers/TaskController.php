<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'deadline' => 'required|date',
        ]);

        \App\Models\Task::create($validated);

        return back();
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'deadline' => 'sometimes|date',
        ]);

        $task->update($validated);

        return back();
    }

    public function destroy(\App\Models\Task $task)
    {
        $task->delete();
        return back();
    }
}
