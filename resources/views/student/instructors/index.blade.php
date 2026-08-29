@extends('layouts.app')

@section('title', 'Giảng viên | OnlineFEA')

@section('content')
@php
    $sortLabels = [
        'newest'   => 'Mới nhất',
        'courses'  => 'Nhiều khóa học nhất',
        'rating'   => 'Đánh giá cao nhất',
        'students' => 'Nhiều học viên nhất',
        'name'     => 'Tên A-Z',
    ];
    
    if (!function_exists('format_k_number')) {
        function format_k_number($num) {
            if ($num >= 1000) {
                return round($num / 1000, 1) . 'k';
            }
            return $num;
        }
    }
@endphp

<style>
    /* CSS Grid Responsive */
    .instructor-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    @media (min-width: 1536px) {
        .instructor-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }
    @media (max-width: 1280px) {
        .instructor-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Card Hover */
    .instructor-card {
        background: #FFFFFF;
        border-radius: 20px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        position: relative;
        overflow: hidden;
    }
    .instructor-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #BFDBFE;
    }
    .instructor-avatar-wrap {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 16px;
        border: 2px solid #EFF6FF;
    }
    .instructor-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s cubic-bezier(.4,0,.2,1);
    }
    .instructor-card:hover .instructor-avatar {
        transform: scale(1.1);
    }
    
    /* Custom Scrollbar for dropdown */
    .filter-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 16px;
    }
</style>

{{-- HERO --}}
<section style="background: #F0F7FF; padding: 40px 0 60px; position: relative; overflow: hidden; border-bottom: 1px solid #E5E7EB;">
    {{-- Decorative Background on the right (Wave/Blob) --}}
    <div style="position: absolute; right: -5%; top: -20%; bottom: -20%; width: 55%; background: #E0ECFF; border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; z-index: 0; transform: rotate(-5deg);"></div>
    <div style="position: absolute; right: -10%; bottom: -40%; width: 40%; height: 60%; background: #D1E0FF; border-radius: 50%; z-index: 0; opacity: 0.5;"></div>
    
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 32px 16px; position: relative; z-index: 1;">
        <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('home') }}'; }" class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-[#0056D2] hover:text-[#0046B8] cursor-pointer transition py-1">
            ← Quay lại
        </button>
    </div>

    <div style="max-width: 1400px; margin: 0 auto; padding: 0 32px; position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
        
        {{-- Left: Text & Stats --}}
        <div style="flex: 1; min-width: 320px; max-width: 580px;">
            <h1 style="font-size: 38px; font-weight: 800; color: #111827; margin: 0 0 16px; letter-spacing: -0.02em; line-height: 1.2;">
                Khám phá đội ngũ giảng viên
            </h1>
            <p style="font-size: 16px; color: #6B7280; margin: 0 0 32px; line-height: 1.6;">
                Lựa chọn giảng viên yêu thích và khám phá các khóa học chất lượng được biên soạn bởi các chuyên gia hàng đầu.
            </p>
            
            <div style="display: flex; gap: 48px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: #2563EB; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(37,99,235,0.25);">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 800; color: #111827; line-height: 1.1;">{{ number_format($instructors->total()) }}</div>
                        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Giảng viên</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: #2563EB; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(37,99,235,0.25);">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 800; color: #111827; line-height: 1.1;">{{ $specialties->count() }}</div>
                        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Chuyên môn</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Floating Cards Illustration --}}
        <div style="flex: 1; min-width: 320px; position: relative; height: 320px; display: flex; align-items: center; justify-content: center;">
            {{-- Dot pattern decorative --}}
            <div style="position: absolute; right: 0; top: 0; opacity: 0.5;">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="4" cy="4" r="2" fill="#93C5FD"/><circle cx="20" cy="4" r="2" fill="#93C5FD"/><circle cx="36" cy="4" r="2" fill="#93C5FD"/><circle cx="52" cy="4" r="2" fill="#93C5FD"/><circle cx="68" cy="4" r="2" fill="#93C5FD"/>
                    <circle cx="4" cy="20" r="2" fill="#93C5FD"/><circle cx="20" cy="20" r="2" fill="#93C5FD"/><circle cx="36" cy="20" r="2" fill="#93C5FD"/><circle cx="52" cy="20" r="2" fill="#93C5FD"/><circle cx="68" cy="20" r="2" fill="#93C5FD"/>
                    <circle cx="4" cy="36" r="2" fill="#93C5FD"/><circle cx="20" cy="36" r="2" fill="#93C5FD"/><circle cx="36" cy="36" r="2" fill="#93C5FD"/><circle cx="52" cy="36" r="2" fill="#93C5FD"/><circle cx="68" cy="36" r="2" fill="#93C5FD"/>
                    <circle cx="4" cy="52" r="2" fill="#93C5FD"/><circle cx="20" cy="52" r="2" fill="#93C5FD"/><circle cx="36" cy="52" r="2" fill="#93C5FD"/><circle cx="52" cy="52" r="2" fill="#93C5FD"/><circle cx="68" cy="52" r="2" fill="#93C5FD"/>
                </svg>
            </div>
            
            {{-- Floating Card 1 (Center Left) --}}
            <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); background: #fff; border-radius: 16px; padding: 16px 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 20px 40px rgba(37,99,235,0.08); z-index: 3; border: 1px solid #fff;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <div>
                    <div style="font-size: 20px; font-weight: 800; color: #111827;">4.8/5</div>
                    <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Đánh giá trung bình</div>
                </div>
            </div>

            {{-- Floating Card 2 (Top Right) --}}
            <div style="position: absolute; right: 10%; top: 10%; background: #fff; border-radius: 16px; padding: 12px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 12px 30px rgba(37,99,235,0.06); z-index: 2; border: 1px solid #fff;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #DBEAFE; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l9-5-9-5-9 5 9 5z"/></svg>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 800; color: #111827;">Chuyên gia</div>
                    <div style="font-size: 13px; color: #6B7280; font-weight: 500;">Hàng đầu ngành</div>
                </div>
            </div>

            {{-- Floating Card 3 (Bottom Right) --}}
            <div style="position: absolute; right: 5%; bottom: 12%; background: #fff; border-radius: 16px; padding: 12px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 12px 30px rgba(37,99,235,0.06); z-index: 4; border: 1px solid #fff;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 800; color: #111827;">Chất lượng</div>
                    <div style="font-size: 13px; color: #6B7280; font-weight: 500;">Được kiểm duyệt</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FILTER BAR --}}
