<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Invitation</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h1 style="color: #2c3e50; text-align: center; margin-bottom: 10px;">You're Invited!</h1>
        <p style="text-align: center; color: #666; font-size: 16px;">Company Event Invitation</p>
    </div>

    <div style="background-color: white; padding: 30px; border: 1px solid #ddd; border-radius: 8px;">

        <p style="font-size: 16px; margin-bottom: 20px;">
            Dear <strong>{{ $user_email }}</strong>,
        </p>

        <p style="font-size: 16px; margin-bottom: 25px;">
            We are pleased to invite you to our upcoming company event. Your presence would be greatly valued as we
            celebrate our achievements and discuss future opportunities.
        </p>
        <div style="background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 25px 0;">
            <p style="margin: 0; font-size: 14px; color: #555;">
                <strong>Your Details:</strong><br>
                Name: {{ $name }}<br>
                Email: {{ $email }}<br>
                Address: {{ $address }}
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}"
                style="background-color: #3498db; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Register Now
            </a>
        </div>

    </div>

</body>

</html>
