<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = [
        'volunteer_id',
        'work_location_id',
        'task_id',
        'assignment_date',
        'status',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function workLocation(): BelongsTo
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
