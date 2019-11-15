<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\ActionPlanReminder;
use Momentum\Enums\ActionPlanReminderTypes;
use Momentum\Interfaces\CanReceiveNotifications;
use App;
use Momentum\Utilities\Localization;

class ReminderEmail extends Notification
{
    use Queueable;
    public $user;
    public $action_plan_reminder;

    /**
     * Create a new notification instance.
     *
     * @param CanReceiveNotifications $recipient
     * @param ActionPlanReminder $action_plan_reminder
     */
    public function __construct(CanReceiveNotifications $recipient, ActionPlanReminder $action_plan_reminder)
    {
        $this->recipient = $recipient;

        // set the applications locale to the user's culture we are sending
        // the notification to, instead of the current application locale
        Localization::setApplicationLocale($recipient->culture);
        $this->action_plan_reminder = $action_plan_reminder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->action_plan_reminder->type == ActionPlanReminderTypes::REVIEW) {
            return (new MailMessage)
                ->subject(trans('action_plan.email.reminders.review.subject'))
                ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
                ->line(trans('action_plan.email.reminders.review.intro', ['action_plan_name' => $this->action_plan_reminder->action_plan->label]))
                ->action(trans('action_plan.email.reminders.review.button'),
                    route('action-plans.edit', ['id' => $this->action_plan_reminder->action_plan->id]))
                ->replyTo(config('mail.reply_to'));
        }

        if ($this->action_plan_reminder->type == ActionPlanReminderTypes::PULSE_SURVEYS) {
            return (new MailMessage)
                ->subject(trans('action_plan.email.reminders.pulse_surveys.subject'))
                ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
                ->line(trans('action_plan.email.reminders.pulse_surveys.intro'))
                ->action(trans('action_plan.email.reminders.pulse_surveys.button'), route('pulse-surveys.create'))
                ->replyTo(config('mail.reply_to'));
        }

        if ($this->action_plan_reminder->type == ActionPlanReminderTypes::ACTION_STEP && $this->action_plan_reminder->action_step !== null) {
            return (new MailMessage)
                ->subject(trans('action_plan.email.reminders.action_step.subject'))
                ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
                ->line(trans('action_plan.email.reminders.action_step.intro'))
                ->line($this->action_plan_reminder->action_step->name_processed)
                ->line($this->action_plan_reminder->action_step->description_processed)
                ->action(trans('action_plan.email.reminders.action_step.button'),
                    route('action-plans.edit', ['id' => $this->action_plan_reminder->action_plan->id]))
                ->replyTo(config('mail.reply_to'));
        }

        return (new MailMessage)
            ->subject(trans('action_plan.email.reminders.generic.subject'))
            ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
            ->line(trans('action_plan.email.reminders.generic.intro'))
            ->action(trans('action_plan.email.reminders.generic.button'),
                route('action-plans.edit', ['id' => $this->action_plan_reminder->action_plan->id]))
            ->replyTo(config('mail.reply_to'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
