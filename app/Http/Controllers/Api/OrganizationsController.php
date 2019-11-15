<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\Http\Controllers\Controller;
use Momentum\Events\Organization\OrganizationCreated;
use Momentum\Organization;

/**
 * Handles any AJAX-API requests related to organizations.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class OrganizationsController extends Controller
{
    /**
     * Returns all organizations for the current report as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\Observer
     */
    public function index()
    {
        return Organization::currentReport()->get();
    }

    /**
     * Saves an organization.
     * @since 0.2.4
     *
     * @todo Check if this is being used.
     */
    public function save()
    {
        // TODO
        // event(new OrganizationCreated($organization));
    }

    /**
     * Returns an organization as a json response.
     * @since 0.2.4
     * 
     * @param int $id Organization ID to get.
     *
     * @return \Momentum\Organization
     */
    public function get($id)
    {
        return Organization::currentReport()->findOrFail($id);
    }
}
