<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\ActionPlan;
use Momentum\Interfaces\CanReceiveNotifications;
use App;
use Momentum\Utilities\Localization;

class ShareActionPlan extends Notification
{
    use Queueable;
    public $recipient;
    public $action_plan;

    /**
     * Create a new notification instance.
     *
     * @param CanReceiveNotifications $recipient
     * @param ActionPlan $action_plan
     */
    public function __construct(CanReceiveNotifications $recipient, ActionPlan $action_plan, $file)
    {
        $this->recipient = $recipient;

        // set the applications locale to the user's culture we are sending
        // the notification to, instead of the current application locale
        Localization::setApplicationLocale($recipient->culture);
        $this->action_plan = $action_plan;
        $this->file = $file;
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
        return (new MailMessage)
            ->subject(trans('action_plan.email.share.subject', ['full_name' => $this->action_plan->user->full_name]))
            ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
            ->line(trans('action_plan.email.share.intro', ['full_name' => $this->action_plan->user->full_name]))
            ->attach($this->file, [
                'as'   => 'action-plan.pdf',
                'mime' => 'application/pdf',
            ])
            ->replyTo($this->action_plan->user->email);
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
