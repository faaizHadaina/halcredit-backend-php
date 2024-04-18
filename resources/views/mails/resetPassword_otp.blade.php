<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dddddd;
        }
        .header img {
            max-width: 150px; /* Logo size can be adjusted */
        }
        .header h2 {
            margin: 0;
            color: #333; /* Updated to be a visible color */
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 0.9em;
            background-color: #f8f8f8;
            border-top: 1px solid #dddddd;
        }
        .strong {
            font-size: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Logo Image -->
            <img src="https://halcredit-bucket.s3.us-east-1.amazonaws.com/user-profile-pictures/78w8t8c4we-logo.png" alt="Halcredit Logo">
            <h2>Change Password OTP</h2>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>We received a request to change your password. To ensure the security of your account, please use the following one-time verification code within the next 15 minutes:</p>
            <p>Verification Code: <strong class="strong">{{ $otp }}</strong></p>
            <p>If you did not initiate this password change or have any concerns, please contact our support team at support@halvestco.com immediately.</p>
            <p>Thank you for choosing Halvest.</p>
        </div>
        <div class="footer">
            <p>Best Regards</p>
            <p><strong><br>{{ config('app.name') }}</strong></p>
        </div>
    </div>
</body>
</html>
