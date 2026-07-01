<?php

namespace App\Notifications;

use App\Models\CustomerVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private CustomerVerification $verification,
        private string $subject,
        private string $message,
        private ?string $subjectEn = null,
        private ?string $messageEn = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->mailer('noreply_mailer')
            ->from(config('mail.noreply_from.address'), config('mail.noreply_from.name'))
            ->subject($this->subject . ' / ' . ($this->subjectEn ?? $this->subject))
            ->view('email.customer-action', [
                'name' => $notifiable->name,
                'subjectBm' => $this->subject,
                'subjectEn' => $this->subjectEn ?? $this->subject,
                'bodyBm' => $this->message,
                'bodyEn' => $this->messageEn ?? $this->message,
                'noteBm' => $this->verification->review_note
                    ? trans('email.admin_note', [], 'ms') . ': ' . $this->verification->review_note
                    : null,
                'noteEn' => $this->verification->review_note
                    ? trans('email.admin_note', [], 'en') . ': ' . $this->verification->review_note
                    : null,
                'secondaryBm' => trans('email.thanks_using', [], 'ms'),
                'secondaryEn' => trans('email.thanks_using', [], 'en'),
                'actionBm' => trans('email.view_verification', [], 'ms'),
                'actionEn' => trans('email.view_verification', [], 'en'),
                'actionUrl' => route('customer.verification.index'),
            ]);
    }
}
