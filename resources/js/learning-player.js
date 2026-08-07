document.addEventListener('DOMContentLoaded', () => {
    initLearningSidebar();
    initVideoProgressV2();
    initQuizPlayer();
    initMarkComplete();
    initCertificateDropdown();
    initLessonNotes();
    initStudyNotesPage();
    initLessonAi();
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('learning-toast');
    if (!toast) return;

    toast.textContent = message;
    toast.className = `learning-toast learning-toast--${type}`;
    toast.hidden = false;

    window.clearTimeout(showToast._timer);
    showToast._timer = window.setTimeout(() => {
        toast.hidden = true;
    }, 2800);
}

function updateHeaderProgress(percent) {
    const bar = document.querySelector('[data-header-progress-bar]');
    const text = document.querySelector('[data-header-progress-text]');
    const safe = Math.min(100, Math.max(0, Number(percent) || 0));

    if (bar) bar.style.width = `${safe}%`;
    if (text) text.textContent = `${Math.round(safe)}%`;
}

function initLearningSidebar() {
    const sidebar = document.querySelector('[data-learning-sidebar]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const main = document.querySelector('[data-learning-main]');
    if (!sidebar) return;

    const setOpen = (open) => {
        sidebar.dataset.sidebarOpen = open ? 'true' : 'false';
        sidebar.classList.toggle('learning-sidebar--closed', !open);
        if (main) main.classList.toggle('learning-main--expanded', !open);
        if (backdrop) backdrop.classList.toggle('hidden', open || window.innerWidth >= 1024);
    };

    document.querySelectorAll('[data-toggle-sidebar], [data-toggle-sidebar-desktop]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const isOpen = sidebar.dataset.sidebarOpen !== 'false';
            setOpen(!isOpen);
        });
    });

    document.querySelector('[data-close-sidebar]')?.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));

    if (window.innerWidth < 1024) {
        setOpen(false);
    }
}

function initVideoProgress() {
    const video = document.querySelector('[data-lesson-progress-video]');
    if (!video) return;

    const progressUrl = video.dataset.progressUrl;
    const requiredPercent = Number(video.dataset.requiredPercent || 90) / 100;
    const durationHint = Number(video.dataset.durationSeconds || 0);
    let lastSentAt = 0;
    let completed = video.dataset.initialCompleted === '1';
    let requestInFlight = false;
    let pendingCompleted = false;

    const sendProgress = async (forceCompleted = false, forceSend = false) => {
        if (!progressUrl) return;

        const watchedSeconds = Math.floor(Math.max(
            Number(video.currentTime || 0),
            Number(video.dataset.initialWatched || 0),
        ));

        const duration = Number.isFinite(video.duration) && video.duration > 0 ? video.duration : durationHint;
        const reachedThreshold = duration > 0 && watchedSeconds >= Math.ceil(duration * requiredPercent);
        const shouldComplete = forceCompleted || reachedThreshold;

        if (!forceSend && !shouldComplete && watchedSeconds - lastSentAt < 15) {
            return;
        }

        if (completed && shouldComplete) return;

        if (requestInFlight) {
            pendingCompleted = pendingCompleted || shouldComplete;
            return;
        }

        requestInFlight = true;
        lastSentAt = watchedSeconds;

        try {
            const response = await fetch(progressUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    watched_seconds: watchedSeconds,
                    completed: shouldComplete,
                }),
            });

            if (!response.ok) throw new Error('progress_failed');

            const data = await response.json();
            if (data.lesson_completed) {
                completed = true;
                showToast('Đã lưu tiến độ bài học.');
            }

            if (typeof data.course_progress === 'number') {
                updateHeaderProgress(data.course_progress);
            }
            if (typeof data.lesson_progress === 'number') {
                updateCurrentLessonProgress(data.lesson_progress, data.lesson_completed);
            }
        } catch {
            showToast('Chưa lưu được tiến độ. Hệ thống sẽ thử lại.', 'error');
        } finally {
            requestInFlight = false;
            if (pendingCompleted && !completed) {
                pendingCompleted = false;
                sendProgress(true, true);
            }
        }
    };

    video.addEventListener('loadedmetadata', () => {
        if (requestedStartTimeFromUrl() !== null) return;

        const watchedSeconds = Number(video.dataset.initialWatched || 0);
        if (!completed && watchedSeconds > 0 && Number.isFinite(video.duration) && watchedSeconds < video.duration - 3) {
            video.currentTime = watchedSeconds;
        }
    }, { once: true });

    video.addEventListener('timeupdate', () => sendProgress(false, false));
    video.addEventListener('pause', () => sendProgress(false, true));
    video.addEventListener('ended', () => sendProgress(true, true));

    window.addEventListener('beforeunload', () => {
        if (!completed && video.currentTime > 0) {
            navigator.sendBeacon?.(
                progressUrl,
                new Blob([JSON.stringify({
                    watched_seconds: Math.floor(video.currentTime),
                    completed: false,
                })], { type: 'application/json' }),
            );
        }
    });
}

