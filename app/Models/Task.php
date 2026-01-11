<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'column_id', 'title', 'description', 'status', 'priority', 'deadline'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function getIsOverdueAttribute()
    {
        $isCompleted = $this->column ? $this->column->is_completed : $this->status === 'completed';

        return ! $isCompleted && $this->deadline && $this->deadline->isPast();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
