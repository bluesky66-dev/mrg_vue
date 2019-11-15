<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\Behavior;
use Momentum\Http\Controllers\Controller;

/**
 * Handles any AJAX-API requests related to behaviors.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class BehaviorsController extends Controller
{
    /**
     * Returns all behaviors as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\Behavior
     */
    public function index()
    {
        return Behavior::with('behavior_group')->get();
    }

    /**
     * Returns a behavior as a json response.
     * @since 0.2.4
     * 
     * @param int $id Behavior ID to get.
     *
     * @return \Momentum\Behavior
     */
    public function get($id)
    {
        return Behavior::findOrFail($id);
    }
}
