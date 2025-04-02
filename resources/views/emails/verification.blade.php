<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận email đăng ký</title>
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
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            color: #333;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác nhận email</h1>
        </div>
        <div class="content">
            <p>Chào {{ $user->fullname }},</p>
            <p>Cảm ơn bạn đã đăng ký! Vui lòng nhấp vào nút bên dưới để kích hoạt tài khoản của bạn:</p>
            <a href="{{ $verificationUrl }}" class="button">Kích hoạt tài khoản</a>
            <p>Nếu nút không hoạt động, bạn có thể sao chép và dán liên kết sau vào trình duyệt:</p>
            <p>{{ $verificationUrl }}</p>
        </div>
        <div class="footer">
            <p>Trân trọng,<br>{{ env('APP_NAME') }}</p>
            <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email.</p>
        </div>
    </div>
</body>
</html>