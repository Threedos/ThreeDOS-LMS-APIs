<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        .container {
            max-width: 520px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 14px 22px;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $user->name }}</h2>
        <p>You requested to reset your password.</p>

        <p style="text-align:center;">
            <a href="{{ $url }}" class="btn">Reset Password</a>
        </p>

        <p>This link will expire in 60 minutes.</p>

        <div class="footer">
            If you did not request this, ignore this email.
        </div>
    </div>
</body>
</html>
