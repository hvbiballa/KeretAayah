<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->mailer('auth_mailer')
            ->from(
                config('mail.auth_from.address'),
                config('mail.auth_from.name'),
            )
            ->subject(trans('email.reset_password_subject', [], 'ms') . ' / ' . trans('email.reset_password_subject', [], 'en'))
            ->view('email.customer-action', [
                'name' => $notifiable->name,
                'subjectBm' => trans('email.reset_password_subject', [], 'ms'),
                'subjectEn' => trans('email.reset_password_subject', [], 'en'),
                'bodyBm' => trans('email.reset_password_line', [], 'ms'),
                'bodyEn' => trans('email.reset_password_line', [], 'en'),
                'secondaryBm' => trans('email.reset_password_expiry', [
                    'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
                ], 'ms') . ' ' . trans('email.reset_password_ignore', [], 'ms'),
                'secondaryEn' => trans('email.reset_password_expiry', [
                    'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
                ], 'en') . ' ' . trans('email.reset_password_ignore', [], 'en'),
                'actionBm' => trans('email.reset_password_action', [], 'ms'),
                'actionEn' => trans('email.reset_password_action', [], 'en'),
                'actionUrl' => $this->resetUrl($notifiable),
            ]);
    }
}
