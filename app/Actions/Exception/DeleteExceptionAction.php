<?php

namespace App\Actions\Exception;

use App\Models\Exception;

class DeleteExceptionAction
{
    public function handle(Exception $exception): void
    {
        $exception->delete();
    }
}
