<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['user_id', 'title', 'description', 'priority'])]
class Task extends Model
{
    protected $table = 'tasks';

     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


       public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_task')->withTimestamps(); // Category هاي بتكون الموديل
    }

         public function favoriteByUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
