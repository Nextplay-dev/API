<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nextplay')</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="background-color: #f9fafb; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 24px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
                    <tr>
                        <td style="padding: 24px 32px; background-color: #ffffff; border-bottom: 1px solid #f3f4f6;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align: middle; padding-right: 8px;">
                                                    <img src="{{ asset('logo.svg') }}" width="28" height="28"
                                                        style="display: block;" alt="Next Play">
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <span
                                                        style="font-size: 24px; font-weight: 800; color: #111827; letter-spacing: -0.5px; line-height: 1;">NextPlay</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px; background-color: #f9fafb; border-top: 1px solid #f3f4f6;"
                            align="center">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            style="margin: 0 auto 8px auto;">
                                            <tr>
                                                <td style="vertical-align: middle; padding-right: 6px;">
                                                    <img src="{{ asset('logo.svg') }}" width="20" height="20"
                                                        style="display: block;" alt="NextPlay">
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <span
                                                        style="font-size: 18px; font-weight: 800; color: #111827; letter-spacing: -0.5px; line-height: 1;">NextPlay</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-top: 1px solid #e5e7eb; padding-top: 16px; font-size: 12px; color: #9ca3af; text-align: center;">
                                        Questions? Contact us at <a href="mailto:hello@nextplay.app"
                                            style="color: #4f46e5; text-decoration: none;">hello@nextplay.app</a><br><br>
                                        If you don't want to receive these emails, you can unsubscribe.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>