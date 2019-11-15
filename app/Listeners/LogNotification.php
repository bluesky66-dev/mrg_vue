<?php

namespace Momentum\Listeners;

use Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Momentum\EmailLog;

/**
 * Listens to `NotificationSent` event to add a record log in the database.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class LogNotification
{
    /**
     * Handle the event.
     * @since 0.2.4
     * @since 0.2.5 Logging exceptions.
     *
     * @param NotificationSent|object $event
     */
    public function handle(NotificationSent $event)
    {
        try {
            // get the user from the notification
            $user = $event->notification->recipient;
            $recipient = null;
            $reflection = new \ReflectionClass($event->notification);
            $email_type = Str::snake($reflection->getShortName());

            if ($user !== null) {
                $recipient = $user->email;
            }

            EmailLog::create([
                'recipient'  => $recipient,
                'email_type' => $email_type,
                'sent_at'    => Carbon::now(),
            ]);
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
