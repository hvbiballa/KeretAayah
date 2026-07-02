<?php

namespace App\Notifications\Payment;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Payment $payment)
    {
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
            ->subject(trans('email.payment_status_subject', [], 'ms') . ' / ' . trans('email.payment_status_subject', [], 'en'))
            ->view('email.customer-action', [
                'name' => $notifiable->name,
                'subjectBm' => trans('email.payment_status_subject', [], 'ms'),
                'subjectEn' => trans('email.payment_status_subject', [], 'en'),
                'bodyBm' => trans('email.payment_status_line', [], 'ms'),
                'bodyEn' => trans('email.payment_status_line', [], 'en'),
                'noteBm' => trans('email.status', [], 'ms') . ': '
                    . trans('payment.statuses.' . $this->payment->status, [], 'ms') . ' | '
                    . trans('email.amount_paid', [], 'ms') . ': RM ' . number_format($this->payment->amount_paid, 2),
                'noteEn' => trans('email.status', [], 'en') . ': '
                    . trans('payment.statuses.' . $this->payment->status, [], 'en') . ' | '
                    . trans('email.amount_paid', [], 'en') . ': RM ' . number_format($this->payment->amount_paid, 2),
                'secondaryBm' => trans('email.payment_manual_review', [], 'ms'),
                'secondaryEn' => trans('email.payment_manual_review', [], 'en'),
                'actionBm' => trans('email.view_booking', [], 'ms'),
                'actionEn' => trans('email.view_booking', [], 'en'),
                'actionUrl' => route('bookings.show', $this->payment->rental),
            ]);
    }
}
