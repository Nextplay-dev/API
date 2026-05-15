<?php

namespace App\DTOs;

use App\Models\Activity;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ListAvailableSlotDTO
{
    public function __construct(
        public Activity $activity,
        public Carbon $from,
        public Carbon $to,
        public ?Resource $resource = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        $activity = Activity::findOrFail($request->validated('activity_id', null));
        $resource = $request->has('resource_id') ? Resource::findOrFail($request->validated('resource_id', null)) : null;

        return new self(
            activity: $activity,
            from: $request->validated('from'),
            to: $request->validated('to'),
            resource: $resource,
        );
    }
}
