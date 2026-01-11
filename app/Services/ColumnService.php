<?php

namespace App\Services;

use App\Models\Column;
use App\Models\Project;

class ColumnService
{
    /**
     * Create a new column for a project.
     *
     * @param Project $project
     * @param array $data
     * @return Column
     */
    public function createColumn(Project $project, array $data): Column
    {
        $maxOrder = $project->columns()->max('order') ?? 0;

        return $project->columns()->create([
            'name' => $data['name'],
            'order' => $maxOrder + 1,
            'is_completed' => false,
        ]);
    }
}
