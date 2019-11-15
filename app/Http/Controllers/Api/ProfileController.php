<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Momentum\Http\Controllers\Controller;
use Momentum\Observer;
use Hash;

/**
 * Handles any AJAX-API requests related to profiles.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ProfileController extends Controller
{
    /**
     * Returns current profile information as a json response.
     * @since 0.2.4
     *
     * @return array
     */
    public function get()
    {
        return new Collection([
            'user'      => Auth::user(),
            'culture'   => Auth::user()->culture,
            'observers' => Observer::currentUser()->get(),
        ]);
    }

    /**
     * Attempts to save/create the current user.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        // get the user
        $user = Auth::user();

        // if there's a password, the user is trying to change their password
        if ($request->has('password')) {
            $this->validate($request, [
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required',
            ]);

            $user->password = Hash::make($request->get('password'));
        }

        // if there is no password, we're changing the user's information
        if (!$request->has('password')) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name'  => 'required',
                'culture_id' => 'required|exists:cultures,id',
                'email'      => [
                    'required',
                    Rule::unique($user->getTable())->ignore($user->id)->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
                ],
            ]);

            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');
            $user->culture_id = $request->get('culture_id');

        }

        $user->save();

        // change the app culture so the message is in the correct language
        $user->load('culture');

        \App::setLocale($user->culture->code);

        return response()->json([
            'message' => trans('profile.success'),
            'success' => true,
        ]);
    }
}
