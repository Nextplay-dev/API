<?php

namespace App\Actions\BugReport;

use App\DTOs\BugReportDTO;
use App\Models\BugReport;

class StoreBugReportAction
{
    public function handle(BugReportDTO $dto): BugReport
    {
        return BugReport::create([
            'user_id' => $dto->userId,
            'message' => $dto->message,
        ]);
    }
}
