<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Category extends Model
{
          public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'category_task')->withTimestamps(); // Task هاي بتكون الموديل
    }
}
