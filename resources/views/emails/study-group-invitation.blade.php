<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lời mời tham gia nhóm học tập - OnlineFEA</title>
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
            background: linear-gradient(135deg, #0056D2 0%, #1e40af 100%);
            padding: 32px;
            text-align: center;
        }
        .logo {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
            text-decoration: none;
            margin: 0;
        }
        .content {
            padding: 36px 32px;
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
            margin-bottom: 20px;
        }
        .group-card {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0056D2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .group-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .group-meta {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 8px 0;
        }
        .group-desc {
            font-size: 14px;
            color: #334155;
            margin: 0;
            line-height: 20px;
        }
        .cta-container {
            text-align: center;
            margin-bottom: 28px;
        }
        .button {
            display: inline-block;
            background-color: #0056D2;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 86, 210, 0.25);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 18px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <a href="{{ config('app.url') }}" class="logo">OnlineFEA</a>
            </div>
            
            <div class="content">
                <h1 class="greeting">Xin chào, {{ $invitedUser->name }}!</h1>
                <p class="text">
                    <strong>{{ $inviter->name }}</strong> vừa gửi lời mời bạn tham gia nhóm học tập trên nền tảng <strong>OnlineFEA</strong>.
                </p>

                <div class="group-card">
                    <h3 class="group-title">{{ $studyGroup->name }}</h3>
                    <p class="group-meta">Khóa học: <strong>{{ $studyGroup->course->title }}</strong></p>
                    @if($studyGroup->description)
                        <p class="group-desc">{{ $studyGroup->description }}</p>
                    @endif
                </div>

                <div class="cta-container">
                    <a href="{{ $actionUrl }}" class="button">Xem & Chấp nhận lời mời</a>
                </div>

                <p class="text" style="font-size: 13px; color: #94a3b8; margin-bottom: 0;">
                    Lời mời có hiệu lực trong vòng 7 ngày. Nếu bạn không muốn tham gia, bạn có thể từ chối hoặc bỏ qua email này.
                </p>
            </div>

            <div class="footer">
                <p class="footer-text">
                    Email này được gửi tự động từ hệ thống Học trực tuyến OnlineFEA.<br>
                    &copy; {{ date('Y') }} OnlineFEA. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
