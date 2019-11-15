<?php

namespace Momentum\Events\Organization;

use Illuminate\Queue\SerializesModels;
use Momentum\Organization;
use Momentum\OrganizationGoal;

/**
 * Event called when an organization is created/added.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class OrganizationGoalAdded
{
    use SerializesModels;
    /**
     * Organization
     * @since 0.2.5
     *
     * @var \Momemtum\Organization
     */
    public $organization;
    /**
     * Goal
     * @since 0.2.5
     *
     * @var \Momemtum\OrganizationGoal
     */
    public $goal;
    /**
     * Constructor.
     * @since 0.2.5
     * 
     * @param \Momemtum\Organization     $organization
     * @param \Momemtum\OrganizationGoal $goal
     */
    public function __construct(Organization $organization, OrganizationGoal $goal)
    {
        $this->organization = $organization;
        $this->goal = $goal;
    }
}