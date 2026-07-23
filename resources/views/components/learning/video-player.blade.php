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
    $lessonCompleted = (bool) ($lessonProgress['is_completed'] ?? false);
    $durationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);

    // Kiểm tra video đã được convert sang HLS chưa
    $hlsDir = 'lesson-hls/' . $lesson->id . '/playlist.m3u8';
    $hasHls = \Illuminate\Support\Facades\Storage::disk('local')->exists($hlsDir);
@endphp

<div class="learning-video-stage relative flex min-h-[220px] w-full items-center justify-center bg-[#1c1d1f] sm:min-h-[320px] lg:min-h-[calc(100vh-14rem)] overflow-hidden" id="video-container-{{ $lesson->id }}">
    @if($videoSource || $lesson->video_path)
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
                    let lastSentProgress = 0;

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

                    async function fetchProgress() {
                        try {
                            const resp = await fetch(`/api/video/${lessonId}/progress`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                }
                            });
                            if (resp.ok) { return (await resp.json()).current_time || 0; }
                        } catch (e) { /* ignore */ }
                        return 0;
                    }

                    async function updateProgress(t) {
                        try {
                            await fetch(`/api/video/${lessonId}/progress`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                },
                                body: JSON.stringify({ current_time: t })
                            });
                        } catch (e) { /* ignore */ }
                    }

                    async function initHlsPlayer() {
                        currentToken = await fetchToken();
                        if (!currentToken) { console.error('Không lấy được token video'); return; }

                        const playlistUrl = `/api/video/hls/${lessonId}/playlist.m3u8?token=${currentToken}`;
                        const initialTime = await fetchProgress();

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

                        // Chống tua video đối với học sinh (khi isEnrolled = true và chưa hoàn thành)
                        const isEnrolled = {{ $isEnrolled ? 'true' : 'false' }};
                        const lessonCompleted = {{ $lessonCompleted ? 'true' : 'false' }};
                        let maxTimeWatched = initialTime;

                        if (isEnrolled && !lessonCompleted) {
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
                    videoElement.addEventListener('timeupdate', () => {
                        const ct = Math.floor(videoElement.currentTime);
                        if (ct - lastSentProgress >= 10) {
                            lastSentProgress = ct;
                            updateProgress(ct);
                        }
                    });
                });
            </script>
        @else
            <div class="px-6 py-12 text-center text-sm text-white">
                <p class="font-semibold text-lg">Video đang trong quá trình xử lý để bảo mật.</p>
                <p class="mt-2 text-white/70">Hệ thống đang tự động chuyển đổi định dạng. Vui lòng quay lại sau ít phút.</p>
            </div>
        @endif
    @else
        <div class="px-6 py-12 text-center text-sm text-white/70">
            Bài học này chưa có video để phát.
        </div>
    @endif
</div>
