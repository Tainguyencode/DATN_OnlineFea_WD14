@props([
    'videoSource' => null,
    'lesson',
    'progressUrl' => null,
    'lessonProgress' => null,
    'requiredVideoPercent' => 80,
    'isEnrolled' => false,
])

@php
    $watchedSeconds = (int) ($lessonProgress['watched_seconds'] ?? 0);
    $lastPositionSeconds = (int) ($lessonProgress['last_position_seconds'] ?? $watchedSeconds);
    $furthestPositionSeconds = (int) ($lessonProgress['furthest_position_seconds'] ?? $watchedSeconds);
    $progressPercent = (float) ($lessonProgress['progress_percent'] ?? 0);
    $lessonCompleted = (bool) ($lessonProgress['is_completed'] ?? false);
    $durationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);

    // Kiểm tra video đã được convert sang HLS chưa
    $hlsDir = 'lesson-hls/' . $lesson->id . '/playlist.m3u8';
    $hasHls = \Illuminate\Support\Facades\Storage::disk('local')->exists($hlsDir);
@endphp

<div class="learning-video-stage relative flex min-h-[220px] w-full items-center justify-center bg-[#1c1d1f] sm:min-h-[320px] lg:min-h-[calc(100vh-14rem)] overflow-hidden" id="video-container-{{ $lesson->id }}">
    @if($hasHls)
        <video
            id="learning-video-{{ $lesson->id }}"
            controls
            preload="metadata"
            playsinline
            class="aspect-video max-h-[calc(100vh-14rem)] w-full max-w-full bg-black"
            @if($isEnrolled && $progressUrl)
                data-lesson-progress-video
                data-progress-url="{{ $progressUrl }}"
                data-initial-watched="{{ $watchedSeconds }}"
                data-initial-last-position="{{ $lastPositionSeconds }}"
                data-initial-furthest-position="{{ $furthestPositionSeconds }}"
                data-initial-progress-percent="{{ $progressPercent }}"
                data-initial-completed="{{ $lessonCompleted ? '1' : '0' }}"
                data-duration-seconds="{{ $durationSeconds }}"
                data-required-percent="{{ $requiredVideoPercent }}"
            @endif
        >
            Trình duyệt không hỗ trợ phát video HTML5.
        </video>

        {{-- Dynamic Watermark --}}
        @auth
            <div id="dynamic-watermark-{{ $lesson->id }}" class="absolute pointer-events-none select-none transition-all duration-[2000ms] ease-in-out" style="z-index: 50; font-size: 13px; color: rgba(255,255,255,0.08); letter-spacing: 1px;">
                {{ auth()->user()->email }} · ID: {{ auth()->id() }}
            </div>
        @endauth

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const videoId    = 'learning-video-{{ $lesson->id }}';
                const videoElement = document.getElementById(videoId);
                const lessonId   = '{{ $lesson->id }}';
                const watermark  = document.getElementById('dynamic-watermark-' + lessonId);
                const container  = document.getElementById('video-container-' + lessonId);
                let currentToken = null;
                let hls          = null;

                // ─── Watermark di chuyển ngẫu nhiên mỗi 5 giây ───
                if (watermark && container) {
                    function moveWatermark() {
                        const maxX = container.clientWidth  - watermark.clientWidth;
                        const maxY = container.clientHeight - watermark.clientHeight;
                        watermark.style.left = Math.max(0, Math.floor(Math.random() * maxX)) + 'px';
                        watermark.style.top  = Math.max(0, Math.floor(Math.random() * maxY)) + 'px';
                    }
                    moveWatermark();
                    setInterval(moveWatermark, 5000);
                }

                // Khởi tạo HLS Player
                const hlsScript = document.createElement('script');
                hlsScript.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
                hlsScript.onload = function () { initHlsPlayer(); };
                document.head.appendChild(hlsScript);

                async function fetchToken() {
                    try {
                        const resp = await fetch(`/api/video/${lessonId}/token`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            }
                        });
                        if (resp.ok) { return (await resp.json()).token; }
                    } catch (e) { console.error('fetchToken error', e); }
                    return null;
                }

                async function initHlsPlayer() {
                    currentToken = await fetchToken();
                    if (!currentToken) { console.error('Không lấy được token video'); return; }

                    const playlistUrl = `/api/video/hls/${lessonId}/playlist.m3u8?token=${currentToken}`;
                    const requestedTime = Number(new URLSearchParams(window.location.search).get('t') || 0);
                    const initialTime = requestedTime > 0 ? requestedTime : 0;

                    if (typeof Hls !== 'undefined' && Hls.isSupported()) {
                        if (hls) { hls.destroy(); }
                        hls = new Hls({
                            xhrSetup: function(xhr, url) {
                                if (currentToken) {
                                    if (url.includes('?token=')) {
                                        url = url.replace(/(token=)[^\&]+/, '$1' + currentToken);
                                    } else {
                                        url += (url.includes('?') ? '&' : '?') + 'token=' + currentToken;
                                    }
                                }
                                xhr.open('GET', url, true);
                            }
                        });
                        hls.loadSource(playlistUrl);
                        hls.attachMedia(videoElement);

                        hls.on(Hls.Events.MANIFEST_PARSED, function () {
                            if (initialTime > 0) { videoElement.currentTime = initialTime; }
                        });

                        hls.on(Hls.Events.ERROR, async function (_, data) {
                            if (data.fatal && data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                                hls.startLoad();
                            }
                        });
                    } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                        videoElement.src = playlistUrl;
                        videoElement.addEventListener('loadedmetadata', function () {
                            if (initialTime > 0) { videoElement.currentTime = initialTime; }
                        });
                    }

                    // Chống tua video đối với học sinh chưa hoàn thành bài học
                    const isStudent = {{ (auth()->check() && auth()->user()->isStudent()) ? 'true' : 'false' }};
                    const isEnrolled = {{ $isEnrolled ? 'true' : 'false' }};
                    const lessonCompleted = {{ $lessonCompleted ? 'true' : 'false' }};
                    let maxTimeWatched = Number(videoElement.dataset.initialFurthestPosition || videoElement.dataset.initialWatched || 0);

                    if (isStudent && isEnrolled && !lessonCompleted) {
                        videoElement.addEventListener('timeupdate', () => {
                            if (!videoElement.seeking && videoElement.currentTime > maxTimeWatched) {
                                maxTimeWatched = videoElement.currentTime;
                            }
                        });

                        videoElement.addEventListener('seeking', () => {
                            // Cho phép tua lùi hoặc tua trong phạm vi đã xem (+ sai số 2 giây)
                            if (videoElement.currentTime > maxTimeWatched + 2) {
                                videoElement.currentTime = maxTimeWatched;
                            }
                        });
                    }

                    // Token tự refresh mỗi 9 phút
                    setInterval(async () => {
                        const t = await fetchToken();
                        if (t) {
                            currentToken = t;
                        }
                    }, 9 * 60 * 1000);
                }

                // Progress: gửi mỗi 10 giây
                videoElement.dataset.progressManagedBy = 'learning-player.js';
            });
        </script>
    @elseif($lesson->video_url)
        @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
            @php
                preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lesson->video_url, $matches);
                $youtubeId = $matches[1] ?? null;
            @endphp
            @if($youtubeId)
                <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" class="aspect-video max-h-[calc(100vh-14rem)] w-full max-w-full bg-black border-0" allowfullscreen></iframe>
            @else
                <div class="px-6 py-12 text-center text-sm text-white">
                    <p class="font-semibold text-lg">Liên kết video bài học</p>
                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
                        Mở video trên YouTube ↗
                    </a>
                </div>
            @endif
        @elseif(str_contains($lesson->video_url, 'vimeo.com'))
            @php
                preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $lesson->video_url, $matches);
                $vimeoId = $matches[1] ?? null;
            @endphp
            @if($vimeoId)
                <iframe src="https://player.vimeo.com/video/{{ $vimeoId }}" class="aspect-video max-h-[calc(100vh-14rem)] w-full max-w-full bg-black border-0" allowfullscreen></iframe>
            @else
                <div class="px-6 py-12 text-center text-sm text-white">
                    <p class="font-semibold text-lg">Liên kết video Vimeo</p>
                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
                        Mở video trên Vimeo ↗
                    </a>
                </div>
            @endif
        @else
            <video src="{{ $lesson->video_url }}" controls class="aspect-video max-h-[calc(100vh-14rem)] w-full max-w-full bg-black">
                Trình duyệt không hỗ trợ phát video HTML5.
            </video>
        @endif
    @elseif($lesson->video_path && (\Illuminate\Support\Facades\Storage::disk('local')->exists($lesson->video_path) || \Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->video_path)))
        <div class="px-6 py-12 text-center text-sm text-white">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500/20 text-indigo-400 mb-3">
                <svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="font-semibold text-lg">Video đang trong quá trình xử lý để bảo mật.</p>
            <p class="mt-2 text-white/70">Hệ thống đang tự động chuyển đổi định dạng. Vui lòng quay lại sau ít phút.</p>
        </div>
    @elseif($lesson->video_path)
        <div class="px-6 py-12 text-center text-sm text-white/70">
            <svg class="mx-auto h-12 w-12 text-rose-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="font-semibold text-lg text-white">Video không tồn tại</p>
            <p class="mt-1 text-white/60">Tệp video bài học này không tồn tại trên hệ thống hoặc đã bị xóa.</p>
        </div>
    @else
        <div class="px-6 py-12 text-center text-sm text-white/70">
            <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 002-2H5a2 2 0 002 2v8a2 2 0 002 2z" />
            </svg>
            <p class="font-semibold text-base text-white">Bài học này chưa có video</p>
            <p class="mt-1 text-xs text-white/60">Giảng viên chưa tải video lên cho bài học này.</p>
        </div>
    @endif
</div>
