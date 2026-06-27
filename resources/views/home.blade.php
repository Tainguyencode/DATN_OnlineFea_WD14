@extends('layouts.app')

@section('title', 'Fea - Nền Tảng Học Tập Thông Minh')

@section('content')
<!-- Hero Section -->
<section class="relative bg-slate-950 text-white overflow-hidden py-24 lg:py-32">
    <!-- Glowing background blobs -->
    <div class="absolute -top-20 -left-20 w-80 h-80 bg-indigo-600/30 rounded-full glow-blob animate-blob-1"></div>
    <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-purple-600/30 rounded-full glow-blob animate-blob-2"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Hero Text -->
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-1.5 bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-semibold px-4 py-1.5 rounded-full mb-6 uppercase tracking-wider">
                    ⚡ Hệ thống quản lý học tập & đồ án tốt nghiệp
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-6">
                    {{ $banner['title'] ?? 'Học mọi lúc, mọi nơi' }}
                </h1>
                <p class="text-lg sm:text-xl text-slate-300 mb-8 max-w-2xl font-light leading-relaxed">
                    {{ $banner['subtitle'] ?? 'Nền tảng e-learning thông minh kết hợp quản lý dự án, theo dõi tiến trình và tích hợp trợ lý ảo AI hỗ trợ giảng dạy.' }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#courses" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-7 py-3.5 rounded-xl transition shadow-lg shadow-indigo-600/35 dark:shadow-none hover:scale-102">
                        Bắt đầu học ngay
                    </a>
                    <a href="#paths" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold px-7 py-3.5 rounded-xl transition hover:scale-102">
                        Xem lộ trình học
                    </a>
                </div>
            </div>
            
            <!-- Workspace Mockup -->
            <div class="lg:col-span-5 relative hidden lg:block">
                <div class="relative bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 overflow-hidden">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 bg-rose-500 rounded-full"></span>
                            <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                            <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        </div>
                        <span class="text-xs text-slate-500 font-semibold tracking-wider uppercase">Sinh viên: Trần Thị Học</span>
                    </div>
                    <!-- Mockup content -->
                    <div class="space-y-4">
                        <div class="p-3.5 bg-slate-950 border border-slate-800/80 rounded-xl">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-indigo-400">Đồ Án Tốt Nghiệp: Fea LMS</span>
                                <span class="text-xs text-emerald-400 font-bold">85% hoàn thành</span>
                            </div>
                            <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-full rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="p-3 bg-slate-950 border border-slate-800/80 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-indigo-500/10 text-indigo-400 rounded-lg flex items-center justify-center text-xs">01</span>
                                <span class="text-xs font-semibold text-slate-300">Nghiên cứu Schema cơ sở dữ liệu</span>
                            </div>
                            <span class="text-[10px] bg-emerald-950 text-emerald-400 border border-emerald-900 px-2 py-0.5 rounded font-bold uppercase">Đạt</span>
                        </div>
                        <div class="p-3 bg-slate-950 border border-slate-800/80 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-indigo-500/10 text-indigo-400 rounded-lg flex items-center justify-center text-xs">02</span>
                                <span class="text-xs font-semibold text-slate-300">Tách nhỏ file Migrations & Seeders</span>
                            </div>
                            <span class="text-[10px] bg-emerald-950 text-emerald-400 border border-emerald-900 px-2 py-0.5 rounded font-bold uppercase">Đạt</span>
                        </div>
                        <div class="p-3 bg-slate-950 border border-slate-800/80 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-indigo-500/10 text-indigo-400 rounded-lg flex items-center justify-center text-xs">03</span>
                                <span class="text-xs font-semibold text-slate-300">Tích hợp AI Trợ Lý & Chatbot Drawer</span>
                            </div>
                            <span class="text-[10px] bg-indigo-950 text-indigo-400 border border-indigo-900 px-2 py-0.5 rounded font-bold uppercase">Đang làm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Banner Section -->
<section class="bg-white dark:bg-[#161615] border-b border-slate-200/80 dark:border-slate-800/80 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/40">
                <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $stats['courses'] }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">Khóa học đăng tải</div>
            </div>
            <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/40">
                <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $stats['students'] }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">Học viên tham gia</div>
            </div>
            <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/40">
                <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $stats['instructors'] }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">Giảng viên hướng dẫn</div>
            </div>
            <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/40">
                <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $categories->count() }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">Danh mục đa dạng</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
