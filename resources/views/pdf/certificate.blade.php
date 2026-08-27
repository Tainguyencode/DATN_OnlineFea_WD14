@if(isset($isPublic) && $isPublic)
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate - {{ $certificate->certificate_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700;800&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .font-heading { font-family: 'Montserrat', sans-serif; }
        .font-signature { font-family: 'Great Vibes', cursive; }
        
        .cert-container {
            animation: certFadeIn 0.5s ease-out forwards;
        }
        @keyframes certFadeIn {
            0% { opacity: 0; transform: scale(0.96); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        /* Double border */
        .double-border {
            border: 2px solid #D4A24C;
            position: relative;
        }
        .double-border::before {
            content: '';
            position: absolute;
            top: 8px; bottom: 8px; left: 8px; right: 8px;
            border: 1px solid #D4A24C;
        }
        
        /* Corner ornaments */
        .corner {
            position: absolute;
            width: 25px; height: 25px;
            border-top: 2px solid #D4A24C;
            border-left: 2px solid #D4A24C;
            border-top-left-radius: 100%;
        }
        .corner-tl { top: -2px; left: -2px; }
        .corner-tr { top: -2px; right: -2px; transform: rotate(90deg); }
        .corner-bl { bottom: -2px; left: -2px; transform: rotate(-90deg); }
        .corner-br { bottom: -2px; right: -2px; transform: rotate(180deg); }
    </style>
</head>
<body class="min-h-screen py-10 px-4 md:px-8">
    <div class="max-w-[1200px] mx-auto">
        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <a href="javascript:history.back()" class="flex items-center text-gray-500 hover:text-[#3B5BDB] transition font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
            
            <div class="flex items-start gap-3">
                <div>
                    <button type="button" onclick="copyCertificateLink()" aria-describedby="certificate-copy-status" class="flex items-center px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition font-medium text-sm">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        Share
                    </button>
                    <p id="certificate-copy-status" class="mt-1 min-h-4 text-xs font-medium text-gray-600" role="status" aria-live="polite"></p>
                </div>
                <a href="{{ route('certificates.public.pdf', ['code' => $certificate->certificate_code, 'download' => 1]) }}" class="flex items-center px-4 py-2.5 bg-white text-[#3B5BDB] border border-[#3B5BDB]/20 rounded-lg shadow-sm hover:bg-blue-50 transition font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF
                </a>
                <a href="{{ route('certificates.public', $certificate->certificate_code) }}" class="flex items-center px-4 py-2.5 bg-[#3B5BDB] text-white rounded-lg shadow-sm hover:bg-blue-700 transition font-medium text-sm">
                    <svg class="w-4 h-4 mr-2 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Verify Certificate
                </a>
            </div>
        </div>

        <!-- Certificate Frame -->
        <div class="cert-container bg-white shadow-xl rounded-xl p-6 md:p-10 border border-gray-100 relative overflow-hidden">
            <!-- Inner double border -->
            <div class="double-border w-full h-full p-8 md:p-16 flex flex-col items-center justify-between min-h-[750px]">
                
                <!-- Ornaments -->
                <div class="corner corner-tl bg-white"></div>
                <div class="corner corner-tr bg-white"></div>
                <div class="corner corner-bl bg-white"></div>
                <div class="corner corner-br bg-white"></div>

                <!-- Header -->
                <div class="text-center w-full mt-2">
                    <h1 class="text-[#3B5BDB] text-5xl md:text-[3.5rem] font-heading font-bold tracking-tight mb-4">OnlineFEA</h1>
                    <h2 class="text-[#1a202c] text-3xl md:text-[2.2rem] font-heading font-bold uppercase tracking-widest mb-8">CERTIFICATE OF COMPLETION</h2>
                    
                    <div class="flex justify-center items-center gap-3 mb-10">
                        <div class="w-24 h-[1px] bg-[#D4A24C]"></div>
                        <div class="w-2.5 h-2.5 rotate-45 border border-[#D4A24C] flex items-center justify-center">
                            <div class="w-1 h-1 bg-[#D4A24C]"></div>
                        </div>
                        <div class="w-24 h-[1px] bg-[#D4A24C]"></div>
                    </div>
                    
                    <p class="text-gray-500 text-lg md:text-xl mb-6">This certificate is proudly presented to</p>
                    
                    <!-- Student Name -->
                    <h3 class="text-5xl md:text-[4rem] font-heading font-extrabold text-[#111827] mb-8 tracking-wide">
                        {{ $user->name }}
                    </h3>
                    
                    <p class="text-gray-500 text-lg md:text-xl mb-8">for successfully completing</p>
                    
                    <!-- Course Name -->
                    <h4 class="text-3xl md:text-4xl font-bold italic text-gray-800 mb-16 px-4 md:px-20 leading-tight">
                        "{{ $course->title }}"
                    </h4>
                </div>

                <!-- Footer Columns -->
                <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-8 items-end mt-auto px-4 md:px-8">
                    
                    <!-- Col 1: Instructor -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-orange-50/50 text-[#D4A24C] flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <p class="font-bold text-gray-900 text-[13px] mb-6 uppercase tracking-wider">Instructor</p>
                        <p class="font-signature text-[2.5rem] text-gray-800 leading-[0.5] mb-2">{{ $course->instructor->name ?? 'Instructor Name' }}</p>
                        <div class="w-full max-w-[180px] h-[1px] bg-gray-300 mb-2 mt-4"></div>
                        <p class="text-[11px] text-gray-500">(Ký tên)</p>
                    </div>

                    <!-- Col 2: Info -->
                    <div class="flex flex-col items-center text-center pb-1">
                        <div class="w-14 h-14 rounded-full bg-orange-50/50 text-[#D4A24C] flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="font-bold text-gray-900 text-[13px] mb-2 uppercase tracking-wider">Issued on</p>
                        <p class="text-gray-700 font-medium mb-8">{{ $certificate->issued_at->format('d F Y') }}</p>
                        
                        <p class="font-bold text-gray-900 text-[13px] mb-2 uppercase tracking-wider">Certificate ID</p>
                        <p class="text-gray-700 font-medium text-sm">{{ $certificate->certificate_code }}</p>
                    </div>

                    <!-- Col 3: CEO -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-orange-50/50 text-[#D4A24C] flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 7l3 5 5-7 5 7 3-5v11H4V7z"></path></svg>
                        </div>
                        <p class="font-bold text-gray-900 text-[13px] mb-6 uppercase tracking-wider">CEO OnlineFEA</p>
                        <p class="font-signature text-[2.5rem] text-gray-800 leading-[0.5] mb-2">Hoàng Tuấn Tú</p>
                        <div class="w-full max-w-[180px] h-[1px] bg-gray-300 mb-2 mt-4"></div>
                        <p class="text-[11px] text-gray-500">(Ký tên)</p>
                    </div>

                    <!-- Col 4: QR -->
                    <div class="flex flex-col items-center text-center pb-2">
                        <p class="font-bold text-gray-900 text-[13px] mb-3 uppercase tracking-wider">QR Verify</p>
                        <div class="p-2 border border-gray-200 rounded-lg bg-white shadow-sm inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('certificates.public', $certificate->certificate_code)) }}" alt="QR Code" class="w-24 h-24">
                        </div>
                        <a href="{{ route('certificates.public', $certificate->certificate_code) }}" target="_blank" class="mt-3 text-[11px] text-[#3B5BDB] hover:underline break-all max-w-[180px] leading-tight font-medium">
                            {{ route('certificates.public', $certificate->certificate_code) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        async function copyCertificateLink() {
            const status = document.getElementById('certificate-copy-status');

            try {
                if (!navigator.clipboard?.writeText) {
                    throw new Error('Clipboard API unavailable');
                }

                await navigator.clipboard.writeText(window.location.href);
                status.textContent = 'Đã sao chép liên kết chứng chỉ.';
            } catch (error) {
                console.error('Certificate link copy failed.', error);
                status.textContent = 'Không thể sao chép tự động. Vui lòng sao chép liên kết trên thanh địa chỉ.';
            }
        }
    </script>
</body>
</html>
@else
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chứng chỉ {{ $certificate->certificate_code }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Great+Vibes&family=Alex+Brush&family=Allura&family=Dancing+Script:wght@400;500;600;700&display=swap');
        
        @page {
            size: A4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Poppins', 'Inter', "DejaVu Sans", sans-serif;
            background-color: #ffffff;
            color: #1F2937;
            line-height: 1.15;
            overflow: hidden;
        }
        
        /* Khung: Border cách mép giấy khoảng 25px */
        .double-border {
            position: absolute;
            top: 25px;
            bottom: 25px;
            left: 25px;
            right: 25px;
            border: 2px solid #D4A24C;
        }
        .double-border-inner {
            position: absolute;
            top: 6px;
            bottom: 6px;
            left: 6px;
            right: 6px;
            border: 1px solid #D4A24C;
            text-align: center;
        }
        
        /* 4 góc bo trang trí */
        .corner {
            position: absolute;
            width: 35px; height: 35px;
            background-color: #ffffff;
        }
        .corner-tl { top: -2px; left: -2px; border-top: 2px solid #D4A24C; border-left: 2px solid #D4A24C; border-top-left-radius: 35px; }
        .corner-tr { top: -2px; right: -2px; border-top: 2px solid #D4A24C; border-right: 2px solid #D4A24C; border-top-right-radius: 35px; }
        .corner-bl { bottom: -2px; left: -2px; border-bottom: 2px solid #D4A24C; border-left: 2px solid #D4A24C; border-bottom-left-radius: 35px; }
        .corner-br { bottom: -2px; right: -2px; border-bottom: 2px solid #D4A24C; border-right: 2px solid #D4A24C; border-bottom-right-radius: 35px; }

        /* Giảm padding-top lớn */
        .content-wrapper {
            padding-top: 10px;
        }

        /* Màu: Logo #3B5BDB, Text #1F2937, Border #D4A24C */
        .cert-logo {
            font-size: 41px;
            font-weight: 700;
            color: #3B5BDB;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
        }
        .cert-title {
            font-size: 25px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .cert-separator {
            color: #D4A24C;
            font-size: 26px;
            margin-bottom: 8px;
        }
        
        /* Giảm khoảng cách dọc khoảng 30% */
        .cert-recipient-label {
            font-size: 18px;
            color: #4B5563;
            margin-bottom: 5px;
        }
        .cert-recipient-name {
            font-size: 50px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
        }
        .cert-course-label {
            font-size: 18px;
            color: #4B5563;
            margin-bottom: 5px;
        }
        .cert-course-title {
            font-size: 34px;
            font-weight: 700;
            color: #1F2937;
            font-style: italic;
            padding: 0 50px;
        }
        
        /* BOTTOM GRID */
        .cert-footer-table {
            width: 100%;
            table-layout: fixed;
            margin: 0;
        }
        .cert-footer-table td {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        
        /* ICON: 36px màu vàng */
        .icon-wrapper {
            margin-bottom: 3px;
        }
        
        .footer-label {
            font-size: 13px;
            font-weight: 700;
            color: #1F2937;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .footer-value {
            font-size: 15px;
            color: #4B5563;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        /* CHỮ KÝ: Không dùng block, không dùng giãn chữ, ép kerning, loại bỏ letter-spacing */
        .footer-signature {
            font-family: 'Great Vibes', 'Alex Brush', 'Allura', 'Dancing Script', cursive;
            font-size: 38px;
            font-weight: 400;
            font-style: normal;

            letter-spacing: 0 !important;
            word-spacing: 0 !important;

            line-height: 1;

            white-space: nowrap;
            display: inline-block;

            transform: none;
            text-rendering: optimizeLegibility;

            font-kerning: normal;
            font-variant-ligatures: normal;
            font-feature-settings: "kern" 1, "liga" 1;

            text-align: center;
            margin-top: 5px;
            margin-bottom: 3px;
        }
        .signature-line {
            width: 140px;
            height: 1px;
            background: #D4A24C;
            margin: 0 auto;
        }
        .footer-small {
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 2px;
        }
        
        /* QR: 100x100 canh giữa, URL xuống tối đa 2 dòng */
        .qr-box {
            width: 100px;
            height: 100px;
            margin: 0 auto 5px auto;
            border: 1px solid #E5E7EB;
            padding: 5px;
            background: #fff;
            box-sizing: border-box;
        }
        .qr-url {
            font-size: 10px;
            color: #3B5BDB;
            word-break: break-all;
            max-width: 150px;
            margin: 0 auto;
            line-height: 1.2;
            max-height: 24px;
            overflow: hidden;
            display: block;
        }
    </style>
</head>
<body>
    <div class="double-border">
        <div class="double-border-inner">
            
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
            
            <div class="content-wrapper">
                <div class="cert-logo">OnlineFEA</div>
                <div class="cert-title">CERTIFICATE OF COMPLETION</div>
                
                <div class="cert-separator">――― ♦ ―――</div>
                
                <div class="cert-recipient-label">This certificate is proudly presented to</div>
                <div class="cert-recipient-name">{{ $user->name }}</div>
                
                <div class="cert-course-label">for successfully completing</div>
                <div class="cert-course-title">"{{ $course->title }}"</div>
            </div>

            <div style="position: absolute; bottom: 30px; left: 0; width: 100%;">
                <table class="cert-footer-table" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                    <!-- Cột 1: INSTRUCTOR -->
                    <td>
                        <div class="icon-wrapper">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAACbElEQVR4nO3ZPWgUURAH8FERRWyMiNhqYSMWphEEFZHLcTMrFkYbbQQ7W7EQDH6AIKQIYhEPuZ05EySFpViLikoIZGfulBDRtBIDYmM8uZNFjEU+3Ntd7z1l//Dqfb99b97HLkCRIkWcJBrD3cp0zgRvmuBdYxxRpitaCw51OrAOfI+GVFKmlybUWb3hM6uW+sDXqOAtZWqvjfjZVHBOhe5FYbAffIoJXkwCWKF9ixjPgw+JxnCbMn1OCekYU6spuM+1A6KwcjY1YgmDVR9qYywHyFvXDlChVzlAvs+MlDe5hTDNZIbEbWJwq1OICc3mAZkcpS2OIfgiKyJe9cB1TOhRZojglA+r1rUcRuSOawcoB5S5RkI85doBzTodyAqJQuz3oUae5FAjj32oEcuhRtS1A5RxPAfIA9cOaDAeT3oPWQXRNgmOgQ8xqZTTQ3AAfIoxvUsBmQXfooLDKVarYfAtGpb3KONiYghTa7p+Yi/4GGO88U+Pxq/E36yS7CvK9GFiYnAD+BxNcGNUxgb4HmN8/eephW/A9yhjI0F9zIHPmRzt32hCHxNMrS/va0c2g68xoaHk+wjeBt9i1VKfMta6PXPF38Sm7pd3uO4/ROPBTmO6mmQ6rYGZV6HrzfrArp4DGmHlcPw2VehrWsAKdbNogg+1jkf/6j+UztDQ+vhubYLTeXV+jeNLFNWD0/Ezc0U068HBngBkOSh+di4IY7wQH/B6jpAlTCvuQyaEMp3JcvvLr36o3RAcTP8DR+iTa4T9HpmFJp/c3jXEBC8777wsG5lL3UOYnnsIeZoGsuAdRHC+a0iRIkXgv8kPEklrE0Tx0H0AAAAASUVORK5CYII=" width="36" height="36">
                        </div>
                        <div class="footer-label">INSTRUCTOR</div>
                        @if(isset($course->instructor) && !empty($course->instructor->signature))
                            <div style="margin-top: 5px; margin-bottom: 5px;">
                                <img src="{{ asset('storage/' . $course->instructor->signature) }}" alt="Signature" style="height: 38px; display: block; margin: 0 auto;">
                            </div>
                        @else
                            <div class="footer-signature">{{ $course->instructor->name ?? 'Instructor Name' }}</div>
                        @endif
                        <div class="signature-line"></div>
                        <div class="footer-small">(Ký tên)</div>
                    </td>
                    
                    <!-- Cột 2: ISSUED ON & CERTIFICATE ID -->
                    <td>
                        <div class="icon-wrapper">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA+ElEQVR4nO3aaw6CMBAE4D2esTXqDe0eSZYLoHiNGiSg8rI+SLcyk8wPJWz7hRpjIlFghNcrcfYkbH1dU1Tvhd4fe36benCzyH0xSmR+m2b42Gvt89ODHA9mnzl76T/e53YX+nUpcH6115ztrgcJQWiCyA1jytFHmxJEho7e4iCirAQI6yoBwsohiLaIgmMi+Iw8JPaGAOkm9oYA6ebjG1/8mvv2mgDCgHhAYkBEWWnxEJo4BnNcE0AYEA9IDIgoKy0eQhPHYI5rAggD4gGJARFlJUBYVwkQ1lX6W0jmTBl7U/J+zz1I9XeIlDCZM2XuNtuBryCENOQK0ynKCKufY/QAAAAASUVORK5CYII=" width="36" height="36">
                        </div>
                        <div class="footer-label" style="margin-bottom: 5px;">ISSUED ON</div>
                        <div class="footer-value">{{ $certificate->issued_at->format('d F Y') }}</div>
                        
                        <div class="footer-label" style="margin-bottom: 5px;">CERTIFICATE ID</div>
                        <div class="footer-value" style="margin-bottom: 0;">{{ $certificate->certificate_code }}</div>
                    </td>
                    
                    <!-- Cột 3: CEO ONLINEFEA -->
                    <td>
                        <div class="icon-wrapper">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAACjUlEQVR4nO2XO28TQRDHFygQoqCg4iVogC9AnYqA4xkcmjQU6RAfAIkiTaR0KSgo04TbOR6JI6oknwCSCgHO7DkSQkiIIiCIEiKBgngcWt9dbNm5l+3Ie2j/0jR7p73/b3d2Zk8IKysrKyshRM0tnWXCBSbY0aEIn3mP4aIoIMSmIvRbQ4+9duCMKIqYcCEwD4vauA4lcakBI2FeFEUcpJLfuvo8O3quASdxWxRFSuK2Nq1TrNAgLGE+NL2kYcIzsxym25woitYfjl5QEj63H3Y9pp+Jomi1OnaMCT50gBC+e/ugdFQURUw4tQ9EWILhniiCuHGo4XsCyE7dvX7K+E7M0UFPCJYweyD+an3qxJ5THkqDCHflz5ocuZITYjPVXz86cbU6doQJXmUBCUvziu+LQ1nm5qz++tGJlYTbmSGaMLeygUA2fyzxW1wnZsKNtA+9rF49sW/fSAf5+IaGj6eD4Eamm0JiJ5bwvuZWLqes2P3cEM08n0qaW1G5pCR8iveHT/deDh7A1/iVg9+KgOpy5FL7h/QYS/jZLYiS8MN7VD7fsTgOXlMEqynV70tHMdIDemeiNIsFkihby1506HoJJqxG863RjWFdCJIBtEeYW39SOR27lf7k5GFFsJWS27+Y0FEO3ukVomWR7iqCF+nvwpb2mJSOzW3du6maF0y4nAmiAeLChLEgLkyIfnfoQYTnlIdyXsNx18C02tXeRB6lVY6BhMSVXBAhyLSBINP5QVyoDNw4tYULldwgdXnzJEv8O3DzFIT2oj2JbsQSPHNAwOsKIgDBGYN2ZKZ7EIJxY0AIxrsGsTJRyoC0UoS+BYk0aABlQdr0H4FAht/Pgw0meN4ziJWVlZUwSf8AP2BT+D3dqkYAAAAASUVORK5CYII=" width="36" height="36">
                        </div>
                        <div class="footer-label">CEO ONLINEFEA</div>
                        <div class="footer-signature">Hoàng Tuấn Tú</div>
                        <div class="signature-line"></div>
                        <div class="footer-small">(Ký tên)</div>
                    </td>
                    
                    <!-- Cột 4: QR VERIFY -->
                    <td>
                        <div class="footer-label" style="margin-bottom: 5px;">QR VERIFY</div>
                        <div class="qr-box">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('certificates.public', $certificate->certificate_code)) }}" alt="QR Code" width="88" height="88">
                        </div>
                        <div class="qr-url">
                            {{ route('certificates.public', $certificate->certificate_code) }}
                        </div>
                    </td>
                </tr>
            </table>
            </div>
        </div>
    </div>
</body>
</html>
@endif
