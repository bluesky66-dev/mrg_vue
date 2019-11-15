<?php

namespace Momentum\Events\ActionPlan;

use Illuminate\Queue\SerializesModels;
use Momentum\ActionGoal;
use Momentum\ActionPlan;

/**
 * Event called when an action goal is added to a plan.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionGoalAdded
{
    use SerializesModels;
    /**
     * Goal.
     * @since 0.2.5
     *
     * @var \Momemtum\ActionGoal
     */
    public $goal;
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
     * @param \Momemtum\ActionGoal $goal
     */
    public function __construct(ActionPlan $plan, ActionGoal $goal)
    {
        $this->plan = $plan;
        $this->goal = $goal;
    }
}