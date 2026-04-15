<?php

namespace App\Actions\Exception;

use App\Models\Exception;
use App\Models\Resource;

class StoreExceptionAction
{
    public function handle(Resource $resource, array $data): Exception
    {
        return $resource->exceptions()->create($data);
    }
}
