<![CDATA[<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .logo {
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3C686A;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0b2c2e;
        }
        .footer {
            margin-top: 30px;
            color: #777;
            font-size: 0.9em;
        }
        .footer p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/reusemartlogo.png') }}" alt="ReuseMart Logo" width="150">
        </div>
        <h1>Reset your password</h1>
        <p>Hey {{ $name }},</p>
        <p>Need to reset your password? No problem! Just click the button below and you'll be on your way. If you did not make this request, please ignore this email.</p>
        <a href="{{ $url }}" class="button">Reset your password</a>
        <div class="footer">
            <p>Problems or questions? Call us on 08123456780 or email support@reusemart.com</p>
            <p>&copy; {{ date('Y') }} ReuseMart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>