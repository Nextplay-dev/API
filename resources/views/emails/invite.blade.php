@extends('emails.layout')

@section('title', 'Invitation to ' . $activityName)

@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 32px; background-color: #111827;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="vertical-align: middle;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <div
                                            style="font-size: 28px; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 8px;">
                                            You're invited
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: #818cf8; line-height: 1.2;">
                                            to a {{ $activityName }}!
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding-right: 12px;">
                                                    <div
                                                        style="width: 44px; height: 44px; background-color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: bold; font-size: 16px;">
                                                        {{ substr($inviterName, 0, 1) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div style="font-size: 14px; font-weight: 600; color: #ffffff;">
                                                        {{ $inviterName }}
                                                    </div>
                                                    <div style="font-size: 12px; color: #9ca3af;">
                                                        invited you to join an activity.
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        @if(isset($venueImageUrl) && $venueImageUrl && (str_starts_with($venueImageUrl, 'http') || str_starts_with($venueImageUrl, '/')))
                            <td width="35%" style="padding-left: 20px; vertical-align: middle;" align="right">
                                <img src="{{ $venueImageUrl }}" width="100%"
                                    style="max-width: 160px; height: auto; border-radius: 16px; display: block;"
                                    alt="{{ $venueName }}">
                            </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 32px 32px 24px 32px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="border-bottom: 1px solid #f3f4f6; padding-bottom: 24px;">
                    <tr>
                        <td>
                            <div style="font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 4px;">
                                {{ $activityName }}
                            </div>
                            <div style="font-size: 14px; font-weight: bold; color: #111827;">
                                {{ $venueName }}
                            </div>
                            @if(isset($venueAddress) && $venueAddress)
                                <div style="font-size: 13px; color: #6b7280; margin-top: 2px;">
                                    {{ $venueAddress }}
                                </div>
                            @endif
                        </td>
                        <td align="right">
                            <span
                                style="background-color: {{ $categoryColor }}26; color: {{ $categoryColor }}; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 12px;">
                                {{ $capacity }} {{ $capacity > 1 ? 'players' : 'player' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 0 32px 24px 32px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="25%" valign="top" style="padding-right: 8px;">
                            <div
                                style="font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">
                                Date</div>
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">{{ $dateStr }}</div>
                        </td>
                        <td width="25%" valign="top"
                            style="padding-right: 8px; padding-left: 8px; border-left: 1px solid #f3f4f6;">
                            <div
                                style="font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">
                                Time</div>
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">{{ $timeStr }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 0 32px 40px 32px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" style="padding-right: 8px;">
                            <a href="{{ $acceptUrl }}"
                                style="display: block; background-color: #4f46e5; color: #ffffff; text-align: center; font-size: 16px; font-weight: 700; padding: 14px 24px; border-radius: 16px; text-decoration: none;">
                                Accept Invitation
                            </a>
                        </td>
                        <td width="50%" style="padding-left: 8px;">
                            <a href="{{ $declineUrl }}"
                                style="display: block; border: 2px solid #e5e7eb; color: #4f46e5; text-align: center; font-size: 16px; font-weight: 700; padding: 12px 24px; border-radius: 16px; text-decoration: none; background-color: #ffffff;">
                                Decline Invitation
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" style="padding-top: 16px; font-size: 13px; color: #9ca3af;">
                            Replying will notify {{ $inviterName }} and other guests.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection