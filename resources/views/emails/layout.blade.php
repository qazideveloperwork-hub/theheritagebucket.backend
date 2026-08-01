<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Heritage Basket' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#faf4ec; font-family: Georgia, 'Times New Roman', serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf4ec; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border:1px solid #e6d9c5;">
                    <tr>
                        <td align="center" style="padding:28px 32px; border-bottom:1px solid #e6d9c5;">
                            <span style="font-family: Georgia, serif; font-size:22px; letter-spacing:0.5px;">
                                <span style="color:#a5360d; font-weight:bold;">Heritage</span>
                                <span style="color:#97472d; font-weight:bold;">Basket</span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px; font-family: Helvetica, Arial, sans-serif; font-size:15px; line-height:1.6; color:#1b1b18;">
                            {{ $slot }}
                        </td>
                    </tr>
                    @isset($showcase)
                        <tr>
                            <td style="padding:0 32px 32px;">
                                {{ $showcase }}
                            </td>
                        </tr>
                    @endisset
                    <tr>
                        <td align="center" style="padding:24px 32px; border-top:1px solid #e6d9c5; font-family: Helvetica, Arial, sans-serif; font-size:12px; color:#8a6c56;">
                            &copy; {{ date('Y') }} Heritage Basket. Handcrafted with love in Pakistan.<br>
                            You're receiving this email because you have an order with Heritage Basket.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
