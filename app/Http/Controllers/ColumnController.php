<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColumnRequest;
use App\Models\Project;
use App\Services\ColumnService;

class ColumnController extends Controller
{
    protected $columnService;

    public function __construct(ColumnService $columnService)
    {
        $this->columnService = $columnService;
    }

    public function store(StoreColumnRequest $request, Project $project)
    {
        $this->columnService->createColumn($project, $request->validated());

        return back();
    }
}
