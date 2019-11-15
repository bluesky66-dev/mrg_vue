<?php

namespace Momentum\Events\ActionPlan;

use Illuminate\Queue\SerializesModels;
use Momentum\ActionPlan;

/**
 * Event called when an action plan is created.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlanCreated
{
    use SerializesModels;
    /**
     * Plan.
     * @since 0.2.5
     *
     * @var \Momemtum\ActionPlan
     */
    public $plan;
    /**
     * Constructor.
     * @since 0.2.5
     * 
     * @param \Momemtum\ActionPlan $plan
     */
    public function __construct(ActionPlan $plan)
    {
        $this->plan = $plan;
    }
}