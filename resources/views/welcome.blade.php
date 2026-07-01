<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Fea LMS Platform hỗ trợ sinh viên học online, quản lý khóa học, theo dõi tiến độ, nộp bài tập và kết nối giảng viên.">
        <title>Fea LMS Platform</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
    </head>
    <body class="fea-welcome-page">
        <div class="landing-shell">
            <header class="landing-nav" aria-label="Điều hướng chính">
                <a href="{{ route('home') }}" class="brand-link" aria-label="Fea LMS Platform">
                    <span class="brand-mark">Fea</span>
                    <span class="brand-text">
                        <strong>Fea</strong>
                        <small>LMS Platform</small>
                    </span>
                </a>

                <nav class="nav-links" aria-label="Liên kết nhanh">
                    <a href="{{ route('home') }}#courses">Khóa học</a>
                    <a href="#features">Tính năng</a>
                    @guest
                        <a href="{{ route('login') }}">Đăng nhập</a>
                    @else
                        <a href="{{ auth()->user()->dashboardUrl() }}">Dashboard</a>
                    @endguest
                </nav>
            </header>

            <main>
                <section class="hero-section" aria-labelledby="welcome-title">
                    <div class="hero-content">
                        <span class="hero-badge">
                            <span class="badge-dot" aria-hidden="true"></span>
                            FEA LMS PLATFORM
                        </span>

                        <h1 id="welcome-title">Học tập trực tuyến thông minh cùng Fea</h1>

                        <p class="hero-description">
                            Nền tảng hỗ trợ sinh viên quản lý khóa học, theo dõi tiến độ,
                            nộp bài tập và kết nối với giảng viên một cách dễ dàng.
                        </p>

                        <div class="hero-actions">
                            <a href="{{ route('home') }}" class="btn-primary">Bắt đầu học ngay</a>
                            @guest
                                <a href="{{ route('login') }}" class="btn-secondary">Đăng nhập</a>
                            @else
                                <a href="{{ auth()->user()->dashboardUrl() }}" class="btn-secondary">Bảng điều khiển</a>
                            @endguest
                        </div>

                        <div class="hero-metrics" aria-label="Tổng quan nhanh">
                            <div>
                                <strong>24/7</strong>
                                <span>Truy cập bài học</span>
                            </div>
                            <div>
                                <strong>4</strong>
                                <span>Vai trò quản lý</span>
                            </div>
                            <div>
                                <strong>100%</strong>
                                <span>Theo dõi tiến độ</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-visual" aria-label="Mockup dashboard học tập">
                        <div class="dashboard-surface">
                            <div class="dashboard-header">
                                <div>
                                    <span class="eyebrow">Student workspace</span>
                                    <h2>Bảng học tập hôm nay</h2>
                                </div>
                                <span class="status-pill">Đang học</span>
                            </div>

                            <div class="dashboard-grid">
                                <article class="dashboard-card progress-card">
                                    <div class="card-heading">
                                        <span>Tiến độ học tập</span>
                                        <strong>72%</strong>
                                    </div>
                                    <div class="progress-track" aria-hidden="true">
                                        <span style="width: 72%"></span>
                                    </div>
                                    <p>Hoàn thành 18/25 bài học trong lộ trình Laravel cơ bản.</p>
                                </article>

                                <article class="dashboard-card course-card">
                                    <span class="card-kicker">Khóa học đang học</span>
                                    <h3>Laravel từ Zero đến Hero</h3>
                                    <p>Chương 5: Eloquent Relationships</p>
                                    <div class="mini-tags">
                                        <span>12 bài</span>
                                        <span>3 quiz</span>
                                    </div>
                                </article>

                                <article class="dashboard-card assignment-card">
                                    <span class="card-kicker">Bài tập sắp đến hạn</span>
                                    <h3>Xây dựng module đăng nhập</h3>
                                    <p>Nộp trước 22:00 hôm nay</p>
                                    <span class="deadline-chip">Còn 6 giờ</span>
                                </article>

                                <article class="dashboard-card schedule-card">
                                    <span class="card-kicker">Lịch học hôm nay</span>
                                    <div class="schedule-row">
                                        <span>08:30</span>
                                        <strong>Backend API</strong>
                                    </div>
                                    <div class="schedule-row">
                                        <span>14:00</span>
                                        <strong>Review đồ án</strong>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="features-section" aria-labelledby="features-title">
                    <div class="section-heading">
                        <span class="section-kicker">Hệ sinh thái học tập</span>
                        <h2 id="features-title">Mọi hoạt động học online được gom về một nơi</h2>
                    </div>

                    <div class="features-grid">
                        <article class="feature-card">
                            <span class="feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M5 5.5A2.5 2.5 0 0 1 7.5 3H20v16H7.5A2.5 2.5 0 0 0 5 21.5v-16Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 5.5A2.5 2.5 0 0 0 2.5 3H2v16h.5A2.5 2.5 0 0 1 5 21.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h3>Quản lý khóa học</h3>
                            <p>Theo dõi danh sách môn học, chương bài và tài liệu trong cùng một không gian rõ ràng.</p>
                        </article>

                        <article class="feature-card">
                            <span class="feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M4 19V5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M8 15l3-3 3 2 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h3>Theo dõi tiến độ</h3>
                            <p>Nắm được bài đã học, điểm quiz, tiến trình hoàn thành và các mốc cần ưu tiên.</p>
                        </article>

                        <article class="feature-card">
                            <span class="feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 15V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M8 8l4-4 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h3>Nộp bài tập online</h3>
                            <p>Gửi bài, kiểm tra hạn nộp và nhận phản hồi từ giảng viên ngay trong hệ thống.</p>
                        </article>

                        <article class="feature-card">
                            <span class="feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M6 10.5A4.5 4.5 0 0 1 10.5 6H17a4 4 0 0 1 0 8h-4l-4 3v-3H6.5A3.5 3.5 0 0 1 3 10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 14.5A4.5 4.5 0 0 0 18.5 19H20l-2.5-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h3>Kết nối giảng viên</h3>
                            <p>Trao đổi lớp học, nhận thông báo và phối hợp xử lý bài tập nhanh hơn.</p>
                        </article>
                    </div>
                </section>
            </main>

            <footer class="landing-footer">
                <span>&copy; {{ date('Y') }} Fea LMS Platform.</span>
                <span>Nền tảng học online cho sinh viên và giảng viên.</span>
            </footer>
        </div>
    </body>
</html>
