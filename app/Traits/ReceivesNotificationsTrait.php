<?php

namespace Momentum\Traits;

use App;
use Auth;
use Exception;
use ReflectionClass;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Momentum\EmailLog;
use Momentum\Utilities\Localization;

/**
 * Trait that adds functionality to send notifications.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
trait ReceivesNotificationsTrait
{
    /**
     * Sends a notification.
     * Alias for "notify" to wrap the notify method in a try/catch
     * so that we can log any errors if the email fails.
     * @since 0.2.4
     *
     * @param object $instance An instance of the object that will be norified.
     */
    public function sendNotification($instance)
    {
        try {
            $this->notify($instance);
        } catch (Exception $e) {
            // get the user from the notification
            $user = $instance->recipient;
            $recipient = null;
            $reflection = new ReflectionClass($instance);
            $email_type = Str::snake($reflection->getShortName());

            if ($user !== null) {
                $recipient = $user->email;
            }

            EmailLog::create([
                'recipient'  => $recipient,
                'email_type' => $email_type,
                'sent_at'    => Carbon::now(),
                'error_at'   => Carbon::now(),
                'error'      => json_encode($e->getTrace()),
            ]);
        }

        // reset the application locale to the currently logged in user to prevent
        // the recipients culture from overriding the user's culture
        if (Auth::check()) {
            Localization::setApplicationLocale(Auth::user()->culture);
        }
    }
}