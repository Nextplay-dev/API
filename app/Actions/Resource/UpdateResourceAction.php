<?php

namespace App\Actions\Resource;

use App\Models\Resource;

class UpdateResourceAction
{
    public function handle(Resource $resource, array $data): Resource
    {
        $resource->update($data);
        return $resource->refresh();
    }
}
