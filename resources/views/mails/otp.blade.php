<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Your Account</title>
    <style>
        body, table, td, a {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header img {
            width: 120px;
            padding: 20px 0;
        }

        .content h1 {
            font-size: 24px;
            margin-bottom: 16px;
        }

        .content p {
            margin-bottom: 16px;
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }

        a.button {
            display: inline-block;
            margin: 0.5rem 0;
            padding: 12px 24px;
            border-radius: 4px;
            color: #ffffff;
            font-size: 18px;
            text-decoration: none;
            background-color: #000000;
        }

        .footer {
            color: #999;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://halcredit-bucket.s3.us-east-1.amazonaws.com/user-profile-pictures/78w8t8c4we-logo.png" alt="Halcredit">
        </div>
        <div class="content">
            <h1>Welcome, <%= user.name %>!</h1>
            <p>Thank you for joining Halcredit. To complete your registration, please click on the link below to verify your email.</p>
            <a href="<%= verificationLink %>" target="_blank" class="button">Verify Email</a>
            <p>If you didn't initiate this, feel free to ignore this email.</p>
            <p>Warm Regards, <br> Halcredit Team</p>
        </div>
        <div class="footer">
            &copy; 2023 Halcredit
        </div>
    </div>
</body>

</html>
