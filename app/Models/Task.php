<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}