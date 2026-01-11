<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description'];

    protected $appends = ['is_on_alert'];

    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getIsOnAlertAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return false;
        }

        $overdueTasks = $this->tasks->filter(function ($task) {
            $isCompleted = $task->column ? $task->column->is_completed : $task->status === 'completed';

            return ! $isCompleted && $task->deadline < now();
        })->count();

        $percentage = ($overdueTasks / $totalTasks) * 100;

        return $percentage > 20;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
