<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhắc nhở học tập - OnlineFEA</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .header {
            background-color: #0056D2;
            padding: 32px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
            text-decoration: none;
            margin: 0;
        }
        .content {
            padding: 40px 32px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            line-height: 24px;
            color: #475569;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .course-card {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .course-title {
            font-size: 16px;
            font-weight: 700;
            color: #0056D2;
            margin: 0 0 8px 0;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn-learn {
            display: inline-block;
            background-color: #0056D2;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 86, 210, 0.15);
        }
        .divider {
            border: 0;
            border-top: 1px solid #f1f5f9;
            margin: 32px 0;
        }
        .footer {
            padding: 0 32px 40px;
            font-size: 13px;
            line-height: 20px;
            color: #64748b;
        }
        .copyright {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 32px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <a href="{{ url('/') }}" class="logo">OnlineFEA</a>
            </div>

            <!-- Content -->
            <div class="content">
                <h1 class="greeting">Xin chào, {{ $user->name }}!</h1>
                <p class="text">
                    Đã lâu bạn chưa trở lại học tập trên <strong>OnlineFEA</strong>. Đừng để gián đoạn lộ trình học tập của mình nhé!
                </p>

                @if($courseTitle)
                <div class="course-card">
                    <div class="course-title">{{ $courseTitle }}</div>
                    <p class="text" style="margin: 0;">{{ $reminderMessage }}</p>
                </div>
                @else
                <p class="text">{{ $reminderMessage }}</p>
                @endif

                @if($url)
                <div class="btn-container">
                    <a href="{{ $url }}" class="btn-learn" target="_blank">Tiếp tục học ngay</a>
                </div>
                @endif

                <hr class="divider">

                <div class="footer">
                    <p style="margin: 0;">Email này được gửi tự động để hỗ trợ bạn duy trì thói quen học tập tốt mỗi ngày.</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} OnlineFEA. All rights reserved.
        </div>
    </div>
</body>
</html>
