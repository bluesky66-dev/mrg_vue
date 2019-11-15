<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\Culture;
use Momentum\Http\Controllers\Controller;

/**
 * Handles any AJAX-API requests related to cultures.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class CulturesController extends Controller
{
    /**
     * Returns all cultures as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\Behavior
     */
    public function index()
    {
        return Culture::all();
    }

    /**
     * Saves a culture.
     * @since 0.2.4
     *
     * @todo Check if this is being used.
     */
    public function save()
    {
        // TODO
    }

    /**
     * Returns a culture as a json response.
     * @since 0.2.4
     * 
     * @param int $id Culture ID to get.
     *
     * @return \Momentum\Culture
     */
    public function get($id)
    {
        return Culture::findOrFail($id);
    }
}
