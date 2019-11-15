<?php

namespace Momentum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Momentum\Interfaces\CanReceiveNotifications;
use App;
use Momentum\PulseSurvey;
use Momentum\PulseSurveyResult;
use Momentum\User;
use Momentum\Utilities\Localization;

class SendPulseSurvey extends Notification
{
    use Queueable;
    public $recipient;
    public $user;
    public $pulse_survey;
    public $pulse_survey_result;

    /**
     * Create a new notification instance.
     *
     * @param CanReceiveNotifications $recipient
     * @param User $user
     * @param PulseSurvey $pulse_survey
     * @param PulseSurveyResult $pulse_survey_result
     */
    public function __construct(
        CanReceiveNotifications $recipient,
        User $user,
        PulseSurvey $pulse_survey,
        PulseSurveyResult $pulse_survey_result
    ) {
        $this->recipient = $recipient;
        $this->user = $user;
        $this->pulse_survey = $pulse_survey;
        $this->pulse_survey_result = $pulse_survey_result;

        // set the applications locale to the user's culture we are sending
        // the notification to, instead of the current application locale
        Localization::setApplicationLocale($recipient->culture);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $recipient
     * @return array
     */
    public function via($recipient)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $recipient
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($recipient)
    {
        return (new MailMessage)
            ->subject(trans('pulse_survey.email.feedback.subject',
                ['full_name' => $this->user->full_name, 'due_date' => $this->pulse_survey->formatted_dates['due_at']['localized']]))
            ->line(trans('global.email.greeting.normal', ['recipient_full_name' => $this->recipient->full_name]))
            ->line(trans('pulse_survey.email.feedback.intro',
                ['full_name' => $this->user->full_name, 'due_date' => $this->pulse_survey->formatted_dates['due_at']['localized']]))
            ->line(trans('pulse_survey.email.feedback.colleague_label', ['first_name' => $this->user->first_name]))
            ->line($this->pulse_survey->message)
            ->action(trans('pulse_survey.email.feedback.button'),
                route('pulse-survey-result.edit', $this->pulse_survey_result->share_code))
            ->replyTo($this->user->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $recipient
     * @return array
     */
    public function toArray($recipient)
    {
        return [
            //
        ];
    }
}
