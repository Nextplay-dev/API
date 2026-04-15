<?php

namespace App\Actions\Exception;

use App\Models\Exception;

class UpdateExceptionAction
{
    public function handle(Exception $exception, array $data): Exception
    {
        $exception->update($data);
        return $exception->refresh();
    }
}