function initVideoProgressV2() {
    const video = document.querySelector('[data-lesson-progress-video]');
    if (!video) return;

    const progressUrl = video.dataset.progressUrl;
    const durationHint = Number(video.dataset.durationSeconds || 0);
    const resumeThreshold = 5;
    let lastSavedPosition = Math.floor(Number(video.dataset.initialLastPosition || video.dataset.initialWatched || 0));
    let furthestPosition = Math.floor(Number(video.dataset.initialFurthestPosition || video.dataset.initialWatched || 0));
    let unsavedPlayedSeconds = 0;
    let lastPlayhead = null;
    let lastSaveStartedAt = Date.now();
    let completed = video.dataset.initialCompleted === '1';
    let requestInFlight = false;
    let pendingSave = false;

    const durationSeconds = () => Math.floor(Number.isFinite(video.duration) && video.duration > 0 ? video.duration : durationHint);
    const currentPosition = () => clampVideoTime(video.currentTime || 0, durationSeconds());
    const nowIso = () => new Date().toISOString();

    const notePlayedSegment = () => {
        const current = currentPosition();
        if (lastPlayhead === null) {
            lastPlayhead = current;
            return;
        }

        const delta = current - lastPlayhead;
        if (!video.paused && !video.seeking && delta > 0 && delta <= 2.5) {
            unsavedPlayedSeconds += delta;
            furthestPosition = Math.max(furthestPosition, current);
        }
        lastPlayhead = current;
    };

    const payload = () => ({
        last_position_seconds: currentPosition(),
        furthest_position_seconds: Math.floor(furthestPosition),
        played_seconds: Math.floor(unsavedPlayedSeconds),
        video_duration_seconds: durationSeconds(),
        client_updated_at: nowIso(),
        completed: video.ended || currentPosition() >= durationSeconds() - 1,
    });

    const applyProgressResponse = (data) => {
        if (typeof data.course_progress === 'number') {
            updateHeaderProgress(data.course_progress);
        }

        if (typeof data.lesson_progress === 'number') {
            updateCurrentLessonProgress(data.lesson_progress, data.lesson_completed);
        }

        if (typeof data.last_position_seconds === 'number') {
            lastSavedPosition = data.last_position_seconds;
        }

        if (typeof data.furthest_position_seconds === 'number') {
            furthestPosition = Math.max(furthestPosition, data.furthest_position_seconds);
        }

        if (data.lesson_completed) {
            completed = true;
        }
    };

    const sendProgress = async (forceSend = false, options = {}) => {
        if (!progressUrl) return;

        notePlayedSegment();
        const body = payload();
        const hasPlayed = body.played_seconds > 0;
        const positionChanged = Math.abs(body.last_position_seconds - lastSavedPosition) >= 1;
        const elapsed = Date.now() - lastSaveStartedAt;

        if (!forceSend && (!hasPlayed || elapsed < 10000)) {
            return;
        }

        if (requestInFlight) {
            pendingSave = true;
            return;
        }

        requestInFlight = true;
        lastSaveStartedAt = Date.now();

        try {
            const response = await fetch(progressUrl, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: Boolean(options.keepalive),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(body),
            });

            const data = await response.json().catch(() => ({}));
            if (response.status === 409) {
                applyProgressResponse(data);
                return;
            }

            if (!response.ok) throw new Error('progress_failed');

            unsavedPlayedSeconds = 0;
            applyProgressResponse(data);
        } catch {
            if (!options.silent) {
                showToast('Chưa lưu được tiến độ. Hệ thống sẽ thử lại.', 'error');
            }
        } finally {
            requestInFlight = false;
            if (pendingSave) {
                pendingSave = false;
                sendProgress(true, { silent: true });
            }
        }
    };

    const sendBeaconProgress = () => {
        if (!progressUrl) return;

        notePlayedSegment();
        const body = payload();
        if (body.played_seconds <= 0 && Math.abs(body.last_position_seconds - lastSavedPosition) < 1) return;

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        Object.entries(body).forEach(([key, value]) => formData.append(key, value));
        navigator.sendBeacon?.(progressUrl, formData);
    };

    const showResumePrompt = () => {
        const saved = clampVideoTime(lastSavedPosition, durationSeconds());
        if (saved < resumeThreshold) return;

        const stage = video.closest('.learning-video-stage');
        if (!stage || stage.querySelector('[data-video-resume-panel]')) return;

        const panel = document.createElement('div');
        panel.dataset.videoResumePanel = '1';
        panel.className = 'absolute left-4 top-4 z-[60] max-w-sm rounded border border-white/20 bg-black/75 p-4 text-white shadow-lg';
        panel.innerHTML = `
            <p class="text-sm font-semibold">Bạn đã học đến ${formatTime(saved)}</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" data-resume-video class="rounded bg-[#0056D2] px-3 py-2 text-sm font-bold text-white hover:bg-[#0046B8]">Tiếp tục học</button>
                <button type="button" data-replay-video class="rounded border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/10">Xem lại từ đầu</button>
            </div>
        `;
        stage.appendChild(panel);

        panel.querySelector('[data-resume-video]')?.addEventListener('click', () => {
            video.currentTime = saved;
            lastPlayhead = saved;
            panel.remove();
            video.play().catch(() => {});
        });

        panel.querySelector('[data-replay-video]')?.addEventListener('click', () => {
            video.currentTime = 0;
            lastPlayhead = 0;
            panel.remove();
            sendProgress(true, { silent: true });
        });
    };

    video.addEventListener('loadedmetadata', () => {
        if (requestedStartTimeFromUrl() !== null) return;
        showResumePrompt();
    }, { once: true });

    video.addEventListener('play', () => {
        lastPlayhead = currentPosition();
    });
    video.addEventListener('timeupdate', () => {
        notePlayedSegment();
        sendProgress(false, { silent: true });
    });
    video.addEventListener('seeking', () => {
        lastPlayhead = null;
        sendProgress(true, { silent: true });
    });
    video.addEventListener('pause', () => sendProgress(true));
    video.addEventListener('ended', () => sendProgress(true));
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) sendProgress(true, { keepalive: true, silent: true });
    });
    window.addEventListener('pagehide', sendBeaconProgress);
    window.addEventListener('beforeunload', sendBeaconProgress);
}

