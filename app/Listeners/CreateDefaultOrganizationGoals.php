<?php

namespace Momentum\Listeners;

use Log;
use Exception;
use Momentum\Events\Organization\OrganizationCreated;
use Momentum\Events\Organization\OrganizationGoalAdded;

/**
 * Listens to `OrganizationCreated` event to add and create the default organizational goals.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class CreateDefaultOrganizationGoals
{
    /**
     * Handle the event.
     * @since 0.2.5
     *
     * @param NotificationSent|object $event
     */
    public function handle(OrganizationCreated $event)
    {
        try {
            foreach (config('momentum.organization.goals') as $config) {
                if ($event->organization->goals()->where('question_key', $config['key'])->first() === null) {
                    $goal = $event->organization->goals()->create([
                        'organization_id'   => $event->organization->id,
                        'question_key'      => $config['key'],
                        'type'              => $config['type'],
                        'sort'              => $config['sort'],
                    ]);
                    event(new OrganizationGoalAdded($event->organization, $goal));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
