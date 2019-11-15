<?php

namespace Momentum\Http\Controllers;

use Momentum\Culture;

/**
 * Provides controller utility methods.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class UtilitiesController extends Controller
{
    /**
     * Returns a redirection response and sets culture ID as cookie.
     * @since 0.2.4
     * 
     * @param int $id Culture ID to set as cookie.
     *
     * @return \Illuminate\Http\Response
     */
    public function setCulture($id)
    {
        $culture = Culture::findOrFail($id);
        return redirect()->back()->withCookie('culture', $culture->id);
    }
}
