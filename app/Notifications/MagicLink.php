<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\Interfaces\CanReceiveNotifications;
use App;
use Momentum\Utilities\Localization;

class MagicLink extends Notification
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
            ->subject(trans('login.email.magic_link.subject'))
            ->line(trans('login.email.magic_link.intro'))
            ->action(trans('login.email.magic_link.button'), route('auth.magic', ['code' => $this->magic_link]))
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
