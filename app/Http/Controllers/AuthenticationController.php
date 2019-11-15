<?php

namespace Momentum\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Momentum\Notifications\MagicLink;
use Momentum\User;
use Auth;
use Jenssegers\Agent\Agent;

/**
 * Handles all authentication requests.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class AuthenticationController extends Controller
{
    /**
     * Returns and displays the login form page.
     * @since 0.2.4
     * 
     * @return \Illuminate\Http\Response
     */
    public function loginForm()
    {
        $agent = new Agent();
        if($agent->isMobile()){
            if($agent->is('iPhone')){
                return view('errors.iphone'); 
            }
                return view('errors.mobile');            
        }
        
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Returns response after processing a login (POST) request.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // need to validate that an email was passed
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        // find user by email
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return response()->json([
                'message' => trans('login.auth.missing_user_by_email'),
            ], 404);
        }

        // if the user has a password, they need to use it to login
        if ($user->password !== null && !$request->has('password')) {
            return response()->json([
                'needs_password' => true,
            ]);
        }

        // if the user has a password and it was passed in the request
        // try to log them in
        if ($user->password !== null && $request->has('password')) {
            if (!Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
                return response()->json([
                    'message' => trans('login.auth.invalid_password'),
                ], 401);
            }

            return response()->json([
                'message'  => trans('login.auth.login_successful'),
                'success'  => true,
                'redirect' => route('dashboard'),
            ]);
        }

        // otherwise, we need to generate a token and send them a magic link
        $token = $user->generateMagicToken();

        $user->sendNotification(new MagicLink($user, $token));

        $replacements = [
            'email' => $user->email,
        ];

        return response()->json([
            'message'              => trans('login.auth.magic_link_sent', $replacements),
            'message_key'          => 'login.auth.magic_link_sent',
            'message_replacements' => $replacements,
            'success'              => true,
        ]);
    }

    /**
     * Returns a response redirection after processeing a logout request.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        Auth::logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * Returns a redirect response after attempting loggin in a user using a magic token code.
     * This is used for when a user forgets its credentials.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param string                   $code    Magic token access code.
     * 
     * @return \Illuminate\Http\Response
     */
    public function magicAuth(Request $request, $code)
    {
        // get a user by their remember token
        $user = User::where('magic_token', $code)
            ->where('token_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login', ['message' => trans('login.auth.invalid_token')]);
        }

        Auth::login($user);
        if($request->get('forgot') == 'true'){
            return redirect('/profile/reset');
        } else {
            return redirect('/dashboard');
        }
    }

    /**
     * Returns response after sending magic token code to user.
     * This is used for when a user forgets its credentials.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function magicSend(Request $request)
    {
        // need to validate that an email was passed
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        // find user by email
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return response()->json([
                'message' => trans('login.auth.missing_user_by_email'),
            ], 404);
        }

        $token = $user->generateMagicToken();
        if($request->forgot){
            $token = $token . "?forgot=true";
        }
        $user->sendNotification(new MagicLink($user, $token));

        $replacements = [
            'email' => $user->email,
        ];

        return response()->json([
            'message'              => trans('login.auth.magic_link_sent', $replacements),
            'message_key'          => 'login.auth.magic_link_sent',
            'message_replacements' => $replacements,
            'success'              => true,
        ]);
    }
}
