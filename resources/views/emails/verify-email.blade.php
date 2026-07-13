<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản FEA</title>
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
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn-verify {
            display: inline-block;
            background-color: #0056D2;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 86, 210, 0.15);
            transition: background-color 0.2s ease;
        }
        .btn-verify:hover {
            background-color: #0046b8;
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
        .break-all {
            word-break: break-all;
            color: #0056D2;
            text-decoration: none;
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
                <a href="{{ url('/') }}" class="logo">FEA</a>
            </div>

            <!-- Content -->
            <div class="content">
                <h1 class="greeting">Xin chào, {{ $user->name }}!</h1>
                <p class="text">
                    Cảm ơn bạn đã đăng ký tài khoản tại <strong>Website học trực tuyến FEA</strong>. 
                    Để hoàn tất việc kích hoạt tài khoản và mở khóa các tính năng như thanh toán khóa học, cập nhật hồ sơ và lưu danh sách yêu thích, vui lòng nhấn vào nút xác thực dưới đây:
                </p>

                <div class="btn-container">
                    <a href="{{ $url }}" class="btn-verify" target="_blank">Xác thực địa chỉ email</a>
                </div>

                <p class="text" style="font-size: 14px; color: #64748b;">
                    Liên kết xác thực này sẽ hết hạn trong vòng 60 phút. Nếu bạn không tạo tài khoản trên hệ thống của chúng tôi, bạn có thể bỏ qua email này.
                </p>

                <hr class="divider">

                <div class="footer">
                    <p style="margin-top: 0; margin-bottom: 8px;">Nếu nút ở trên không hoạt động, bạn có thể copy và dán liên kết dưới đây vào trình duyệt web:</p>
                    <a href="{{ $url }}" class="break-all" target="_blank">{{ $url }}</a>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} FEA. All rights reserved.
        </div>
    </div>
</body>
</html>