@if($featuredCourses->isNotEmpty())
<section class="py-20 bg-slate-50 dark:bg-[#0a0a0a] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">Khóa học nổi bật</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Tuyển chọn các khóa học được đánh giá cao nhất trên Fea.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredCourses as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Categories Grid Section -->
<section id="categories" class="py-20 bg-white dark:bg-[#161615] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">Danh mục môn học</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-3 max-w-xl mx-auto">Khám phá các môn học đa dạng giúp bạn phát triển các kỹ năng chuyên môn từ cơ bản đến nâng cao.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($categories as $category)
                @php
                    $colors = [
                        'bg-indigo-500/10 border-indigo-200 dark:border-indigo-900/40 text-indigo-600 dark:text-indigo-400',
                        'bg-emerald-500/10 border-emerald-200 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400',
                        'bg-orange-500/10 border-orange-200 dark:border-orange-900/40 text-orange-600 dark:text-orange-400',
                        'bg-cyan-500/10 border-cyan-200 dark:border-cyan-900/40 text-cyan-600 dark:text-cyan-400'
                    ];
                    $color = $colors[$category->id % count($colors)];
                @endphp
                <a href="{{ route('home', ['category' => $category->id]) }}#courses"
                   class="group p-6 rounded-2xl border transition-all duration-300 hover:-translate-y-1 {{ $color }}">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 bg-white dark:bg-slate-900 shadow-sm border border-slate-100 dark:border-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100 group-hover:underline">{{ $category->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">{{ $category->description ?? 'Khóa học chất lượng do đội ngũ giảng viên biên soạn.' }}</p>
                    <div class="text-xs font-bold mt-4 flex items-center gap-1">
                        Xem khóa học
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- All Courses Section with Search & Filtering -->
<section id="courses" class="py-20 bg-slate-50 dark:bg-[#0a0a0a] transition-colors duration-300 border-t border-slate-200/50 dark:border-slate-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">Tất cả khóa học</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-3">Tìm kiếm, lọc và phân loại các khóa học phù hợp nhất với trình độ của bạn.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('home') }}#courses" class="bg-white dark:bg-[#161615] rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 mb-10 shadow-sm transition-colors duration-300">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="lg:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tìm tên khóa học, giảng viên..."
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 outline-none text-sm text-slate-900 dark:text-white transition">
                </div>
                <select name="category" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 outline-none text-sm text-slate-900 dark:text-white transition cursor-pointer">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="level" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 outline-none text-sm text-slate-900 dark:text-white transition cursor-pointer">
                    <option value="">Tất cả trình độ</option>
                    <option value="beginner" @selected(request('level') == 'beginner')>Cơ bản</option>
                    <option value="intermediate" @selected(request('level') == 'intermediate')>Trung cấp</option>
                    <option value="advanced" @selected(request('level') == 'advanced')>Nâng cao</option>
                </select>
                <select name="sort" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 outline-none text-sm text-slate-900 dark:text-white transition cursor-pointer">
                    <option value="newest" @selected(request('sort', 'newest') == 'newest')>Mới nhất</option>
                    <option value="rating" @selected(request('sort') == 'rating')>Đánh giá cao</option>
                    <option value="popular" @selected(request('sort') == 'popular')>Phổ biến</option>
                    <option value="price_asc" @selected(request('sort') == 'price_asc')>Giá từ thấp → cao</option>
                    <option value="price_desc" @selected(request('sort') == 'price_desc')>Giá từ cao → thấp</option>
                </select>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow-md shadow-indigo-600/10">
                    Áp dụng bộ lọc
                </button>
                <a href="{{ route('home') }}#courses" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-950 transition">
                    Xóa lọc
                </a>
            </div>
        </form>

        <!-- Course Cards -->
        @if($courses->isEmpty())
            <div class="text-center py-20 text-slate-500 dark:text-slate-400">
                <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-700 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-lg font-bold">Không tìm thấy khóa học nào</p>
                <p class="text-sm mt-1">Hãy thử sử dụng từ khóa hoặc thay đổi các tiêu chí bộ lọc khác.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
            <div class="mt-12">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>

