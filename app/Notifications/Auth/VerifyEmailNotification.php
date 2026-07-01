<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->mailer('auth_mailer')
            ->from(
                config('mail.auth_from.address'),
                config('mail.auth_from.name'),
            )
            ->subject(trans('email.verify_email_subject', [], 'ms') . ' / ' . trans('email.verify_email_subject', [], 'en'))
            ->view('email.customer-action', [
                'name' => $notifiable->name,
                'subjectBm' => trans('email.verify_email_subject', [], 'ms'),
                'subjectEn' => trans('email.verify_email_subject', [], 'en'),
                'bodyBm' => trans('email.verify_email_line', [], 'ms'),
                'bodyEn' => trans('email.verify_email_line', [], 'en'),
                'secondaryBm' => trans('email.verify_email_ignore', [], 'ms'),
                'secondaryEn' => trans('email.verify_email_ignore', [], 'en'),
                'actionBm' => trans('email.verify_email_action', [], 'ms'),
                'actionEn' => trans('email.verify_email_action', [], 'en'),
                'actionUrl' => $this->verificationUrl($notifiable),
            ]);
    }
}
