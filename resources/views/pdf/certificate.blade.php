<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chứng chỉ {{ $certificate->certificate_code }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: "DejaVu Sans", sans-serif;
            background-color: #fdfbf7;
            color: #2d3748;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: block;
        }
        .cert-container {
            position: relative;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding: 40px;
            text-align: center;
        }
        .cert-border {
            border: 15px double #b7791f; /* Gold border */
            height: calc(100% - 30px);
            box-sizing: border-box;
            padding: 40px;
            background-color: #ffffff;
            position: relative;
        }
        .cert-header {
            margin-top: 15px;
        }
        .cert-logo {
            font-size: 28px;
            font-weight: bold;
            color: #4c51bf; /* Violet */
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .cert-subtitle {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #718096;
            margin-bottom: 25px;
        }
        .cert-title {
            font-size: 36px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .cert-recipient-label {
            font-size: 16px;
            font-style: italic;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .cert-recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #2b6cb0; /* Accent Blue */
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            display: inline-block;
            padding-bottom: 8px;
            min-width: 300px;
        }
        .cert-course-label {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .cert-course-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 40px;
            font-style: italic;
        }
        .cert-footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
        }
        .footer-col {
            float: left;
            width: 33.33%;
            text-align: center;
        }
        .footer-label {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .footer-value {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
        }
        .signature-line {
            width: 150px;
            border-top: 1px solid #cbd5e0;
            margin: 15px auto 5px auto;
        }
        .stamp-placeholder {
            width: 60px;
            height: 60px;
            border: 2px dashed #b7791f;
            border-radius: 50%;
            margin: 0 auto;
            line-height: 56px;
            font-size: 10px;
            color: #b7791f;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-border">
            <div class="cert-header">
                <div class="cert-logo">OnlineFEA</div>
                <div class="cert-subtitle">Hệ thống Đào tạo Trực tuyến</div>
            </div>

            <div class="cert-title">Chứng nhận hoàn thành</div>

            <div class="cert-recipient-label">Chứng nhận học viên</div>
            <div class="cert-recipient-name">{{ $user->name }}</div>

            <div class="cert-course-label">Đã hoàn thành xuất sắc khóa học</div>
            <div class="cert-course-title">"{{ $course->title }}"</div>

            <div class="cert-footer">
                <div class="footer-col">
                    <div class="footer-label">Mã số chứng chỉ</div>
                    <div class="footer-value" style="font-family: monospace;">{{ $certificate->certificate_code }}</div>
                </div>
                <div class="footer-col">
                    <div class="stamp-placeholder">FEA</div>
                    <div class="footer-label" style="margin-top: 5px;">Hội đồng Thẩm định</div>
                </div>
                <div class="footer-col">
                    <div class="footer-label">Ngày cấp chứng chỉ</div>
                    <div class="footer-value">{{ $certificate->issued_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
