@extends('layouts.app')

@section('title', $user->name . ' | Giảng viên | OnlineFEA')

@push('head')
<meta name="description" content="Hồ sơ giảng viên {{ $user->name }} tại OnlineFEA – {{ $totalCourses }} khóa học, {{ number_format($totalStudents) }} học viên, đánh giá {{ $avgRating }}/5.">
@endpush

@section('content')
@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $gradients   = [
        'from-indigo-500 to-purple-600',
        'from-emerald-500 to-teal-600',
        'from-orange-500 to-red-500',
        'from-blue-500 to-cyan-500',
        'from-pink-500 to-rose-500',
    ];
@endphp

{{-- ══════════════════════════════════════════════════════════════
     BREADCRUMB
══════════════════════════════════════════════════════════════ --}}
<div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
    <div style="max-width:1280px;margin:0 auto;padding:14px 32px;">
        <nav style="display:flex;align-items:center;gap:6px;font-size:13px;color:#64748b;">
            <a href="{{ route('home') }}" style="color:#64748b;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#2563eb';" onmouseout="this.style.color='#64748b';">Trang chủ</a>
            <svg style="width:14px;height:14px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
            <a href="{{ route('instructors.index') }}" style="color:#64748b;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#2563eb';" onmouseout="this.style.color='#64748b';">Giảng viên</a>
            <svg style="width:14px;height:14px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
            <span style="color:#1e293b;font-weight:500;">{{ $user->name }}</span>
        </nav>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PROFILE HERO