<section style="background: #F8FAFC; position: sticky; top: 70px; z-index: 40; padding: 20px 0;" id="filter-bar">
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 32px;">
        <form method="GET" action="{{ route('instructors.index') }}#filter-bar" id="filter-form" style="background: #fff; border-radius: 18px; padding: 12px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px; border: 1px solid #E5E7EB;">
            
            {{-- Search --}}
            <div style="position: relative; flex: 1; max-width: 320px;">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo tên giảng viên..." style="width: 100%; height: 46px; border: none; background: transparent; padding-left: 40px; font-size: 14px; outline: none; color: #1F2937;">
            </div>
            
            <div style="width: 1px; height: 28px; background: #E5E7EB;"></div>
            
            {{-- Specialty --}}
            <select name="specialty" class="filter-select" onchange="this.form.submit()" style="height: 46px; border: none; background-color: transparent; font-size: 14px; color: #4B5563; outline: none; min-width: 180px; cursor: pointer; padding-right: 28px;">
                <option value="">Tất cả chuyên môn</option>
                @foreach($specialties as $parent)
                    @if($parent->children->isNotEmpty())
                        <optgroup label="{{ $parent->name }}">
                            @foreach($parent->children as $child)
                                <option value="{{ $child->id }}" {{ (string)$specialty === (string)$child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $parent->id }}" {{ (string)$specialty === (string)$parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endif
                @endforeach
            </select>
            
            <div style="width: 1px; height: 28px; background: #E5E7EB;"></div>
            
            {{-- Sort --}}
            <select name="sort" class="filter-select" onchange="this.form.submit()" style="height: 46px; border: none; background-color: transparent; font-size: 14px; color: #4B5563; outline: none; min-width: 160px; cursor: pointer; padding-right: 28px;">
                @foreach($sortLabels as $value => $label)
                    <option value="{{ $value }}" {{ $sort === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            
            <a href="{{ route('instructors.index') }}#filter-bar" style="height: 44px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 12px; font-weight: 600; font-size: 14px; color: #4B5563; background: #F3F4F6; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='#E5E7EB';" onmouseout="this.style.background='#F3F4F6';">
                <svg style="width: 16px; height: 16px; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Làm mới
            </a>
            
            <button type="submit" style="background: #2563EB; color: #fff; height: 44px; padding: 0 24px; border-radius: 12px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#1D4ED8';" onmouseout="this.style.background='#2563EB';">
                Tìm kiếm
            </button>
            
            <div style="margin-left: auto; font-size: 14px; color: #6B7280; font-weight: 500;">
                <strong style="color: #111827;">{{ $instructors->total() }}</strong> giảng viên
            </div>
        </form>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section style="background: #F8FAFC; padding: 20px 0 80px;">
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 32px;">
        @if($instructors->isEmpty())
            <div style="text-align: center; padding: 80px 0;">
                <h3 style="font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px;">Không tìm thấy giảng viên</h3>
                <p style="font-size: 14px; color: #6B7280; margin-bottom: 16px;">Vui lòng thử lại với từ khóa hoặc chuyên môn khác.</p>
                <a href="{{ route('instructors.index') }}" style="display: inline-block; color: #2563EB; font-size: 14px; font-weight: 600; text-decoration: underline;">Xóa bộ lọc</a>
            </div>
        @else
            <div class="instructor-grid">
                @foreach($instructors as $instructor)
                @php
                    $isTop = $instructor->courses_count >= 5;
                    $rating = $instructor->average_rating;
                @endphp
                <div class="instructor-card">
                    <div class="instructor-avatar-wrap">
                        <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="instructor-avatar">
                    </div>
                    
                    <div style="text-align: center; margin-bottom: 8px;">
                        <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 4px; line-height: 1.3;">{{ $instructor->name }}</h2>
                        <p style="font-size: 13px; color: #6B7280; margin: 0;">
                            {{ $instructor->courses->first()?->category->name ?? 'Giảng viên' }}
                        </p>
                    </div>
                    
                    {{-- Rating --}}
                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px; margin-bottom: 16px;">
                        @if($rating > 0)
                            <div style="display: flex; gap: 2px; color: #F59E0B;">
                                @for($i=1; $i<=5; $i++)
                                    <svg style="width:14px;height:14px;fill:{{ $i <= round($rating) ? 'currentColor' : '#E5E7EB' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span style="font-size: 13px; font-weight: 700; color: #374151; margin-left: 2px;">{{ number_format($rating, 1) }}</span>
                            <span style="font-size: 12px; color: #9CA3AF;">({{ $instructor->total_rating_count }})</span>
                        @else
                            <span style="font-size: 13px; color: #9CA3AF;">Chưa có đánh giá</span>
                        @endif
                    </div>
                    
                    {{-- 3 Stats --}}
                    <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; font-size: 12px; color: #4B5563; font-weight: 500;">
                        <span style="display: flex; align-items: center; gap: 4px;">
                            👨‍🎓 {{ format_k_number($instructor->students_count) }} học viên
                        </span>
                        <span style="color: #D1D5DB;">|</span>
                        <span style="display: flex; align-items: center; gap: 4px;">
                            📚 {{ $instructor->courses_count }} khóa học
                        </span>
                        @if($isTop)
                            <span style="color: #D1D5DB;">|</span>
                            <span style="display: flex; align-items: center; gap: 4px; color: #B45309;">
                                ⭐ Top Instructor
                            </span>
                        @endif
                    </div>
                    
                    {{-- Bio --}}
                    <p style="font-size: 13px; color: #6B7280; line-height: 1.5; margin: 0 0 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-align: center;">
                        {{ $instructor->bio ?: 'Giảng viên chuyên nghiệp tại OnlineFEA.' }}
                    </p>
                    
                    {{-- Button --}}
                    <div style="margin-top: auto;">
                        <a href="{{ route('instructors.show', $instructor) }}" style="display: block; width: 100%; text-align: center; border: 1.5px solid #2563EB; color: #2563EB; font-weight: 600; font-size: 14px; padding: 9px 0; border-radius: 12px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#2563EB'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#2563EB';">
                            Xem hồ sơ
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- ── Pagination ── --}}
            @if($instructors->hasPages())
                <div style="margin-top:48px;display:flex;justify-content:center;align-items:center;gap:6px;">
                    {{-- Previous --}}
                    @if($instructors->onFirstPage())
                        <span style="padding:8px 14px;border-radius:8px;border:1px solid #E5E7EB;color:#9CA3AF;font-size:14px;font-weight:500;cursor:default;">‹</span>
                    @else
                        <a href="{{ $instructors->previousPageUrl() }}#filter-bar"
                           style="padding:8px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#fff;color:#374151;font-size:14px;font-weight:500;text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.borderColor='#2563EB';this.style.color='#2563EB';" onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151';"
                        >‹</a>
                    @endif

                    {{-- Pages --}}
                    @foreach($instructors->getUrlRange(max(1, $instructors->currentPage()-2), min($instructors->lastPage(), $instructors->currentPage()+2)) as $page => $url)
                        @if($page == $instructors->currentPage())
                            <span style="padding:8px 14px;border-radius:8px;border:1px solid #2563EB;background:#2563EB;color:#fff;font-size:14px;font-weight:600;min-width:40px;text-align:center;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}#filter-bar"
                               style="padding:8px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#fff;color:#374151;font-size:14px;font-weight:500;text-decoration:none;min-width:40px;text-align:center;transition:all .2s;"
                               onmouseover="this.style.borderColor='#2563EB';this.style.color='#2563EB';" onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151';"
                            >{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($instructors->hasMorePages())
                        <a href="{{ $instructors->nextPageUrl() }}#filter-bar"
                           style="padding:8px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#fff;color:#374151;font-size:14px;font-weight:500;text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.borderColor='#2563EB';this.style.color='#2563EB';" onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151';"
                        >›</a>
                    @else
                        <span style="padding:8px 14px;border-radius:8px;border:1px solid #E5E7EB;color:#9CA3AF;font-size:14px;font-weight:500;cursor:default;">›</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
</section>
@endsection
