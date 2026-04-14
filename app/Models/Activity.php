<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'duration_minutes',
        'slot_interval_minutes',
        'rules_json',
    ];

    protected $casts = [
        'rules_json' => 'array',
    ];
}
