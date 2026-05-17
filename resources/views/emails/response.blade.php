<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nextplay - Booking Invitation</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #0f172a;
        }

        .container {
            width: 100%;
            max-width: 480px;
            padding: 24px;
            box-sizing: border-box;
        }

        .card {
            background-color: #ffffff;
            border-radius: 32px;
            padding: 40px 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            text-align: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
        }

        .logo-img {
            width: 32px;
            height: 32px;
            margin-right: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
            font-size: 40px;
        }

        .status-icon.success {
            background-color: #f0fdf4;
            color: #15803d;
        }

        .status-icon.error {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .status-icon.info {
            background-color: #f1f5f9;
            color: #475569;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px 0;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin: 0 0 32px 0;
        }

        .details-card {
            background-color: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: left;
            border: 1px solid #f1f5f9;
        }

        .details-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .details-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
            color: #475569;
        }

        .details-row:last-child {
            margin-bottom: 0;
        }

        .details-row span {
            margin-right: 8px;
        }

        .btn {
            display: inline-block;
            width: 100%;
            background-color: #6564DB;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            padding: 16px;
            border-radius: 20px;
            box-sizing: border-box;
            transition: background-color 0.2s;
            font-size: 16px;
            box-shadow: 0 4px 6px -1px rgba(101, 100, 219, 0.2);
        }

        .btn:hover {
            background-color: #4f46e5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="logo-container">
                <img src="{{ asset('logo.svg') }}" class="logo-img" alt="Nextplay logo">
                <span class="logo-text">NextPlay</span>
            </div>

            @if($success)
                @if($status === 'accepted')
                    <h1>Invitation Accepted</h1>
                    <p>You've successfully joined the booking.<br>Get ready to play and have fun!</p>

                    <div class="details-card">
                        <div class="details-title">{{ $booking->activity->name }}</div>
                        <div class="details-row">
                            {{ $booking->start_at->format('l, F j, Y') }}
                        </div>
                        <div class="details-row">
                            {{ $booking->start_at->format('g:i A') }} – {{ $booking->end_at->format('g:i A') }}
                        </div>
                        <div class="details-row">
                            {{ $booking->resource->venue->name ?? 'Venue' }}
                        </div>
                    </div>
                @elseif($status === 'declined')
                    <h1>Invitation Declined</h1>
                    <p>You have declined the invitation.<br>If you change your mind, let the organizer know!</p>
                @else
                    <h1>Invitation Expired</h1>
                    <p>You have not responded to the invitation within the allowed time.<br>If you change your mind, let the
                        organizer know!</p>
                @endif
            @else
                <h1>Oops</h1>
                <p>{{ $message }}</p>
            @endif

            <a href="nextplay://" class="btn">Open in Nextplay App</a>
        </div>
    </div>
</body>

</html>