══════════════════════════════════════════════════════════════ --}}
<section style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#0f172a 100%);position:relative;overflow:hidden;">
    {{-- Decorative blobs --}}
    <div style="position:absolute;top:-100px;right:-60px;width:400px;height:400px;border-radius:50%;background:rgba(37,99,235,0.12);filter:blur(70px);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-80px;left:-40px;width:350px;height:350px;border-radius:50%;background:rgba(99,102,241,0.1);filter:blur(60px);pointer-events:none;"></div>

    <div style="max-width:1280px;margin:0 auto;padding:56px 32px;position:relative;z-index:1;">
        <div style="display:flex;align-items:flex-start;gap:48px;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;">
                <div style="width:140px;height:140px;border-radius:50%;overflow:hidden;border:4px solid rgba(255,255,255,0.15);box-shadow:0 20px 50px rgba(0,0,0,0.5);">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>

            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;flex-wrap:wrap;">
                    <h1 style="font-size:34px;font-weight:800;color:#fff;margin:0;letter-spacing:-0.01em;">{{ $user->name }}</h1>
                    @if($user->email_verified_at)
                        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(37,99,235,.25);border:1px solid rgba(96,165,250,.4);color:#93c5fd;font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px;">
                            <svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Đã xác minh
                        </span>
                    @endif
                    @if($totalCourses >= 5)
                        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.4);color:#fde68a;font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px;">
                            ⭐ Top Instructor
                        </span>
                    @endif
                </div>

                {{-- Specialty / Role --}}
                <p style="font-size:15px;color:#94a3b8;margin:0 0 20px;">
                    @if($courses->count() > 0 && $courses->first()->category)
                        Chuyên gia {{ $courses->first()->category->name }}
                        @if($courses->first()->category->parent)
                            – {{ $courses->first()->category->parent->name }}
                        @endif
                    @else
                        Giảng viên tại OnlineFEA
                    @endif
                </p>

                {{-- Stats row --}}
                <div style="display:flex;gap:40px;flex-wrap:wrap;margin-bottom:28px;">
                    <div>
                        <div style="font-size:28px;font-weight:800;color:#fff;">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</div>
                        <div style="display:flex;align-items:center;gap:4px;margin-top:2px;">
                            @for($i = 1; $i <= 5; $i++)
                                <svg style="width:14px;height:14px;{{ $i <= round($avgRating) ? 'fill:#f59e0b;color:#f59e0b;' : 'fill:#334155;color:#334155;' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span style="font-size:12px;color:#64748b;margin-left:4px;">({{ number_format($totalReviews) }} đánh giá)</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">Đánh giá TB</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                    <div>
                        <div style="font-size:28px;font-weight:800;color:#fff;">{{ number_format($totalStudents) }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;">Học viên</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                    <div>
                        <div style="font-size:28px;font-weight:800;color:#fff;">{{ $totalCourses }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;">Khóa học</div>
                    </div>
                    @if($user->created_at)
                        <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#fff;">{{ $user->created_at->format('Y') }}</div>
                            <div style="font-size:12px;color:#64748b;margin-top:4px;">Năm tham gia</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     BODY: BIO + COURSES
══════════════════════════════════════════════════════════════ --}}
<div style="background:#f8fafc;min-height:400px;">
    <div style="max-width:1280px;margin:0 auto;padding:48px 32px;display:grid;grid-template-columns:320px 1fr;gap:40px;align-items:start;">

        {{-- ── LEFT: Sidebar ── --}}
        <aside>
            {{-- About --}}
            <div style="background:#fff;border-radius:20px;border:1.5px solid #e2e8f0;overflow:hidden;margin-bottom:24px;">
                <div style="padding:24px;border-bottom:1px solid #f1f5f9;">
                    <h2 style="font-size:17px;font-weight:700;color:#1e293b;margin:0;">Giới thiệu</h2>
                </div>
                <div style="padding:24px;">
                    @if($user->instructorProfile?->bio || $user->bio)
                        <p style="font-size:14px;color:#475569;line-height:1.75;margin:0;white-space:pre-line;">{{ $user->instructorProfile?->bio ?: $user->bio }}</p>
                    @else
                        <p style="font-size:14px;color:#94a3b8;font-style:italic;">Giảng viên chưa cập nhật thông tin giới thiệu.</p>
                    @endif
                </div>
            </div>

            {{-- Verified Certificates & Degrees (STU-FE-16, STU-BE-10) --}}
            @if(isset($approvedCertificates) && $approvedCertificates->isNotEmpty())
            <div style="background:#fff;border-radius:20px;border:1.5px solid #e2e8f0;overflow:hidden;margin-bottom:24px;">
                <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <h2 style="font-size:16px;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px;">
                        <span>🏅</span>
                        <span>Chứng chỉ đã xác minh</span>
                    </h2>
                    <span style="font-size:12px;font-weight:700;background:#ecfdf5;color:#047857;padding:2px 8px;border-radius:999px;border:1px solid #a7f3d0;">
                        {{ $approvedCertificates->count() }}
                    </span>
                </div>
                <div style="padding:16px 24px;display:flex;flex-direction:column;gap:14px;">
                    @foreach($approvedCertificates as $cert)
                    <div style="padding-bottom:12px;border-bottom:1px solid #f8fafc;last:border-0;last:pb-0;">
                        <div style="display:flex;align-items:flex-start;gap:10px;">
                            <div style="width:28px;height:28px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:14px;font-weight:700;color:#1e293b;line-height:1.3;" class="line-clamp-2">{{ $cert->name }}</div>
                                @if($cert->institution)
                                    <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $cert->institution }}</div>
                                @endif
                                <div style="display:flex;align-items:center;gap:6px;margin-top:4px;flex-wrap:wrap;">
                                    <span style="font-size:10px;font-weight:700;text-transform:uppercase;background:#f1f5f9;color:#475569;padding:1px 6px;border-radius:4px;">
                                        {{ $cert->document_type === 'degree' ? 'Bằng cấp' : ($cert->document_type === 'portfolio' ? 'Hồ sơ năng lực' : 'Chứng chỉ') }}
                                    </span>
                                    @if($cert->issued_at)
                                        <span style="font-size:11px;color:#94a3b8;">
                                            {{ \Carbon\Carbon::parse($cert->issued_at)->format('m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quick Stats --}}
            <div style="background:#fff;border-radius:20px;border:1.5px solid #e2e8f0;overflow:hidden;margin-bottom:24px;">
                <div style="padding:24px;border-bottom:1px solid #f1f5f9;">
                    <h2 style="font-size:17px;font-weight:700;color:#1e293b;margin:0;">Thống kê</h2>
                </div>
                <div style="padding:16px 24px;">
                    @php
                        $statItems = [
                            ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'label' => 'Đánh giá trung bình', 'value' => $avgRating > 0 ? number_format($avgRating, 1).' ⭐' : 'Chưa có', 'color' => '#f59e0b'],
                            ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Tổng học viên', 'value' => number_format($totalStudents), 'color' => '#2563eb'],
                            ['icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'label' => 'Khóa học đã đăng', 'value' => $totalCourses.' khóa', 'color' => '#10b981'],
                            ['icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'label' => 'Tổng đánh giá', 'value' => number_format($totalReviews), 'color' => '#8b5cf6'],
                        ];
                    @endphp
                    @foreach($statItems as $item)
                        <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f1f5f9;last:border-0;">
                            <div style="width:38px;height:38px;border-radius:10px;background:{{ $item['color'] }}1a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg style="width:18px;height:18px;color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            </div>
                            <div>
                                <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">{{ $item['label'] }}</div>
                                <div style="font-size:15px;font-weight:700;color:#1e293b;">{{ $item['value'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Social / Contact --}}
            <div style="background:#fff;border-radius:20px;border:1.5px solid #e2e8f0;overflow:hidden;">
                <div style="padding:24px;border-bottom:1px solid #f1f5f9;">
                    <h2 style="font-size:17px;font-weight:700;color:#1e293b;margin:0;">Liên hệ</h2>
                </div>
                <div style="padding:20px 24px;display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:16px;height:16px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span style="font-size:13px;color:#475569;">{{ $user->email }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:16px;height:16px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span style="font-size:13px;color:#475569;">Tham gia {{ $user->created_at->format('m/Y') }}</span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── RIGHT: Courses ── --}}
        <main>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
                <div>
                    <h2 style="font-size:22px;font-weight:800;color:#1e293b;margin:0 0 4px;">Khóa học của giảng viên</h2>
                    <p style="font-size:14px;color:#64748b;margin:0;">{{ $courses->total() }} khóa học đang được mở</p>
                </div>
                @if($courses->total() > 0)
                    <span style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:1.5px solid #bfdbfe;color:#1d4ed8;font-size:13px;font-weight:600;padding:6px 16px;border-radius:999px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        {{ $courses->total() }} khóa học
                    </span>
                @endif
            </div>

            @if($courses->isEmpty())
                {{-- Empty state --}}
                <div style="text-align:center;background:#fff;border-radius:20px;border:1.5px solid #e2e8f0;padding:72px 40px;">
                    <div style="width:80px;height:80px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg style="width:40px;height:40px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 7l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 8px;">Chưa có khóa học nào</h3>
                    <p style="font-size:14px;color:#64748b;margin:0;">Giảng viên này chưa có khóa học đã xuất bản.</p>
                </div>
            @else
                {{-- Course grid --}}
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
                    @foreach($courses as $course)
                    @php
                        $discountPrice  = $course->discount_price ?? $course->sale_price;
                        $displayPrice   = $discountPrice ?? $course->price;
                        $originalPrice  = $discountPrice ? $course->price : null;
                        $gradient       = $gradients[$course->id % count($gradients)];
                        $durationHours  = $course->duration_minutes ? floor($course->duration_minutes / 60) : 0;
                        $durationMins   = $course->duration_minutes ? $course->duration_minutes % 60 : 0;
                    @endphp
                    <article
                        style="background:#fff;border-radius:16px;border:1.5px solid #e2e8f0;overflow:hidden;display:flex;flex-direction:column;transition:all .3s cubic-bezier(.4,0,.2,1);"
                        onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 48px rgba(37,99,235,.12)';this.style.borderColor='#93c5fd';"
                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';this.style.borderColor='#e2e8f0';"
                    >
                        {{-- Thumbnail --}}
                        <a href="{{ route('courses.show', $course->slug) }}" style="display:block;aspect-ratio:16/9;overflow:hidden;background:linear-gradient(135deg,#3730a3,#1d4ed8);flex-shrink:0;">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                                     style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="course-thumb">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                    <svg style="width:40px;height:40px;color:rgba(255,255,255,.5);" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                </div>
                            @endif
                        </a>

                        {{-- Content --}}
                        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
                            @if($course->category)
                                <a href="{{ route('courses.category', $course->category->slug) }}"
                                   style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#2563eb;text-decoration:none;margin-bottom:6px;display:inline-block;"
                                >{{ $course->category->name }}</a>
                            @endif

                            <h3 style="font-size:15px;font-weight:700;color:#1e293b;line-height:1.4;margin:0 0 8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <a href="{{ route('courses.show', $course->slug) }}" style="color:inherit;text-decoration:none;transition:color .2s;"
                                   onmouseover="this.style.color='#2563eb';" onmouseout="this.style.color='#1e293b';"
                                >{{ $course->title }}</a>
                            </h3>

                            {{-- Rating --}}
                            <div style="display:flex;align-items:center;gap:4px;margin-bottom:8px;">
                                @php $cr = (float)$course->rating_avg; @endphp
                                <span style="font-size:12px;font-weight:700;color:#b45309;">{{ number_format($cr, 1) }}</span>
                                @for($i = 1; $i <= 5; $i++)
                                    <svg style="width:12px;height:12px;{{ $i <= round($cr) ? 'fill:#f59e0b;' : 'fill:#e2e8f0;' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span style="font-size:11px;color:#94a3b8;">({{ number_format($course->rating_count) }})</span>
                                <span style="font-size:11px;color:#94a3b8;margin-left:4px;">• {{ number_format($course->enrollment_count) }} học viên</span>
                            </div>

                            {{-- Meta --}}
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#64748b;">
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @if($durationHours > 0)
                                        {{ $durationHours }}h {{ $durationMins }}m
                                    @else
                                        {{ $course->lessons_count ?? 0 }} bài
                                    @endif
                                </span>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:999px;">
                                    {{ $levelLabels[$course->level] ?? $course->level }}
                                </span>
                            </div>

                            {{-- Price + CTA --}}
                            <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                <div>
                                    @if($displayPrice == 0)
                                        <span style="font-size:15px;font-weight:800;color:#10b981;">Miễn phí</span>
                                    @else
                                        <span style="font-size:15px;font-weight:800;color:#1e293b;">{{ number_format($displayPrice, 0, ',', '.') }}đ</span>
                                        @if($originalPrice)
                                            <span style="font-size:12px;color:#94a3b8;text-decoration:line-through;margin-left:4px;">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                        @endif
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}"
                                   style="display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:#fff;font-size:12px;font-weight:600;padding:8px 14px;border-radius:9px;text-decoration:none;white-space:nowrap;transition:all .2s;"
                                   onmouseover="this.style.background='#1d4ed8';this.style.boxShadow='0 4px 12px rgba(37,99,235,.35)';" onmouseout="this.style.background='#2563eb';this.style.boxShadow='none';"
                                >
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    Xem khóa học
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                {{-- Courses Pagination --}}
                @if($courses->hasPages())
                    <div style="margin-top:36px;display:flex;justify-content:center;align-items:center;gap:4px;">
                        @if($courses->onFirstPage())
                            <span style="padding:8px 14px;border-radius:8px;border:1.5px solid #e2e8f0;color:#cbd5e1;font-size:14px;cursor:default;">‹</span>
                        @else
                            <a href="{{ $courses->previousPageUrl() }}"
                               style="padding:8px 14px;border-radius:8px;border:1.5px solid #e2e8f0;color:#374151;font-size:14px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='#2563eb';this.style.color='#2563eb';" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151';"
                            >‹</a>
                        @endif

                        @foreach($courses->getUrlRange(max(1, $courses->currentPage()-2), min($courses->lastPage(), $courses->currentPage()+2)) as $page => $url)
                            @if($page == $courses->currentPage())
                                <span style="padding:8px 14px;border-radius:8px;border:1.5px solid #2563eb;background:#2563eb;color:#fff;font-size:14px;font-weight:700;min-width:40px;text-align:center;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                   style="padding:8px 14px;border-radius:8px;border:1.5px solid #e2e8f0;color:#374151;font-size:14px;text-decoration:none;min-width:40px;text-align:center;transition:all .2s;"
                                   onmouseover="this.style.borderColor='#2563eb';this.style.color='#2563eb';" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151';"
                                >{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($courses->hasMorePages())
                            <a href="{{ $courses->nextPageUrl() }}"
                               style="padding:8px 14px;border-radius:8px;border:1.5px solid #e2e8f0;color:#374151;font-size:14px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='#2563eb';this.style.color='#2563eb';" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151';"
                            >›</a>
                        @else
                            <span style="padding:8px 14px;border-radius:8px;border:1.5px solid #e2e8f0;color:#cbd5e1;font-size:14px;cursor:default;">›</span>
                        @endif
                    </div>
                @endif
            @endif
        </main>
    </div>
</div>

<style>
article:hover .course-thumb { transform: scale(1.05); }
</style>

@endsection
