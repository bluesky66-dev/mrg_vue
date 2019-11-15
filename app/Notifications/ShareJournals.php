<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\Interfaces\CanReceiveNotifications;
use App;
use Momentum\User;
use Momentum\Utilities\Localization;

class ShareJournals extends Notification
{
    use Queueable;
    public $recipient;
    public $user;
    public $file;

    /**
     * Create a new notification instance.
     *
     * @param CanReceiveNotifications $recipient
     * @param User $user
     * @param $file
     */
    public function __construct(CanReceiveNotifications $recipient, User $user, $file)
    {
        $this->recipient = $recipient;

        // set the applications locale to the user's culture we are sending
        // the notification to, instead of the current application locale
        Localization::setApplicationLocale($recipient->culture);
        $this->user = $user;
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
            ->subject(trans('journal.email.share.subject', ['full_name' => $this->user->full_name]))
            ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
            ->line(trans('journal.email.share.intro', ['full_name' => $this->user->full_name]))
            ->attach($this->file, [
                'as'   => 'journal-entries.pdf',
                'mime' => 'application/pdf',
            ])
            ->replyTo($this->user->email);
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