function updateCurrentLessonProgress(percent, completed = false) {
    const item = document.querySelector('[data-current-lesson-item]');
    if (!item) return;

    const safe = Math.min(100, Math.max(0, Number(percent) || 0));
    const percentEl = item.querySelector('[data-lesson-progress-percent]');
    const statusEl = item.querySelector('[data-lesson-progress-status]');

    if (percentEl) percentEl.textContent = `${Math.round(safe)}%`;
    if (statusEl) statusEl.textContent = completed ? 'Hoàn thành' : (safe > 0 ? 'Đang học' : 'Chưa học');
}

function initMarkComplete() {
    document.querySelector('[data-mark-lesson-complete]')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const url = document.querySelector('[data-learning-player]')?.dataset.progressUrl;

        if (!url) return;

        button.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ watched_seconds: 0, completed: true }),
            });

            if (!response.ok) throw new Error('complete_failed');

            const data = await response.json();
            showToast('Đã đánh dấu hoàn thành bài học.');
            if (typeof data.course_progress === 'number') {
                updateHeaderProgress(data.course_progress);
            }
            button.textContent = 'Đã hoàn thành';
        } catch {
            button.disabled = false;
            showToast('Không thể đánh dấu hoàn thành.', 'error');
        }
    });
}

function initQuizPlayer() {
    const root = document.querySelector('[data-quiz-player]');
    if (!root) return;

    const quiz = JSON.parse(root.dataset.quiz || '{}');
    if (!quiz.questions?.length) return;

    const intro = root.querySelector('[data-quiz-intro]');
    const active = root.querySelector('[data-quiz-active]');
    const result = root.querySelector('[data-quiz-result]');
    const questionContainer = root.querySelector('[data-quiz-question-container]');
    const progressLabel = root.querySelector('[data-quiz-progress-label]');
    const progressBar = root.querySelector('[data-quiz-progress-bar]');
    const timerEl = root.querySelector('[data-quiz-timer]');
    const prevBtn = root.querySelector('[data-quiz-prev]');
    const nextBtn = root.querySelector('[data-quiz-next]');

    let currentIndex = 0;
    const answers = {};
    let timerId = null;
    let remainingSeconds = quiz.time_limit_minutes ? quiz.time_limit_minutes * 60 : null;

    const renderQuestion = () => {
        const question = quiz.questions[currentIndex];
        const isMultiple = question.type === 'multiple';
        const selected = answers[question.id] || [];

        progressLabel.textContent = `Câu ${currentIndex + 1} / ${quiz.questions.length}`;
        progressBar.style.width = `${((currentIndex + 1) / quiz.questions.length) * 100}%`;
        prevBtn.disabled = currentIndex === 0;
        // Always re-enable nextBtn when rendering a question (prevents it getting stuck disabled)
        nextBtn.disabled = false;
        nextBtn.textContent = currentIndex === quiz.questions.length - 1 ? 'Nộp bài' : 'Câu tiếp theo';

        questionContainer.innerHTML = `
            <div class="rounded border border-white/10 bg-white/5 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-300">${question.form_type || question.type}</p>
                <h3 class="mt-2 text-lg font-bold">${escapeHtml(question.question)}</h3>
                <p class="mt-1 text-xs text-white/60">${question.points} điểm</p>
                <div class="mt-4 space-y-2">
                    ${question.options.map((option) => {
                        const checked = selected.includes(option.id);
                        const inputType = isMultiple ? 'checkbox' : 'radio';
                        const name = isMultiple ? `q_${question.id}[]` : `q_${question.id}`;
                        return `
                            <label class="flex cursor-pointer items-start gap-3 rounded border border-white/10 p-3 hover:bg-white/5">
                                <input type="${inputType}" name="${name}" value="${option.id}" ${checked ? 'checked' : ''} class="mt-1" data-option-input data-question-id="${question.id}">
                                <span class="text-sm leading-6">${escapeHtml(option.text)}</span>
                            </label>
                        `;
                    }).join('')}
                </div>
            </div>
        `;

        questionContainer.querySelectorAll('[data-option-input]').forEach((input) => {
            input.addEventListener('change', () => {
                const qid = Number(input.dataset.questionId);
                const q = quiz.questions.find((item) => item.id === qid);
                if (!q) return;

                if (q.type === 'multiple') {
                    const checked = [...questionContainer.querySelectorAll(`[data-question-id="${qid}"]:checked`)].map((el) => Number(el.value));
                    answers[qid] = checked;
                } else {
                    answers[qid] = [Number(input.value)];
                }
            });
        });
    };

    const startTimer = () => {
        if (!remainingSeconds || !timerEl) return;
        timerEl.hidden = false;
        timerEl.textContent = formatTime(remainingSeconds);

        timerId = window.setInterval(() => {
            remainingSeconds -= 1;
            timerEl.textContent = formatTime(remainingSeconds);
            if (remainingSeconds <= 0) {
                window.clearInterval(timerId);
                submitQuiz(true);
            }
        }, 1000);
    };

    const startQuiz = () => {
        intro.hidden = true;
        active.hidden = false;
        currentIndex = 0;
        renderQuestion();
        startTimer();
    };

    const submitQuiz = async (auto = false) => {
        const unanswered = quiz.questions.filter((q) => !answers[q.id]?.length);
        if (!auto && unanswered.length > 0) {
            const ok = window.confirm(`Bạn còn ${unanswered.length} câu chưa trả lời. Bạn có chắc muốn nộp bài?`);
            if (!ok) return;
        }

        // Disable both navigation buttons while submitting
        nextBtn.disabled = true;
        nextBtn.textContent = 'Đang nộp bài...';
        prevBtn.disabled = true;
        if (timerId) window.clearInterval(timerId);

        const payload = { answers: {} };
        Object.entries(answers).forEach(([questionId, ids]) => {
            const question = quiz.questions.find((q) => String(q.id) === String(questionId));
            payload.answers[questionId] = question?.type === 'multiple' ? ids : ids[0];
        });

        try {
            const response = await fetch(quiz.submit_url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'submit_failed');
            }

            active.hidden = true;
            result.hidden = false;
            renderQuizResult(data);

            if (typeof data.course_progress === 'number') {
                updateHeaderProgress(data.course_progress);
            }
        } catch (error) {
            // Restore both buttons so user can retry or navigate
            nextBtn.disabled = false;
            nextBtn.textContent = 'Nộp bài';
            prevBtn.disabled = currentIndex === 0;
            showToast(error.message || 'Không thể nộp bài quiz.', 'error');
        }
    };

    const renderQuizResult = (data) => {
        const attempt = data.attempt;
        const passed = attempt.passed;
        result.innerHTML = `
            <div class="rounded border ${passed ? 'border-emerald-400/30 bg-emerald-500/10' : 'border-rose-400/30 bg-rose-500/10'} p-6">
                <p class="text-sm font-semibold uppercase tracking-wide ${passed ? 'text-emerald-300' : 'text-rose-300'}">${passed ? 'Đạt' : 'Chưa đạt'}</p>
                <h3 class="mt-2 text-2xl font-bold">${attempt.percent}%</h3>
                <p class="mt-2 text-sm text-white/80">${attempt.correct_count}/${attempt.total_questions} câu đúng · Điểm ${attempt.score}/${attempt.total_score} · Yêu cầu ${attempt.pass_score}%</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    ${data.remaining_attempts === null || data.remaining_attempts > 0
                        ? '<button type="button" data-quiz-retry class="rounded border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10">Làm lại</button>'
                        : ''}
                    ${data.next_lesson_url
                        ? `<a href="${data.next_lesson_url}" class="rounded bg-[#0056D2] px-4 py-2 text-sm font-bold text-white hover:bg-[#0046B8]">Bài tiếp theo</a>`
                        : ''}
                </div>
            </div>
        `;

        result.querySelector('[data-quiz-retry]')?.addEventListener('click', () => window.location.reload());
    };

    root.querySelector('[data-quiz-start]')?.addEventListener('click', startQuiz);

    prevBtn?.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex -= 1;
            renderQuestion();
        }
    });

    nextBtn?.addEventListener('click', () => {
        if (currentIndex < quiz.questions.length - 1) {
            currentIndex += 1;
            renderQuestion();
        } else {
            submitQuiz(false);
        }
    });
}

