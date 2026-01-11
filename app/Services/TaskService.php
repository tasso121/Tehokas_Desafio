<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }
}
