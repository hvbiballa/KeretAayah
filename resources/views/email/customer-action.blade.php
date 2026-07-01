<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectBm }} - {{ $subjectEn }}</title>
</head>

@php
    $logoUrl = asset('icon-white-red-bg.png');
    $supportEmail = 'info@keretaayah.myuni.agency';
    $whatsappNumber = '+60199694913';
    $whatsappUrl = 'https://wa.me/60199694913';
@endphp

<body style="margin:0; padding:0; background:#fff7f7; font-family:Arial,Helvetica,sans-serif; color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff7f7; padding:32px 12px;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; max-width:640px; background:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #fee2e2;">
                    <tr>
                        <td style="background:#7f1d1d; padding:28px 28px 24px; text-align:center;">
                            <img src="{{ $logoUrl }}" alt="KeretaAyah" width="72" height="72" style="display:block; margin:0 auto 14px; border-radius:18px;">
                            <h1 style="margin:0; color:#ffffff; font-size:26px; line-height:1.2;">KeretaAyah</h1>
                            <p style="margin:8px 0 0; color:#fecaca; font-size:14px;">Sewaan kereta pelajar UMPSA Pekan</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 28px 8px;">
                            <p style="margin:0 0 8px; color:#dc2626; font-size:12px; font-weight:bold; letter-spacing:.08em; text-transform:uppercase;">Bahasa Melayu</p>
                            <h2 style="margin:0 0 14px; color:#7f1d1d; font-size:24px; line-height:1.25;">{{ $subjectBm }}</h2>
                            <p style="margin:0 0 14px; font-size:15px; line-height:1.6;">Hai <strong>{{ $name }}</strong>,</p>
                            <p style="margin:0 0 18px; font-size:15px; line-height:1.6;">{{ $bodyBm }}</p>

                            @isset($noteBm)
                                <div style="margin:0 0 18px; padding:14px 16px; background:#fff1f2; border:1px solid #fecaca; border-radius:12px; color:#7f1d1d; font-size:14px; line-height:1.6;">
                                    {{ $noteBm }}
                                </div>
                            @endisset

                            @isset($secondaryBm)
                                <p style="margin:0 0 18px; font-size:14px; line-height:1.6; color:#475569;">{{ $secondaryBm }}</p>
                            @endisset

                            <div style="text-align:center; margin:26px 0 10px;">
                                <a href="{{ $actionUrl }}" style="display:inline-block; background:#dc2626; color:#ffffff; padding:13px 22px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:bold;">{{ $actionBm }}</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 28px 30px;">
                            <div style="border-top:1px solid #fee2e2; padding-top:22px;">
                                <p style="margin:0 0 8px; color:#64748b; font-size:12px; font-weight:bold; letter-spacing:.08em; text-transform:uppercase;">English</p>
                                <h2 style="margin:0 0 14px; color:#7f1d1d; font-size:22px; line-height:1.25;">{{ $subjectEn }}</h2>
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.6;">Hi <strong>{{ $name }}</strong>,</p>
                                <p style="margin:0 0 18px; font-size:15px; line-height:1.6;">{{ $bodyEn }}</p>

                                @isset($noteEn)
                                    <div style="margin:0 0 18px; padding:14px 16px; background:#fff7f7; border:1px solid #fee2e2; border-radius:12px; color:#7f1d1d; font-size:14px; line-height:1.6;">
                                        {{ $noteEn }}
                                    </div>
                                @endisset

                                @isset($secondaryEn)
                                    <p style="margin:0 0 18px; font-size:14px; line-height:1.6; color:#475569;">{{ $secondaryEn }}</p>
                                @endisset

                                <div style="text-align:center; margin:26px 0 0;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block; background:#7f1d1d; color:#ffffff; padding:13px 22px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:bold;">{{ $actionEn }}</a>
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
                            <p style="margin:14px 0 0; font-size:12px; color:#9ca3af;">&copy; {{ date('Y') }} KeretaAyah. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
