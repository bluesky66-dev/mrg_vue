<?php

namespace Momentum\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Momentum\Enums\ObserverTypes;
use Momentum\Observer;
use Momentum\Services\PDFGeneratorService;

/**
 * Handles requests related with the profile web page.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ProfileController extends Controller
{
    /**
     * Display current user's profile web page.
     * Returns view response.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $data = json_encode([
            'user'           => Auth::user(),
            'culture'        => Auth::user()->culture,
            'observers'      => Observer::currentReport()->currentUser()->enabled()->get(),
            'observer_types' => ObserverTypes::optionsWithLabels(),
        ]);

        return view('profile.profile', compact('data'));
    }

    /**
     * Display current user's reset-profile web page.
     * Returns view response.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        $data = json_encode([
            'user'           => Auth::user(),
            'culture'        => Auth::user()->culture,
            'observers'      => Observer::currentReport()->currentUser()->enabled()->get(),
            'observer_types' => ObserverTypes::optionsWithLabels(),
            'reset'      => 'true'
        ]);

        return view('profile.profile', compact('data'));
    }
}
