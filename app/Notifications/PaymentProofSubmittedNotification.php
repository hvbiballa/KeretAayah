<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProofSubmittedNotification extends Notification
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
            ->subject(__('email.payment_proof_subject'))
            ->greeting(__('email.hello') . ',')
            ->line(__('email.payment_proof_line'))
            ->line(__('email.customer') . ': ' . $this->payment->rental->user->name)
            ->line(__('email.vehicle') . ': ' . $this->payment->rental->car->name)
            ->line(__('email.amount_due') . ': RM ' . number_format($this->payment->amount_due, 2))
            ->action(__('email.review_payment'), route('payments.edit', $this->payment))
            ->line(__('email.review_uploaded_proof'));
    }
}
