<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: block;
            text-align: center;
            padding: 24px 16px;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 8px 8px 0 0;
            width: 100%;
            box-sizing: border-box;
        }

        .header h1,
        .header p {
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 700;
        }

        .header p {
            margin-top: 4px;
            font-size: 16px;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            letter-spacing: 5px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
        }

        .warning {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Expense Tracker App</p>
        </div>
        <div class="content">
            <h2>Hello, {{ $user->name }}!</h2>
            <p>We received a request to verify your account. Use the OTP code below to complete the verification
                process.</p>
            <div class="otp-code">{{ $code }}</div>
            <p>This code will expire in <strong>10 minutes</strong>. Please enter it promptly.</p>
            <p class="warning">Do not share this code with anyone. Our team will never ask for your OTP.</p>
            <p>If you didn't request this verification, please ignore this email or contact our support team.</p>
        </div>
        <div class="footer">
            <p>Thank you for using {{ config('app.name') }}!</p>
            <p>&copy; 2024 {{ config('app.name') }}. All rights reserved.</p>
            <p>Need help? Contact us at support@expensetrackerapp.com</p>
        </div>
    </div>
</body>

</html>
