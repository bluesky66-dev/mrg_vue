<?php

namespace Momentum\Events\ActionPlan;

use Illuminate\Queue\SerializesModels;
use Momentum\ActionPlanBehavior;

/**
 * Event called when an action plan behavior is removed from a plan.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlanBehaviorRemoved
{
    use SerializesModels;
    /**
     * Plan.
     * Plan.
     * @since 0.2.5
     *
     * @var \Momemtum\ActionPlanBehavior
     */
    public $plan_behavior;
    /**
     * Constructor.
     * @since 0.2.5
     * 
     * @param \Momemtum\ActionPlanBehavior $plan_behavior
     */
    public function __construct(ActionPlanBehavior $plan_behavior)
    {
        $this->plan_behavior = $plan_behavior;
    }
}