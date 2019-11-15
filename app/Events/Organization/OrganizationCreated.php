<?php

namespace Momentum\Events\Organization;

use Illuminate\Queue\SerializesModels;
use Momentum\Organization;

/**
 * Event called when an organization is created.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class OrganizationCreated
{
    use SerializesModels;
    /**
     * Organization.
     * @since 0.2.5
     *
     * @var \Momemtum\Organization
     */
    public $organization;
    /**
     * Constructor.
     * @since 0.2.5
     * 
     * @param \Momemtum\Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }
}