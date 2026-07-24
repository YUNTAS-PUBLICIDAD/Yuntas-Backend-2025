<?php

namespace App\Application\Services\ActivityLog;

use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function getAll(int $perPage = 10)
    {
        return Activity::latest()->paginate($perPage);
    }
}