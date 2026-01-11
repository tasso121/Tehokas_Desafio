<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $appends = ['is_on_alert'];

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

        $overdueTasks = $this->tasks()
            ->where('status', '!=', 'completed')
            ->where('deadline', '<', now())
            ->count();

        return ($overdueTasks / $totalTasks) > 0.20;
    }
}
