<?php
/**
 * Action to log user analytics with optional metadata and attached models.
 */

namespace App\Actions\Analytics;

use App\Models\User;
use App\Models\UserAnalytic;
use Illuminate\Database\Eloquent\Model;

class LogUserAnalyticAction
{
    /**
     * Create a new analytic entry.
     * 
     * @param User $user
     * @param string $action
     * @param array $metadata
     * @param array<Model> $attachedModels
     * @return UserAnalytic
     */
    public function handle(User $user, string $action, array $metadata = [], array $attachedModels = []): UserAnalytic
    {
        $analytic = UserAnalytic::create([
            'user_id' => $user->id,
            'action' => $action,
            'metadata' => $metadata,
        ]);

        foreach ($attachedModels as $model) {
            $analytic->attachedModels(get_class($model))->attach($model->id);
        }

        return $analytic;
    }
}
