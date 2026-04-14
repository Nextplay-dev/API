<?php

namespace App\Actions\Resource;

use App\Models\Resource;
use App\Models\Venue;
use Illuminate\Http\Request;

class StoreResourceAction
{
    public function handle(Venue $venue, array $data): Resource
    {
        return $venue->resources()->create($data);
    }
}
