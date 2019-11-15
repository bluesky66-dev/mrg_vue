<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\Interfaces\CanReceiveNotifications;
use Momentum\User;
use App;
use Momentum\Utilities\Localization;

class OnboardEmail extends Notification
{
    use Queueable;
    public $magic_link;
    public $recipient;

    /**
     * Create a new notification instance.
     *
     * @param CanReceiveNotifications $recipient
     * @param $magic_link
     */
    public function __construct(CanReceiveNotifications $recipient, $magic_link)
    {
        $this->magic_link = $magic_link;
        $this->recipient = $recipient;

        // set the applications locale to the user's culture we are sending
        // the notification to, instead of the current application locale
        Localization::setApplicationLocale($recipient->culture);
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
            ->subject(trans('global.email.onboard.subject'))
            ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
            ->line(trans('global.email.onboard.intro'))
            ->line(trans('global.email.onboard.body'))
            ->line(trans('global.email.onboard.footer'))
            ->action(trans('global.email.onboard.button'), route('auth.magic', ['code' => $this->magic_link]))
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
