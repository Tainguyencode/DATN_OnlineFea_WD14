document.addEventListener('DOMContentLoaded', () => {
    initLearningSidebar();
    initVideoProgress();
    initQuizPlayer();
    initMarkComplete();
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

        nextBtn.disabled = true;
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
            nextBtn.disabled = false;
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
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
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
