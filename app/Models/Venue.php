<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(VenueTournament::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
