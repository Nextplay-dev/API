<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venue extends Model
{
    protected $fillable = [
        "name",
        "address",
        "media",
        "category_id",
        "latitude",
        "longitude",
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class)
            ->withPivot("host")
            ->withTimestamps();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
