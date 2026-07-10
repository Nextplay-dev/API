<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'address',
        'description',
        'media',
        'website',
        'osm_id',
        'category_id',
        'latitude',
        'longitude',
        'is_virtual',
        'external_booking_url',
    ];

    protected $casts = [
        'is_virtual' => 'boolean',
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

    public function ongoingTournaments(): HasMany
    {
        return $this->hasMany(VenueTournament::class)
            ->whereHas('booking', function ($query) {
                $query->where('start_at', '>=', Carbon::now('UTC'));
            });
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function analytics(): MorphToMany
    {
        return $this->morphToMany(UserAnalytic::class, 'attachable', 'user_analytic_attachments');
    }
}
