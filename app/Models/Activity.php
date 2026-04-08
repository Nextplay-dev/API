<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    protected $fillable = [
        "name",
        "address",
        "media",
        "activity_category_id",
        "latitude",
        "longitude",
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'activity_category_id');
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class)
            ->withPivot("host")
            ->withTimestamps();
    }
}
