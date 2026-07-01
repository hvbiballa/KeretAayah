<?php

use App\Mail\AdminBookingNotification;
use App\Mail\BookConfirmMail;
use App\Models\Car;
use App\Models\CustomerVerification;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Notifications\PaymentStatusUpdatedNotification;
use App\Notifications\VerificationStatusUpdatedNotification;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$recipient = 'nnt.122349@gmail.com';

$user = new User();
$user->forceFill([
    'id' => 999001,
    'name' => 'Test Student',
    'email' => $recipient,
    'role' => 'customer',
]);
$user->exists = true;

$car = new Car();
$car->forceFill([
    'id' => 999002,
    'name' => 'KeretaAyah Test Sedan',
    'brand' => 'Perodua',
    'model' => 'Bezza',
    'year' => 2026,
    'car_type' => 'Sedan',
]);
$car->exists = true;

$booking = new Rental();
$booking->forceFill([
    'id' => 999999,
    'user_id' => $user->id,
    'car_id' => $car->id,
    'rental_method' => 'daily',
    'pickup_at' => now()->addDay()->setTime(10, 0),
    'return_at' => now()->addDays(2)->setTime(10, 0),
    'duration_units' => 1,
    'total_cost' => 75,
    'status' => 'ongoing',
]);
$booking->exists = true;
$booking->setRelation('user', $user);
$booking->setRelation('car', $car);

$payment = new Payment();
$payment->forceFill([
    'id' => 999003,
    'rental_id' => $booking->id,
    'amount_due' => 75,
    'amount_paid' => 75,
    'status' => Payment::STATUS_PAID,
    'payment_method' => Payment::METHOD_CASH,
]);
$payment->exists = true;
$payment->setRelation('rental', $booking);
$booking->setRelation('payment', $payment);

$verification = new CustomerVerification();
$verification->forceFill([
    'id' => 999004,
    'customer_id' => $user->id,
    'status' => CustomerVerification::STATUS_APPROVED,
    'review_note' => 'Synthetic test email only. No action needed.',
]);
$verification->exists = true;

Mail::mailer('noreply_mailer')
    ->to($recipient)
    ->send(new BookConfirmMail($booking));

Mail::mailer('noreply_mailer')
    ->to($recipient)
    ->send(new AdminBookingNotification($booking));

$user->notify(new PaymentStatusUpdatedNotification($payment));

$user->notify(new VerificationStatusUpdatedNotification(
    $verification,
    trans('email.verification_subjects.approved', [], 'ms'),
    trans('email.verification_messages.approved', [], 'ms'),
    trans('email.verification_subjects.approved', [], 'en'),
    trans('email.verification_messages.approved', [], 'en'),
));

$user->notify(new VerificationStatusUpdatedNotification(
    $verification,
    trans('email.verification_subjects.update_requested', [], 'ms'),
    trans('email.verification_messages.update_requested', [], 'ms'),
    trans('email.verification_subjects.update_requested', [], 'en'),
    trans('email.verification_messages.update_requested', [], 'en'),
));

$user->notify(new VerifyEmailNotification());
$user->notify(new ResetPasswordNotification('synthetic-test-token'));

echo "Sent fresh test email batch to {$recipient} using synthetic test data.\n";
