<?php

namespace App\Actions\Resource;

use App\Models\Resource;

class DeleteResourceAction
{
    public function handle(Resource $resource): void
    {
        $resource->delete();
    }
}
