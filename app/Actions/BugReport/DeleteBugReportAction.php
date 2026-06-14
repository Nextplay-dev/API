<?php

namespace App\Actions\BugReport;

use App\Models\BugReport;

class DeleteBugReportAction
{
    public function handle(BugReport $bugReport): void
    {
        $bugReport->delete();
    }
}
