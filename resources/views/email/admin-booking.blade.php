<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempahan Baru Diterima - New Booking Received</title>
</head>

@php
    $logoUrl = asset('icon-white-red-bg.png');
    $supportEmail = 'info@keretaayah.myuni.agency';
    $whatsappNumber = '+60199694913';
    $whatsappUrl = 'https://wa.me/60199694913';
    $adminUrl = route('rentals.edit', $booking);
    $methodBm = $booking->rental_method === 'hourly' ? 'Sewaan Mengikut Jam' : 'Sewaan Harian';
    $methodEn = $booking->rental_method === 'hourly' ? 'Hourly Rental' : 'Daily Rental';
    $unitBm = $booking->rental_method === 'hourly' ? 'jam' : 'hari';
    $unitEn = $booking->rental_method === 'hourly' ? 'hours' : 'days';
@endphp

<body style="margin:0; padding:0; background:#fff7f7; font-family:Arial,Helvetica,sans-serif; color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff7f7; padding:32px 12px;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; max-width:640px; background:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #fee2e2;">
                    <tr>
                        <td style="background:#7f1d1d; padding:28px 28px 24px; text-align:center;">
                            <img src="{{ $logoUrl }}" alt="KeretaAyah" width="72" height="72" style="display:block; margin:0 auto 14px; border-radius:18px;">
                            <h1 style="margin:0; color:#ffffff; font-size:26px; line-height:1.2;">KeretaAyah Admin</h1>
                            <p style="margin:8px 0 0; color:#fecaca; font-size:14px;">Makluman tempahan baru / New booking alert</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 28px 8px;">
                            <p style="margin:0 0 8px; color:#dc2626; font-size:12px; font-weight:bold; letter-spacing:.08em; text-transform:uppercase;">Bahasa Melayu</p>
                            <h2 style="margin:0 0 14px; color:#7f1d1d; font-size:24px; line-height:1.25;">Tempahan baru diterima</h2>
                            <p style="margin:0 0 14px; font-size:15px; line-height:1.6;">Hai Admin,</p>
                            <p style="margin:0 0 18px; font-size:15px; line-height:1.6;">Satu tempahan kereta baru telah dibuat dalam sistem KeretaAyah. Sila semak butiran tempahan, pembayaran, dan status pelanggan sebelum tindakan seterusnya.</p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate; border-spacing:0; border:1px solid #fee2e2; border-radius:12px; overflow:hidden;">
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; width:38%; font-size:13px; color:#7f1d1d; font-weight:bold;">ID Tempahan</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->id }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Pelanggan</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->user->name }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">E-mel</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->user->email }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Kereta</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->car->name }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Kaedah Sewaan</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $methodBm }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Ambil</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->pickup_at->format('d M Y, g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Pulang</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ $booking->return_at->format('d M Y, g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Tempoh</td>
                                    <td style="padding:12px 14px; font-size:13px;">{{ number_format($booking->duration_units, 2) }} {{ $unitBm }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#fff1f2; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Jumlah</td>
                                    <td style="padding:12px 14px; font-size:13px; font-weight:bold; color:#dc2626;">RM {{ number_format($booking->total_cost, 2) }}</td>
                                </tr>
                            </table>

                            <div style="text-align:center; margin:26px 0 10px;">
                                <a href="{{ $adminUrl }}" style="display:inline-block; background:#dc2626; color:#ffffff; padding:13px 22px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:bold;">Lihat di Panel Admin</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 28px 30px;">
                            <div style="border-top:1px solid #fee2e2; padding-top:22px;">
                                <p style="margin:0 0 8px; color:#64748b; font-size:12px; font-weight:bold; letter-spacing:.08em; text-transform:uppercase;">English</p>
                                <h2 style="margin:0 0 14px; color:#7f1d1d; font-size:22px; line-height:1.25;">New booking received</h2>
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.6;">Hello Admin,</p>
                                <p style="margin:0 0 18px; font-size:15px; line-height:1.6;">A new car booking has been placed in KeretaAyah. Please review the booking details, payment, and customer status before taking the next action.</p>

                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate; border-spacing:0; border:1px solid #fee2e2; border-radius:12px; overflow:hidden;">
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; width:38%; font-size:13px; color:#7f1d1d; font-weight:bold;">Booking ID</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Customer</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Email</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Vehicle</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->car->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Rental Method</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $methodEn }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Pickup</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->pickup_at->format('d M Y, g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Return</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ $booking->return_at->format('d M Y, g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Duration</td>
                                        <td style="padding:12px 14px; font-size:13px;">{{ number_format($booking->duration_units, 2) }} {{ $unitEn }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background:#fff7f7; padding:12px 14px; font-size:13px; color:#7f1d1d; font-weight:bold;">Total</td>
                                        <td style="padding:12px 14px; font-size:13px; font-weight:bold; color:#dc2626;">RM {{ number_format($booking->total_cost, 2) }}</td>
                                    </tr>
                                </table>

                                <div style="text-align:center; margin:26px 0 0;">
                                    <a href="{{ $adminUrl }}" style="display:inline-block; background:#7f1d1d; color:#ffffff; padding:13px 22px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:bold;">View in Admin Panel</a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#111827; padding:24px 28px; color:#f9fafb; text-align:center;">
                            <p style="margin:0 0 8px; font-size:14px; font-weight:bold;">KeretaAyah Support</p>
                            <p style="margin:0 0 10px; font-size:13px; line-height:1.6; color:#d1d5db;">UMPSA Pekan student car rental support<br>Khidmat sokongan sewaan kereta pelajar UMPSA Pekan</p>
                            <p style="margin:0; font-size:13px; line-height:1.8;">
                                Email: <a href="mailto:{{ $supportEmail }}" style="color:#fecaca; text-decoration:none;">{{ $supportEmail }}</a><br>
                                WhatsApp: <a href="{{ $whatsappUrl }}" style="color:#fecaca; text-decoration:none;">{{ $whatsappNumber }}</a>
                            </p>
                            <p style="margin:14px 0 0; font-size:12px; color:#9ca3af;">&copy; {{ date('Y') }} KeretaAyah Admin System</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
