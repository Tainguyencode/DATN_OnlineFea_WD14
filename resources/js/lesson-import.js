const STATUS_PRESENTATION = {
    valid: {
        label: 'Hợp lệ',
        symbol: '✓',
        badgeClass: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        rowClass: '',
    },
    warning: {
        label: 'Cảnh báo',
        symbol: '!',
        badgeClass: 'border-amber-200 bg-amber-50 text-amber-800',
        rowClass: 'bg-amber-50/40',
    },
    error: {
        label: 'Lỗi',
        symbol: '×',
        badgeClass: 'border-rose-200 bg-rose-50 text-rose-800',
        rowClass: 'bg-rose-50/40',
    },
};

const TYPE_LABELS = {
    video: 'Video',
    document: 'Tài liệu',
    quiz: 'Quiz',
    assignment: 'Bài tập',
};

const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

function createElement(tag, className = '', text = null) {
    const element = document.createElement(tag);

    if (className) element.className = className;
    if (text !== null && text !== undefined) element.textContent = String(text);

    return element;
}

function firstValidationMessage(payload) {
    const errors = payload && typeof payload === 'object' ? payload.errors : null;

    if (!errors || typeof errors !== 'object') return null;

    for (const messages of Object.values(errors)) {
        if (Array.isArray(messages) && typeof messages[0] === 'string') return messages[0];
        if (typeof messages === 'string') return messages;
    }

    return null;
}

async function readJsonSafely(response) {
    const contentType = response.headers.get('content-type') || '';

    if (!contentType.toLowerCase().includes('application/json')) return null;

    try {
        return await response.json();
    } catch (error) {
        return null;
    }
}

