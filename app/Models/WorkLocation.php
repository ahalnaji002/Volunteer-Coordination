<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