<!-- Learning Paths Section -->
@if($learningPaths->isNotEmpty())
<section id="paths" class="py-20 bg-white dark:bg-[#161615] transition-colors duration-300 border-t border-slate-200/50 dark:border-slate-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">Lộ trình học tập chuyên biệt</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-3">Học theo tuần tự có lộ trình rõ ràng giúp định hướng công việc và rút ngắn tiến độ nghiên cứu tốt nghiệp.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($learningPaths as $path)
                <div class="p-8 bg-gradient-to-br from-indigo-50/70 to-purple-50/70 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-2xl border border-indigo-100/60 dark:border-indigo-900/40 relative overflow-hidden group">
                    <div class="absolute -top-12 -right-12 w-24 h-24 bg-indigo-500/10 rounded-full group-hover:scale-125 transition-transform duration-500"></div>
                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-950 px-3 py-1.5 rounded-full uppercase tracking-wider">{{ is_array($path->course_ids) ? count($path->course_ids) : 0 }} khóa học</span>
                    <h3 class="text-xl font-bold text-slate-950 dark:text-white mt-4 mb-3 leading-snug">{{ $path->title }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $path->description ?? 'Lộ trình được khuyên dùng để có lượng kiến thức toàn diện cho sinh viên khoa Công nghệ thông tin.' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Gamification (Badges) & Leaderboard Section -->
<section class="py-20 bg-slate-50 dark:bg-[#0a0a0a] transition-colors duration-300 border-t border-slate-200/50 dark:border-slate-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Gamification Badges -->
            <div class="lg:col-span-7">
                <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight mb-3">Hệ thống vinh danh & Huy chương</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Fea vinh danh những nỗ lực học tập của bạn thông qua việc tích điểm tích lũy và mở khóa huy chương học thuật danh giá.</p>
                
                <div class="space-y-4">
                    @foreach($badges as $badge)
                        <div class="p-5 bg-white dark:bg-[#161615] border border-slate-200/60 dark:border-slate-800/80 rounded-2xl flex items-center gap-5 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition duration-300">
                            <div class="w-14 h-14 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-2xl shadow-sm shrink-0">
                                🏅
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-950 dark:text-white flex items-center gap-2">
                                    {{ $badge->name }}
                                    <span class="text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $badge->points_required }} pts</span>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $badge->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Mockup Leaderboard -->
            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-[#161615] border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-950 dark:text-white mb-6 flex items-center gap-2">
                        🏆 Bảng xếp hạng học thuật
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3.5 bg-indigo-500/5 dark:bg-indigo-500/10 rounded-xl border border-indigo-100/50 dark:border-indigo-900/30">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-amber-400 text-amber-950 text-xs font-extrabold flex items-center justify-center">1</span>
                                <span class="text-sm font-bold text-slate-950 dark:text-white">Nguyễn Hoàng Nam</span>
                            </div>
                            <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">450 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-100 dark:border-slate-800/50">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-300 text-slate-700 text-xs font-extrabold flex items-center justify-center">2</span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-200">Trần Thị Lan</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">380 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-100 dark:border-slate-800/50">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-orange-300 text-orange-950 text-xs font-extrabold flex items-center justify-center">3</span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-200">Vũ Hoàng Long</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">320 pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
@if($faqs->isNotEmpty())
<section id="faq" class="py-20 bg-white dark:bg-[#161615] transition-colors duration-300 border-t border-slate-200/50 dark:border-slate-800/40">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">Câu hỏi thường gặp</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Giải đáp nhanh các thắc mắc về lớp học trực tuyến và nộp đồ án tốt nghiệp.</p>
        </div>
        <div class="space-y-4">
            @foreach($faqs as $faq)
                <details class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 group">
                    <summary class="px-6 py-4.5 cursor-pointer font-bold text-slate-900 dark:text-slate-100 flex items-center justify-between list-none select-none">
                        {{ $faq->question }}
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="px-6 pb-5 text-slate-600 dark:text-slate-300 text-sm leading-relaxed border-t border-slate-200/30 dark:border-slate-800/30 pt-4">
                        {{ $faq->answer }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action Section -->
@guest
<section class="py-20 bg-indigo-600 text-white relative overflow-hidden">
    <div class="absolute -top-12 -left-12 w-64 h-64 bg-indigo-500 rounded-full glow-blob"></div>
    <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-purple-500 rounded-full glow-blob"></div>
    
    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 tracking-tight">Sẵn sàng nâng tầm kiến thức của bạn?</h2>
        <p class="text-indigo-100 mb-8 max-w-xl mx-auto font-light">Đăng ký tài khoản sinh viên miễn phí trên Fea ngay hôm nay để trải nghiệm môi trường học tập và làm đồ án tốt nghiệp hiện đại nhất.</p>
        <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-700 font-bold px-8 py-3.5 rounded-xl hover:bg-indigo-50 transition shadow-lg hover:scale-102">
            Đăng ký tài khoản ngay
        </a>
    </div>
</section>
@endguest
@endsection