function initializeLessonImport(root) {
    if (root.dataset.lessonImportInitialized === 'true') return;

    root.dataset.lessonImportInitialized = 'true';

    const elements = {
        panel: root.querySelector('[data-lesson-import-panel]'),
        heading: root.querySelector('[data-lesson-import-heading]'),
        subtitle: root.querySelector('[data-lesson-import-subtitle]'),
        selectStep: root.querySelector('[data-lesson-import-step="select"]'),
        previewStep: root.querySelector('[data-lesson-import-step="preview"]'),
        section: root.querySelector('[data-lesson-import-section]'),
        file: root.querySelector('[data-lesson-import-file]'),
        filename: root.querySelector('[data-lesson-import-filename]'),
        submit: root.querySelector('[data-lesson-import-submit]'),
        submitLabel: root.querySelector('[data-lesson-import-submit-label]'),
        error: root.querySelector('[data-lesson-import-error]'),
        errorMessage: root.querySelector('[data-lesson-import-error-message]'),
        errorGuidance: root.querySelector('[data-lesson-import-error-guidance]'),
        rows: root.querySelector('[data-lesson-import-rows]'),
        tableWrap: root.querySelector('[data-lesson-import-table-wrap]'),
        empty: root.querySelector('[data-lesson-import-empty]'),
        filterSummary: root.querySelector('[data-lesson-import-filter-summary]'),
        live: root.querySelector('[data-lesson-import-live]'),
    };

    if (!elements.panel || !elements.section || !elements.file || !elements.submit || !elements.rows) return;

    const triggers = document.querySelectorAll(`[data-lesson-import-open][aria-controls="${root.id}"]`);
    const filterButtons = [...root.querySelectorAll('[data-lesson-import-filter]')];
    const closeButtons = [...root.querySelectorAll('[data-lesson-import-close]')];
    const chooseAnotherButton = root.querySelector('[data-lesson-import-choose-another]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const state = {
        isOpen: false,
        isLoading: false,
        selectedSection: '',
        file: null,
        preview: null,
        batchToken: null,
        activeFilter: 'all',
        requestController: null,
        returnFocus: null,
    };

    function setLiveMessage(message) {
        elements.live.textContent = '';
        window.requestAnimationFrame(() => {
            elements.live.textContent = message;
        });
    }

    function showFileError(message) {
        elements.errorMessage.textContent = message;
        elements.error.classList.remove('hidden');
    }

    function clearFileError() {
        elements.errorMessage.textContent = '';
        elements.error.classList.add('hidden');
    }

    function selectedPreviewUrl() {
        return elements.section.selectedOptions[0]?.dataset.previewUrl || '';
    }

    function updateSubmitAvailability() {
        elements.submit.disabled = state.isLoading || !state.selectedSection || !state.file;
        elements.section.disabled = state.isLoading || elements.section.options.length <= 1;
        elements.file.disabled = state.isLoading || elements.section.options.length <= 1;
        elements.submit.setAttribute('aria-busy', String(state.isLoading));
        elements.submitLabel.textContent = state.isLoading ? 'Đang kiểm tra...' : 'Kiểm tra file';
    }

    function updateFilename() {
        if (!state.file) {
            elements.filename.textContent = '';
            elements.filename.classList.add('hidden');
            return;
        }

        elements.filename.textContent = `Đã chọn: ${state.file.name}`;
        elements.filename.classList.remove('hidden');
    }

    function setStep(step) {
        const isPreview = step === 'preview';

        elements.selectStep.classList.toggle('hidden', isPreview);
        elements.previewStep.classList.toggle('hidden', !isPreview);
        elements.panel.classList.toggle('max-w-2xl', !isPreview);
        elements.panel.classList.toggle('max-w-6xl', isPreview);
        elements.subtitle.textContent = isPreview
            ? 'Kiểm tra dữ liệu trước khi import.'
            : 'Nhập nhiều bài học vào một chương bằng file Excel mẫu.';
    }

    function resetPreviewState({ keepSection = false } = {}) {
        const selectedSection = keepSection ? elements.section.value : '';

        state.preview = null;
        state.batchToken = null;
        state.activeFilter = 'all';
        state.file = null;
        state.selectedSection = selectedSection;
        elements.file.value = '';
        elements.section.value = selectedSection;
        elements.rows.replaceChildren();
        elements.empty.classList.add('hidden');
        elements.tableWrap.classList.remove('hidden');
        elements.errorGuidance.classList.add('hidden');
        elements.filterSummary.textContent = '';
        clearFileError();
        updateFilename();
        setStep('select');

        filterButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.lessonImportFilter === 'all'));
        });

        updateSubmitAvailability();
    }

    function cancelRequest() {
        state.requestController?.abort();
        state.requestController = null;
        state.isLoading = false;
        updateSubmitAvailability();
    }

    function openModal(trigger) {
        cancelRequest();
        resetPreviewState();
        state.isOpen = true;
        state.returnFocus = trigger;
        root.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(() => {
            if (elements.section.disabled) {
                elements.heading.focus();
            } else {
                elements.section.focus();
            }
        });
    }

    function closeModal() {
        if (!state.isOpen) return;

        cancelRequest();
        state.isOpen = false;
        root.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        resetPreviewState();

        const returnFocus = state.returnFocus;
        state.returnFocus = null;
        returnFocus?.focus();
    }

    function createStatusBadge(status) {
        const presentation = STATUS_PRESENTATION[status] || {
            label: 'Không xác định',
            symbol: '?',
            badgeClass: 'border-slate-200 bg-slate-50 text-slate-700',
        };
        const badge = createElement(
            'span',
            `inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold ${presentation.badgeClass}`,
        );
        const symbol = createElement('span', 'font-black', presentation.symbol);
        symbol.setAttribute('aria-hidden', 'true');
        badge.append(symbol, document.createTextNode(presentation.label));

        return badge;
    }

    function createIssueList(title, messages, tone) {
        const section = createElement('div');
        const titleClass = tone === 'error' ? 'text-rose-900' : 'text-amber-900';
        const listClass = tone === 'error' ? 'text-rose-800' : 'text-amber-800';
        section.append(createElement('h4', `text-xs font-bold ${titleClass}`, title));

        const list = createElement('ul', `mt-1 space-y-1 text-xs leading-5 ${listClass}`);
        messages.forEach((message) => {
            const item = createElement('li', 'flex items-start gap-2');
            const marker = createElement('span', 'mt-px shrink-0 font-black', tone === 'error' ? '×' : '!');
            marker.setAttribute('aria-hidden', 'true');
            item.append(marker, createElement('span', 'min-w-0 break-words', message));
            list.append(item);
        });
        section.append(list);

        return section;
    }

    function createDetailRow(row, detailId) {
        const detailRow = createElement('tr', 'hidden bg-slate-50');
        detailRow.id = detailId;
        const cell = createElement('td', 'px-4 py-4');
        cell.colSpan = 6;
        const content = createElement('div', 'grid gap-4 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-2');
        const data = row.data && typeof row.data === 'object' ? row.data : {};
        const overview = createElement('div', 'space-y-1 text-xs text-slate-600');
        overview.append(
            createElement('p', 'font-bold text-slate-900', `Dòng ${row.row_number ?? data.row_number ?? '—'}`),
            createElement('p', '', `Tên: ${data.title || 'Chưa có'}`),
            createElement('p', '', `Loại: ${TYPE_LABELS[data.type] || data.type || 'Không xác định'}`),
            createElement('p', '', data.content ? 'Nội dung: Đã có nội dung' : 'Nội dung: Chưa có nội dung'),
        );

        if (data.type === 'assignment') {
            overview.append(
                createElement('p', '', `Hạn nộp: ${data.assignment_due_days ?? 'Không đặt'} ngày`),
                createElement('p', '', `Điểm tối đa: ${data.assignment_max_score ?? '—'}`),
                createElement('p', '', `Điểm đạt: ${data.assignment_passing_score ?? '—'}`),
            );
        }

        const issues = createElement('div', 'space-y-3');
        const errors = Array.isArray(row.errors) ? row.errors : [];
        const warnings = Array.isArray(row.warnings) ? row.warnings : [];

        if (errors.length) issues.append(createIssueList('Lỗi cần sửa', errors, 'error'));
        if (warnings.length) issues.append(createIssueList('Cảnh báo', warnings, 'warning'));
        if (!errors.length && !warnings.length) {
            issues.append(createElement('p', 'text-xs font-semibold text-emerald-700', 'Dòng dữ liệu hợp lệ.'));
        }

        content.append(overview, issues);
        cell.append(content);
        detailRow.append(cell);

        return detailRow;
    }

    function createPreviewRows(rows) {
        const fragment = document.createDocumentFragment();

        rows.forEach((row, index) => {
            const status = typeof row.status === 'string' ? row.status : 'valid';
            const presentation = STATUS_PRESENTATION[status] || STATUS_PRESENTATION.valid;
            const data = row.data && typeof row.data === 'object' ? row.data : {};
            const mainRow = createElement('tr', presentation.rowClass);
            const detailId = `lesson-import-row-detail-${index}`;
            const errors = Array.isArray(row.errors) ? row.errors : [];
            const warnings = Array.isArray(row.warnings) ? row.warnings : [];
            const hasDetails = errors.length > 0 || warnings.length > 0 || data.type === 'assignment';

            mainRow.dataset.lessonImportRowStatus = status;
            mainRow.append(
                createElement('td', 'whitespace-nowrap px-3 py-3 font-semibold text-slate-700', row.row_number ?? data.row_number ?? '—'),
                createElement('td', 'max-w-36 truncate px-3 py-3 font-mono text-xs font-bold text-slate-800', data.lesson_code || '—'),
            );

            const titleCell = createElement('td', 'px-3 py-3');
            const title = createElement('p', 'max-w-[24rem] truncate font-semibold text-slate-900', data.title || 'Chưa có tiêu đề');
            title.title = data.title || '';
            titleCell.append(title);
            mainRow.append(titleCell);
            mainRow.append(
                createElement('td', 'whitespace-nowrap px-3 py-3 text-slate-700', TYPE_LABELS[data.type] || data.type || 'Không xác định'),
                createElement('td', 'whitespace-nowrap px-3 py-3 tabular-nums text-slate-700', `${data.duration_seconds ?? data.duration ?? 0} giây`),
            );

            const statusCell = createElement('td', 'px-3 py-3');
            const statusWrap = createElement('div', 'flex flex-wrap items-center gap-2');
            statusWrap.append(createStatusBadge(status));

            if (hasDetails) {
                const toggle = createElement('button', 'cursor-pointer text-xs font-bold text-slate-600 underline decoration-slate-300 underline-offset-4 hover:text-slate-950', 'Chi tiết');
                toggle.type = 'button';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-controls', detailId);
                toggle.addEventListener('click', () => {
                    const detail = root.querySelector(`#${detailId}`);
                    const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
                    toggle.setAttribute('aria-expanded', String(willOpen));
                    toggle.textContent = willOpen ? 'Thu gọn' : 'Chi tiết';
                    detail?.classList.toggle('hidden', !willOpen);
                });
                statusWrap.append(toggle);
            }

            statusCell.append(statusWrap);
            mainRow.append(statusCell);
            fragment.append(mainRow);

            if (hasDetails) {
                const detailRow = createDetailRow(row, detailId);
                detailRow.dataset.lessonImportRowStatus = status;
                fragment.append(detailRow);
            }
        });

        return fragment;
    }

    function updateFilter() {
        const rows = Array.isArray(state.preview?.rows) ? state.preview.rows : [];
        const visibleCount = state.activeFilter === 'all'
            ? rows.length
            : rows.filter((row) => row.status === state.activeFilter).length;

        root.querySelectorAll('[data-lesson-import-row-status]').forEach((row) => {
            row.classList.toggle(
                'hidden',
                state.activeFilter !== 'all' && row.dataset.lessonImportRowStatus !== state.activeFilter,
            );
        });

        filterButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.lessonImportFilter === state.activeFilter));
        });

        elements.filterSummary.textContent = `Hiển thị ${visibleCount} dòng`;
    }

    function renderPreview(payload) {
        const rows = Array.isArray(payload.rows) ? payload.rows : [];
        const batch = payload.batch && typeof payload.batch === 'object' ? payload.batch : {};

        state.preview = { batch, rows };
        state.batchToken = typeof batch.token === 'string' ? batch.token : null;
        state.activeFilter = 'all';

        ['row_count', 'valid_count', 'warning_count', 'error_count'].forEach((key) => {
            const value = Number.isFinite(Number(batch[key])) ? Number(batch[key]) : 0;
            root.querySelector(`[data-lesson-import-count="${key}"]`).textContent = String(value);
        });

        const filterCounts = {
            all: batch.row_count ?? 0,
            valid: batch.valid_count ?? 0,
            warning: batch.warning_count ?? 0,
            error: batch.error_count ?? 0,
        };
        Object.entries(filterCounts).forEach(([filter, count]) => {
            const counter = root.querySelector(`[data-lesson-import-filter-count="${filter}"]`);
            if (counter) counter.textContent = String(count);
        });

        elements.rows.replaceChildren(createPreviewRows(rows));
        const isEmpty = rows.length === 0;
        elements.empty.classList.toggle('hidden', !isEmpty);
        elements.tableWrap.classList.toggle('hidden', isEmpty);
        elements.errorGuidance.classList.toggle('hidden', Number(batch.error_count || 0) <= 0);
        setStep('preview');
        updateFilter();
        setLiveMessage(`Đã kiểm tra ${batch.row_count ?? rows.length} dòng dữ liệu.`);
        elements.heading.focus();

        root.dispatchEvent(new CustomEvent('lesson-import:previewed', {
            detail: {
                batchToken: state.batchToken,
                selectedSection: state.selectedSection,
                counts: filterCounts,
            },
        }));
    }

    async function submitPreview() {
        if (state.isLoading || !state.file || !state.selectedSection) return;

        const previewUrl = selectedPreviewUrl();
        if (!previewUrl) {
            showFileError('Chương đã chọn không hợp lệ. Vui lòng chọn lại chương đích.');
            return;
        }

        clearFileError();
        state.isLoading = true;
        state.requestController = new AbortController();
        const requestController = state.requestController;
        updateSubmitAvailability();

        const formData = new FormData();
        formData.append('file', state.file, state.file.name);

        try {
            const response = await fetch(previewUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: formData,
                signal: requestController.signal,
            });
            const payload = await readJsonSafely(response);

            if (!response.ok || !payload || payload.success !== true) {
                const message = firstValidationMessage(payload)
                    || (typeof payload?.message === 'string' ? payload.message : null)
                    || 'Không thể kiểm tra file Excel. Vui lòng thử lại.';
                showFileError(message);

                if (response.status >= 500 && window.AppToast?.show) {
                    window.AppToast.show({ type: 'error', message: 'Không thể kiểm tra file Excel. Vui lòng thử lại.' });
                }
                return;
            }

            if (!payload.batch || !Array.isArray(payload.rows)) {
                showFileError('Phản hồi kiểm tra file không hợp lệ. Vui lòng thử lại.');
                return;
            }

            renderPreview(payload);
        } catch (error) {
            if (error.name === 'AbortError') return;

            const message = 'Không thể kết nối tới máy chủ. Vui lòng thử lại.';
            showFileError(message);
            window.AppToast?.show?.({ type: 'error', message });
        } finally {
            if (state.requestController === requestController) {
                state.requestController = null;
                state.isLoading = false;
                updateSubmitAvailability();
            }
        }
    }

    function trapFocus(event) {
        if (!state.isOpen || event.key !== 'Tab') return;

        const focusable = [...root.querySelectorAll(FOCUSABLE_SELECTOR)]
            .filter((element) => !element.closest('.hidden') && element.getClientRects().length > 0);

        if (!focusable.length) {
            event.preventDefault();
            elements.heading.focus();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    triggers.forEach((trigger) => trigger.addEventListener('click', () => openModal(trigger)));
    closeButtons.forEach((button) => button.addEventListener('click', closeModal));
    root.querySelector('[data-lesson-import-backdrop]')?.addEventListener('click', closeModal);
    root.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        trapFocus(event);
    });

    elements.section.addEventListener('change', () => {
        state.selectedSection = elements.section.value;
        state.preview = null;
        state.batchToken = null;
        clearFileError();
        updateSubmitAvailability();
    });
    elements.file.addEventListener('change', () => {
        state.file = elements.file.files?.[0] || null;
        state.preview = null;
        state.batchToken = null;
        clearFileError();
        updateFilename();
        updateSubmitAvailability();
    });
    elements.submit.addEventListener('click', submitPreview);
    chooseAnotherButton?.addEventListener('click', () => {
        resetPreviewState({ keepSection: true });
        elements.file.focus();
    });
    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.activeFilter = button.dataset.lessonImportFilter || 'all';
            updateFilter();
        });
    });

    updateSubmitAvailability();
}

function initializeLessonImports() {
    document.querySelectorAll('[data-lesson-import]').forEach(initializeLessonImport);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLessonImports, { once: true });
} else {
    initializeLessonImports();
}
