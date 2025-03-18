<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
        }
        .header h1 {
            margin: 0;
        }
        .language-switcher a {
            color: white;
            margin-left: 10px;
            text-decoration: none;
        }
        .language-switcher a:hover {
            text-decoration: underline;
        }
        .hotel-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .hotel-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: calc(33.33% - 20px);
            padding: 15px;
            box-sizing: border-box;
        }
        .hotel-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .hotel-card h3 {
            margin: 10px 0;
            color: #333;
        }
        .hotel-card p {
            margin: 5px 0;
            color: #666;
        }
        .hotel-card .price {
            font-weight: bold;
            color: #007bff;
        }
        .hotel-card .rating {
            color: #f39c12;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hotel Booking</h1>
        <div class="language-switcher">
            <a href="?lang=vi">Tiếng Việt</a>
            <a href="?lang=en">English</a>
        </div>
    </div>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>