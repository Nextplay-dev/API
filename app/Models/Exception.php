<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exception extends Model
{
    protected $fillable = [
        'resource_id',
        'start_at',
        'end_at',
        'type',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
