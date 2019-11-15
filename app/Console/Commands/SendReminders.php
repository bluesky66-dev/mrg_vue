<?php

namespace Momentum\Console\Commands;

use Log;
use Exception;
use Illuminate\Console\Command;
use Momentum\ActionPlanReminder;
use Momentum\Notifications\ReminderEmail;

/**
 * Sends action plan reminders via email.
 *
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class SendReminders extends Command
{
    /**
     * Command.
     * @since 0.2.4
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * Description.
     * @since 0.2.4
     *
     * @var string
     */
    protected $description = 'Sends action plan reminders via email.';

    /**
     * Create a new command instance.
     * @since 0.2.4
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtains all plan reminders from the database in chunks/batches of 100 
     * and sends email reminders if the notification date matches today.
     * @since 0.2.4
     * @since 0.2.5 Added error logging.
     */
    public function handle()
    {
        try {
            ActionPlanReminder::chunk(100, function($reminders){
                foreach ($reminders as $reminder) {
                    $date = $reminder->getNextReminderDate();

                    // if the date is today, send the reminder
                    if ($date && $date->isToday()) {
                        $reminder->action_plan->user->sendNotification(
                            new ReminderEmail($reminder->action_plan->user, $reminder)
                        );
                    }
                }
            });
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