function formatTime(totalSeconds) {
    const safeSeconds = Math.max(0, Math.floor(Number(totalSeconds) || 0));
    const hours = Math.floor(safeSeconds / 3600);
    const minutes = Math.floor((safeSeconds % 3600) / 60);
    const seconds = safeSeconds % 60;

    if (hours > 0) {
        return `${hours}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    return `${minutes}:${String(seconds).padStart(2, '0')}`;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function initCertificateDropdown() {
    const dropdown = document.querySelector('[data-certificate-dropdown]');
    if (!dropdown) return;

    const trigger = dropdown.querySelector('[data-cert-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-cert-dropdown-panel]');
    if (!trigger || !panel) return;

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = panel.classList.contains('hidden');
        if (isHidden) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });
}

function requestedStartTimeFromUrl() {
    const raw = new URLSearchParams(window.location.search).get('t');
    if (raw === null || raw === '') return null;

    const value = Number(raw);
    if (!Number.isFinite(value) || value < 0) return null;

    return Math.floor(value);
}

function clampVideoTime(seconds, duration = 0) {
    const safe = Math.max(0, Math.floor(Number(seconds) || 0));
    const max = Math.max(0, Math.floor(Number(duration) || 0));

    return max > 0 ? Math.min(safe, max) : safe;
}

function parseJsonScript(root, selector) {
    const script = root.querySelector(selector);
    if (!script?.textContent) return [];

    try {
        return JSON.parse(script.textContent);
    } catch {
        return [];
    }
}

async function parseJsonResponse(response) {
    const raw = await response.text();
    if (!raw) return {};

    try {
        return JSON.parse(raw);
    } catch {
        return { success: false, message: 'Máy chủ trả về phản hồi không hợp lệ.' };
    }
}

function validationMessage(data, fallback = 'Dữ liệu ghi chú không hợp lệ.') {
    const errors = data?.errors || {};
    const firstError = Object.values(errors)[0];

    if (Array.isArray(firstError) && firstError.length) {
        return firstError[0];
    }

    return data?.message || fallback;
}

function initLessonNotes() {
    document.querySelectorAll('[data-lesson-notes]').forEach((root) => {
        if (root.dataset.canUseNotes !== '1') return;

        const isVideo = root.dataset.lessonType === 'video';
        const video = document.querySelector('video');
        const videoStage = document.querySelector('.learning-video-stage');
        const durationHint = Number(root.dataset.videoDuration || 0);
        const form = root.querySelector('[data-note-create-form]');
        const textarea = root.querySelector('[data-note-content]');
        const timestampInput = root.querySelector('[data-note-timestamp]');
        const timestampLabel = root.querySelector('[data-note-timestamp-label]');
        const submitButton = root.querySelector('[data-note-submit]');
        const statusEl = root.querySelector('[data-note-form-status]');
        const errorEl = root.querySelector('[data-note-form-error]');
        const charCount = root.querySelector('[data-note-char-count]');
        const list = root.querySelector('[data-note-list]');
        const empty = root.querySelector('[data-note-empty]');
        const count = root.querySelector('[data-note-count]');
        const storeUrl = root.dataset.storeUrl;
        let notes = parseJsonScript(root, '[data-lesson-notes-json]');
        let createInFlight = false;
        let capturedCurrentTime = false;

        const updateTimestampLabel = () => {
            if (timestampLabel && timestampInput) {
                timestampLabel.textContent = formatTime(timestampInput.value || 0);
            }
        };

        const setTimestampFromVideo = () => {
            if (!isVideo || !video || !timestampInput) return;

            video.pause();
            const duration = Number.isFinite(video.duration) && video.duration > 0 ? video.duration : durationHint;
            timestampInput.value = clampVideoTime(video.currentTime || 0, duration);
            updateTimestampLabel();
        };

        const seekVideoTo = async (seconds, shouldPlay = true) => {
            if (!video) return;

            const seek = () => {
                const duration = Number.isFinite(video.duration) && video.duration > 0 ? video.duration : durationHint;
                video.currentTime = clampVideoTime(seconds, duration);
                if (shouldPlay) {
                    video.play().catch(() => {});
                }
                videoStage?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            if (video.readyState >= 1) {
                seek();
            } else {
                video.addEventListener('loadedmetadata', seek, { once: true });
            }
        };

        const applyRequestedTimestamp = () => {
            const requested = requestedStartTimeFromUrl();
            if (requested !== null && isVideo) {
                seekVideoTo(requested, false);
            }
        };

        const updateCharCount = () => {
            if (charCount && textarea) {
                charCount.textContent = `${textarea.value.length}/2000`;
            }
        };

        const setError = (message = '') => {
            if (!errorEl) return;
            errorEl.textContent = message;
            errorEl.classList.toggle('hidden', !message);
        };

        const sortNotes = () => {
            if (isVideo) {
                notes.sort((a, b) => Number(a.timestamp_seconds ?? 0) - Number(b.timestamp_seconds ?? 0) || Number(a.id) - Number(b.id));
            } else {
                notes.sort((a, b) => Number(b.id) - Number(a.id));
            }
        };

        const renderNote = (note) => {
            const item = document.createElement('article');
            item.className = 'rounded border border-[#d1d7dc] bg-white p-4 text-sm';
            item.dataset.noteId = note.id;

            const meta = document.createElement('div');
            meta.className = 'mb-2 flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-[#6a6f73]';

            const leftMeta = document.createElement('div');
            leftMeta.className = 'flex flex-wrap items-center gap-2';
            if (note.timestamp_seconds !== null && note.timestamp_seconds !== undefined) {
                const timeButton = document.createElement('button');
                timeButton.type = 'button';
                timeButton.className = 'rounded bg-[#eef5ff] px-2 py-1 font-bold text-[#0056D2] hover:bg-[#dbeafe]';
                timeButton.textContent = note.timestamp_label || formatTime(note.timestamp_seconds);
                timeButton.addEventListener('click', () => seekVideoTo(note.timestamp_seconds, true));
                leftMeta.appendChild(timeButton);
            }
            const updated = document.createElement('span');
            updated.textContent = note.updated_at ? `Cập nhật ${note.updated_at}` : '';
            leftMeta.appendChild(updated);

            const actions = document.createElement('div');
            actions.className = 'flex items-center gap-2';
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'font-bold text-[#0056D2] hover:underline';
            editButton.textContent = 'Sửa';
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'font-bold text-red-600 hover:underline';
            deleteButton.textContent = 'Xóa';
            actions.append(editButton, deleteButton);

            meta.append(leftMeta, actions);

            const content = document.createElement('p');
            content.className = 'whitespace-pre-line leading-6 text-[#1c1d1f]';
            content.textContent = note.content;

            item.append(meta, content);

            editButton.addEventListener('click', () => openInlineEditor(item, note, content));
            deleteButton.addEventListener('click', () => deleteNote(note));

            return item;
        };

        const render = () => {
            sortNotes();
            list.innerHTML = '';
            notes.forEach((note) => list.appendChild(renderNote(note)));
            empty.hidden = notes.length > 0;
            count.textContent = `${notes.length} ghi chú`;
        };

        const openInlineEditor = (item, note, contentEl) => {
            if (item.querySelector('[data-note-edit-form]')) return;

            contentEl.hidden = true;
            const editForm = document.createElement('form');
            editForm.className = 'mt-3 space-y-3';
            editForm.dataset.noteEditForm = '1';

            const editTextarea = document.createElement('textarea');
            editTextarea.name = 'content';
            editTextarea.required = true;
            editTextarea.maxLength = 2000;
            editTextarea.rows = 4;
            editTextarea.className = 'w-full rounded border border-[#d1d7dc] px-3 py-2 text-sm leading-6 outline-none focus:ring-2 focus:ring-[#0056D2]';
            editTextarea.value = note.content;
            editForm.appendChild(editTextarea);

            let editTimestamp = null;
            let editTimeLabel = null;
            if (isVideo) {
                const row = document.createElement('label');
                row.className = 'flex flex-wrap items-center gap-2 text-sm font-semibold text-[#6a6f73]';
                row.textContent = 'Mốc giây';
                editTimestamp = document.createElement('input');
                editTimestamp.type = 'number';
                editTimestamp.min = '0';
                if (durationHint > 0) editTimestamp.max = String(durationHint);
                editTimestamp.name = 'timestamp_seconds';
                editTimestamp.value = note.timestamp_seconds ?? 0;
                editTimestamp.className = 'h-9 w-24 rounded border border-[#d1d7dc] px-2 text-sm text-[#1c1d1f] outline-none focus:ring-2 focus:ring-[#0056D2]';
                editTimeLabel = document.createElement('span');
                editTimeLabel.textContent = formatTime(editTimestamp.value);
                editTimestamp.addEventListener('input', () => {
                    editTimeLabel.textContent = formatTime(editTimestamp.value);
                });
                row.append(editTimestamp, editTimeLabel);
                editForm.appendChild(row);
            }

            const footer = document.createElement('div');
            footer.className = 'flex flex-wrap items-center justify-between gap-3';
            const editStatus = document.createElement('p');
            editStatus.className = 'text-xs text-[#6a6f73]';
            const buttons = document.createElement('div');
            buttons.className = 'flex gap-2';
            const cancel = document.createElement('button');
            cancel.type = 'button';
            cancel.className = 'rounded border border-[#d1d7dc] px-3 py-2 text-sm font-semibold hover:bg-[#f7f9fa]';
            cancel.textContent = 'Hủy';
            const save = document.createElement('button');
            save.type = 'submit';
            save.className = 'rounded bg-[#1c1d1f] px-3 py-2 text-sm font-bold text-white hover:bg-black disabled:opacity-60';
            save.textContent = 'Lưu';
            buttons.append(cancel, save);
            footer.append(editStatus, buttons);
            editForm.appendChild(footer);
            item.appendChild(editForm);

            cancel.addEventListener('click', () => {
                editForm.remove();
                contentEl.hidden = false;
            });

            editForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (save.disabled) return;
                save.disabled = true;
                editStatus.textContent = 'Đang lưu...';

                try {
                    const payload = { content: editTextarea.value.trim() };
                    if (isVideo) payload.timestamp_seconds = editTimestamp?.value === '' ? null : Number(editTimestamp?.value || 0);
                    const response = await fetch(note.update_url, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await parseJsonResponse(response);
                    if (!response.ok || !data.success) {
                        throw new Error(validationMessage(data, 'Không thể cập nhật ghi chú.'));
                    }
                    notes = notes.map((itemNote) => Number(itemNote.id) === Number(note.id) ? data.note : itemNote);
                    showToast('Đã cập nhật ghi chú.');
                    render();
                } catch (error) {
                    editStatus.textContent = error.message || 'Không thể cập nhật ghi chú.';
                    save.disabled = false;
                }
            });
        };

        const deleteNote = async (note) => {
            if (!window.confirm('Xóa ghi chú này?')) return;

            try {
                const response = await fetch(note.delete_url, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(validationMessage(data, 'Không thể xóa ghi chú.'));
                }
                notes = notes.filter((itemNote) => Number(itemNote.id) !== Number(note.id));
                showToast('Đã xóa ghi chú.');
                render();
            } catch (error) {
                showToast(error.message || 'Không thể xóa ghi chú.', 'error');
            }
        };

        textarea?.addEventListener('focus', () => {
            if (!capturedCurrentTime) {
                capturedCurrentTime = true;
                setTimestampFromVideo();
            }
        });
        textarea?.addEventListener('input', () => {
            if (!capturedCurrentTime) {
                capturedCurrentTime = true;
                setTimestampFromVideo();
            }
            updateCharCount();
        });
        timestampInput?.addEventListener('input', updateTimestampLabel);

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (createInFlight || !storeUrl) return;

            const content = textarea.value.trim();
            if (!content) {
                setError('Vui lòng nhập nội dung ghi chú.');
                return;
            }

            createInFlight = true;
            submitButton.disabled = true;
            statusEl.textContent = 'Đang lưu...';
            setError('');

            try {
                const payload = { content };
                if (isVideo) {
                    payload.timestamp_seconds = timestampInput?.value === '' ? null : Number(timestampInput?.value || 0);
                }

                const response = await fetch(storeUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await parseJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(validationMessage(data, 'Không thể lưu ghi chú.'));
                }

                notes.push(data.note);
                textarea.value = '';
                capturedCurrentTime = false;
                updateCharCount();
                statusEl.textContent = 'Đã lưu thành công.';
                showToast('Đã lưu ghi chú.');
                render();
            } catch (error) {
                statusEl.textContent = '';
                setError(error.message || 'Không thể lưu ghi chú.');
                showToast(error.message || 'Không thể lưu ghi chú.', 'error');
            } finally {
                createInFlight = false;
                submitButton.disabled = false;
            }
        });

        applyRequestedTimestamp();
        updateCharCount();
        updateTimestampLabel();
        render();
    });
}

function initStudyNotesPage() {
    document.querySelectorAll('[data-study-note-card]').forEach((card) => {
        const content = card.querySelector('[data-study-note-content]');
        const form = card.querySelector('[data-study-note-edit-form]');
        const editButton = card.querySelector('[data-study-note-edit]');
        const cancelButton = card.querySelector('[data-study-note-cancel]');
        const deleteButton = card.querySelector('[data-study-note-delete]');
        const saveButton = card.querySelector('[data-study-note-save]');
        const textarea = card.querySelector('[data-study-note-edit-content]');
        const timestamp = card.querySelector('[data-study-note-edit-timestamp]');
        const timeLabel = card.querySelector('[data-study-note-edit-time-label]');
        const status = card.querySelector('[data-study-note-status]');
        const displayTime = card.querySelector('[data-study-note-time]');
        const updateUrl = card.dataset.updateUrl;
        const deleteUrl = card.dataset.deleteUrl;
        const isVideo = card.dataset.isVideo === '1';
        let inFlight = false;

        timestamp?.addEventListener('input', () => {
            if (timeLabel) timeLabel.textContent = formatTime(timestamp.value || 0);
        });

        editButton?.addEventListener('click', () => {
            form?.classList.remove('hidden');
            content.hidden = true;
            textarea?.focus();
        });

        cancelButton?.addEventListener('click', () => {
            form?.classList.add('hidden');
            content.hidden = false;
        });

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (inFlight) return;

            inFlight = true;
            saveButton.disabled = true;
            status.textContent = 'Đang lưu...';

            try {
                const payload = { content: textarea.value.trim() };
                if (isVideo) payload.timestamp_seconds = timestamp?.value === '' ? null : Number(timestamp?.value || 0);

                const response = await fetch(updateUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await parseJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(validationMessage(data, 'Không thể cập nhật ghi chú.'));
                }

                content.textContent = data.note.content;
                if (displayTime && data.note.timestamp_label) displayTime.textContent = data.note.timestamp_label;
                form.classList.add('hidden');
                content.hidden = false;
                status.textContent = '';
                showToast('Đã cập nhật ghi chú.');
            } catch (error) {
                status.textContent = error.message || 'Không thể cập nhật ghi chú.';
            } finally {
                inFlight = false;
                saveButton.disabled = false;
            }
        });

        deleteButton?.addEventListener('click', async () => {
            if (inFlight || !window.confirm('Xóa ghi chú này?')) return;

            inFlight = true;
            deleteButton.disabled = true;

            try {
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(validationMessage(data, 'Không thể xóa ghi chú.'));
                }
                card.remove();
                showToast('Đã xóa ghi chú.');
            } catch (error) {
                deleteButton.disabled = false;
                showToast(error.message || 'Không thể xóa ghi chú.', 'error');
            } finally {
                inFlight = false;
            }
        });
    });
}

function initLessonAi() {
    const root = document.querySelector('[data-lesson-ai]');
    if (!root || root.getAttribute('data-can-use-ai') !== '1') return;

    const summaryUrl = root.dataset.aiSummaryUrl;
    const explainUrl = root.dataset.aiExplainUrl;
    const summaryBox = root.querySelector('[data-ai-summary-box]');
    const keyPointsEl = root.querySelector('[data-ai-key-points]');
    const takeawaysEl = root.querySelector('[data-ai-takeaways]');
    const summaryStatus = root.querySelector('[data-ai-summary-status]');
    const summaryError = root.querySelector('[data-ai-summary-error]');
    const generateBtn = root.querySelector('[data-ai-generate-summary]');
    const askForm = root.querySelector('[data-ai-ask-form]');
    const askInput = root.querySelector('[data-ai-question-input]');
    const askSubmit = root.querySelector('[data-ai-ask-submit]');
    const askStatus = root.querySelector('[data-ai-ask-status]');
    const chatLog = root.querySelector('[data-ai-chat-log]');

    let summaryInFlight = false;
    let askInFlight = false;

    const aiErrorMessage = (data, fallback) => {
        if (data?.message) return data.message;

        const codeMessages = {
            missing_api_key: 'Chưa cấu hình GEMINI_API_KEY trong .env.',
            invalid_api_key: 'Khóa API Gemini không hợp lệ. Hãy tạo key mới tại Google AI Studio.',
            invalid_model: 'Model Gemini không hợp lệ. Hãy kiểm tra GEMINI_MODEL / GEMINI_FALLBACK_MODELS trong .env.',
            quota_exceeded: 'Gemini đã hết hạn mức trên các model đã thử. Hãy đợi vài phút hoặc đổi API key.',
            timeout: 'Kết nối AI bị quá thời gian chờ. Vui lòng thử lại.',
            ssl_error: 'Lỗi chứng chỉ SSL khi gọi Gemini. Kiểm tra cấu hình PHP/Laragon.',
            connection_error: 'Không kết nối được dịch vụ AI. Kiểm tra mạng rồi thử lại.',
            no_source: 'Bài học chưa có đủ nội dung văn bản để dùng AI.',
            content_blocked: 'Nội dung bị Gemini chặn bởi bộ lọc an toàn.',
            response_truncated: 'Phản hồi AI bị cắt vì quá dài. Hãy hỏi ngắn hơn.',
            empty_response: 'AI không trả về nội dung. Vui lòng thử lại.',
            invalid_response: 'Phản hồi AI không hợp lệ. Vui lòng thử lại.',
            invalid_request: 'Yêu cầu gửi tới AI không hợp lệ.',
            ai_unavailable: 'Dịch vụ Gemini đang gián đoạn. Vui lòng thử lại sau.',
            forbidden: 'Bạn không có quyền dùng AI hỗ trợ bài học.',
            lesson_mismatch: 'Bài học không thuộc khóa học này.',
            validation: 'Dữ liệu câu hỏi không hợp lệ.',
            too_many_requests: 'Bạn thao tác quá nhanh. Hãy đợi một lát rồi thử lại.',
        };

        if (data?.code && codeMessages[data.code]) {
            return codeMessages[data.code];
        }

        return fallback;
    };

    const parseJsonSafe = async (response) => {
        const raw = await response.text();
        if (!raw) return {};
        try {
            return JSON.parse(raw);
        } catch (error) {
            return {
                success: false,
                code: 'invalid_response',
                message: response.status === 429
                    ? 'Bạn thao tác quá nhanh. Hãy đợi một lát rồi thử lại.'
                    : 'Máy chủ trả về phản hồi không hợp lệ.',
            };
        }
    };

    const renderList = (el, items) => {
        if (!el) return;
        el.innerHTML = '';
        (items || []).forEach((item) => {
            const li = document.createElement('li');
            li.textContent = item;
            el.appendChild(li);
        });
    };

    const showSummaryError = (message) => {
        if (!summaryError) return;
        if (!message) {
            summaryError.textContent = '';
            summaryError.classList.add('hidden');
            return;
        }
        summaryError.textContent = message;
        summaryError.classList.remove('hidden');
    };

    const renderSummary = (data) => {
        if (summaryBox) {
            summaryBox.textContent = data.summary || data.message || 'Chưa có bản tóm tắt.';
        }
        renderList(keyPointsEl, data.key_points || []);
        renderList(takeawaysEl, data.takeaways || []);
        if (summaryStatus) {
            summaryStatus.textContent = data.summary
                ? (data.cached ? 'Đang dùng bản tóm tắt đã lưu.' : 'Đã tạo tóm tắt mới.')
                : '';
        }
    };

    const appendChat = (role, text) => {
        if (!chatLog) return;
        const item = document.createElement('div');
        item.className = role === 'user'
            ? 'rounded bg-[#eef5ff] px-3 py-2 text-sm text-[#1c1d1f]'
            : 'rounded bg-[#f7f9fa] px-3 py-2 text-sm text-[#1c1d1f]';
        item.innerHTML = `<strong class="block text-xs uppercase tracking-wide text-[#6a6f73]">${role === 'user' ? 'Bạn' : 'AI'}</strong><span class="mt-1 block whitespace-pre-line">${escapeHtml(text)}</span>`;
        chatLog.appendChild(item);
        chatLog.scrollTop = chatLog.scrollHeight;
    };

    const fetchSummary = async (generate = false) => {
        if (!summaryUrl) return { response: null, data: { success: false, message: 'Thiếu URL tóm tắt.' } };
        const url = generate ? `${summaryUrl}${summaryUrl.includes('?') ? '&' : '?'}generate=1` : summaryUrl;
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await parseJsonSafe(response);
        if (response.status === 429 && !data.code) {
            data.code = 'too_many_requests';
            data.message = data.message || aiErrorMessage({ code: 'too_many_requests' }, 'Thao tác quá nhanh.');
        }
        return { response, data };
    };

    const loadSummary = async () => {
        try {
            const { response, data } = await fetchSummary(false);
            if (!response?.ok || !data.success) {
                if (summaryBox) summaryBox.textContent = aiErrorMessage(data, 'Không tải được tóm tắt.');
                return;
            }
            renderSummary(data);
        } catch (error) {
            if (summaryBox) summaryBox.textContent = 'Không tải được tóm tắt do lỗi mạng. Vui lòng thử lại.';
        }
    };

    generateBtn?.addEventListener('click', async () => {
        if (summaryInFlight) return;
        summaryInFlight = true;
        generateBtn.disabled = true;
        showSummaryError('');
        if (summaryStatus) summaryStatus.textContent = 'Đang tạo tóm tắt...';

        try {
            const { response, data } = await fetchSummary(true);
            if (!response?.ok || !data.success) {
                const message = aiErrorMessage(data, 'Không tạo được tóm tắt.');
                showSummaryError(message);
                if (summaryStatus) summaryStatus.textContent = '';
                showToast(message, 'error');
                return;
            }
            renderSummary(data);
            showSummaryError('');
            showToast(data.cached ? 'Đã tải bản tóm tắt đã lưu.' : 'Đã tạo tóm tắt bài học.');
        } catch (error) {
            showSummaryError('Không tạo được tóm tắt do lỗi mạng hoặc máy chủ.');
            if (summaryStatus) summaryStatus.textContent = '';
            showToast('Không tạo được tóm tắt do lỗi mạng.', 'error');
        } finally {
            summaryInFlight = false;
            generateBtn.disabled = false;
        }
    });

    askForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!explainUrl || askInFlight || !askInput) return;

        const question = askInput.value.trim();
        if (!question) {
            if (askStatus) askStatus.textContent = 'Vui lòng nhập câu hỏi.';
            return;
        }
        if (question.length > 1000) {
            if (askStatus) askStatus.textContent = 'Câu hỏi tối đa 1000 ký tự.';
            return;
        }

        askInFlight = true;
        if (askSubmit) askSubmit.disabled = true;
        if (askStatus) askStatus.textContent = 'Đang giải thích...';
        appendChat('user', question);

        try {
            const response = await fetch(explainUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ question }),
            });
            const data = await parseJsonSafe(response);
            if (response.status === 429 && !data.code) {
                data.code = 'too_many_requests';
            }
            if (!response.ok || !data.success) {
                const message = aiErrorMessage(data, 'Không nhận được giải thích từ AI.');
                appendChat('assistant', message);
                if (askStatus) askStatus.textContent = message;
                showToast(message, 'error');
                return;
            }
            appendChat('assistant', data.answer);
            askInput.value = '';
            if (askStatus) askStatus.textContent = '';
        } catch (error) {
            appendChat('assistant', 'Không kết nối được AI. Vui lòng thử lại.');
            if (askStatus) askStatus.textContent = 'Lỗi kết nối mạng.';
            showToast('Không hỏi được AI do lỗi mạng.', 'error');
        } finally {
            askInFlight = false;
            if (askSubmit) askSubmit.disabled = false;
        }
    });

    loadSummary();
}
