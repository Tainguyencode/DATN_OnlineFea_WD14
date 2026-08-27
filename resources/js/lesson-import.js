import { renderMath } from './math-renderer';

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

const V2_SHEET_NAMES = ['Lessons', 'Quizzes', 'QuizQuestions', 'QuizOptions'];

const V2_SHEET_LABELS = {
    Lessons: 'Bài học',
    Quizzes: 'Quiz',
    QuizQuestions: 'Câu hỏi',
    QuizOptions: 'Đáp án',
};

const V2_SHEET_COLUMN_COUNTS = {
    Lessons: 6,
    Quizzes: 8,
    QuizQuestions: 7,
    QuizOptions: 6,
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
        fileTrigger: root.querySelector('[data-lesson-import-file-trigger]'),
        fileTriggerLabel: root.querySelector('[data-lesson-import-file-trigger-label]'),
        filename: root.querySelector('[data-lesson-import-filename]'),
        submit: root.querySelector('[data-lesson-import-submit]'),
        submitLabel: root.querySelector('[data-lesson-import-submit-label]'),
        error: root.querySelector('[data-lesson-import-error]'),
        errorMessage: root.querySelector('[data-lesson-import-error-message]'),
        errorGuidance: root.querySelector('[data-lesson-import-error-guidance]'),
        confirmError: root.querySelector('[data-lesson-import-confirm-error]'),
        confirmErrorMessage: root.querySelector('[data-lesson-import-confirm-error-message]'),
        confirm: root.querySelector('[data-lesson-import-confirm]'),
        confirmLabel: root.querySelector('[data-lesson-import-confirm-label]'),
        v1Preview: root.querySelector('[data-lesson-import-v1-preview]'),
        v2Preview: root.querySelector('[data-lesson-import-v2-preview]'),
        v2ErrorGuidance: root.querySelector('[data-lesson-import-v2-error-guidance]'),
        v2Issues: root.querySelector('[data-lesson-import-v2-issues]'),
        v2IssuesWrap: root.querySelector('[data-lesson-import-v2-issues-wrap]'),
        v2IssuesEmpty: root.querySelector('[data-lesson-import-v2-issues-empty]'),
        rows: root.querySelector('[data-lesson-import-rows]'),
        tableWrap: root.querySelector('[data-lesson-import-table-wrap]'),
        empty: root.querySelector('[data-lesson-import-empty]'),
        filterSummary: root.querySelector('[data-lesson-import-filter-summary]'),
        live: root.querySelector('[data-lesson-import-live]'),
    };

    if (!elements.panel || !elements.section || !elements.file || !elements.fileTrigger || !elements.submit || !elements.confirm || !elements.rows) return;

    const triggers = document.querySelectorAll(`[data-lesson-import-open][aria-controls="${root.id}"]`);
    const filterButtons = [...root.querySelectorAll('[data-lesson-import-filter]')];
    const v2TabButtons = [...root.querySelectorAll('[data-lesson-import-v2-tab]')];
    const v2SheetPanels = [...root.querySelectorAll('[data-lesson-import-v2-sheet-panel]')];
    const closeButtons = [...root.querySelectorAll('[data-lesson-import-close]')];
    const chooseAnotherButton = root.querySelector('[data-lesson-import-choose-another]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const state = {
        isOpen: false,
        isLoading: false,
        isImporting: false,
        confirmUnavailable: false,
        selectedSection: '',
        file: null,
        preview: null,
        batchToken: null,
        activeFilter: 'all',
        activeV2Tab: 'Lessons',
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

    function showConfirmError(message, unavailable = false) {
        elements.confirmErrorMessage.textContent = message;
        elements.confirmError.classList.remove('hidden');
        state.confirmUnavailable = unavailable;
        updateConfirmAvailability();
    }

    function clearConfirmError() {
        elements.confirmErrorMessage.textContent = '';
        elements.confirmError.classList.add('hidden');
        state.confirmUnavailable = false;
    }

    function selectedPreviewUrl() {
        return elements.section.selectedOptions[0]?.dataset.previewUrl || '';
    }

    function selectedConfirmUrl() {
        return elements.section.selectedOptions[0]?.dataset.confirmUrl || '';
    }

    function isRecord(value) {
        return value !== null && typeof value === 'object' && !Array.isArray(value);
    }

    function numericValue(value, fallback = 0) {
        const number = Number(value);

        return Number.isFinite(number) ? number : fallback;
    }

    function isV2PreviewPayload(payload) {
        return isRecord(payload)
            && isRecord(payload.summary)
            && isRecord(payload.sheets)
            && Array.isArray(payload.issues);
    }

    function previewImportStats() {
        if (state.preview?.kind === 'v2') {
            const summary = state.preview.summary;
            const errorCount = Number(summary?.errors);

            return {
                importCount: numericValue(summary?.lessons),
                errorCount: Number.isFinite(errorCount) ? errorCount : 1,
            };
        }

        return {
            importCount: numericValue(state.preview?.batch?.row_count),
            errorCount: numericValue(state.preview?.batch?.error_count),
        };
    }

    function updateSubmitAvailability() {
        const isBusy = state.isLoading || state.isImporting;
        elements.submit.disabled = isBusy || !state.selectedSection || !state.file;
        elements.section.disabled = isBusy || elements.section.options.length <= 1;
        elements.file.disabled = isBusy || elements.section.options.length <= 1;
        elements.fileTrigger.disabled = isBusy || elements.section.options.length <= 1;
        elements.submit.setAttribute('aria-busy', String(state.isLoading));
        elements.submitLabel.textContent = state.isLoading ? 'Đang kiểm tra...' : 'Kiểm tra file';
    }

    function updateConfirmAvailability() {
        const { importCount, errorCount } = previewImportStats();
        const canImport = Boolean(state.batchToken)
            && importCount > 0
            && errorCount === 0
            && !state.confirmUnavailable;

        elements.confirm.disabled = state.isImporting || !canImport;
        elements.confirm.setAttribute('aria-busy', String(state.isImporting));
        elements.confirmLabel.textContent = state.isImporting
            ? 'Đang import...'
            : (canImport ? `Import ${importCount} bài học` : 'Chưa thể import');
        elements.panel.setAttribute('aria-busy', String(state.isImporting));

        if (chooseAnotherButton) chooseAnotherButton.disabled = state.isImporting;
        closeButtons.forEach((button) => {
            button.disabled = state.isImporting;
        });
    }

    function updateFilename() {
        if (!state.file) {
            elements.fileTriggerLabel.textContent = 'Chọn file Excel';
            elements.filename.textContent = 'Chưa chọn file';
            return;
        }

        elements.fileTriggerLabel.textContent = 'Đổi file';
        elements.filename.textContent = state.file.name;
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

    function resetPreviewState({ selectedSection = '' } = {}) {
        state.isLoading = false;
        state.isImporting = false;
        state.confirmUnavailable = false;
        state.preview = null;
        state.batchToken = null;
        state.activeFilter = 'all';
        state.activeV2Tab = 'Lessons';
        state.file = null;
        state.selectedSection = selectedSection;
        elements.file.value = '';
        elements.section.value = selectedSection;
        elements.rows.replaceChildren();
        elements.v1Preview?.classList.remove('hidden');
        elements.v2Preview?.classList.add('hidden');
        clearV2Preview();
        elements.empty.classList.add('hidden');
        elements.tableWrap.classList.remove('hidden');
        elements.errorGuidance.classList.add('hidden');
        elements.filterSummary.textContent = '';
        clearFileError();
        clearConfirmError();
        updateFilename();
        setStep('select');

        filterButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.lessonImportFilter === 'all'));
        });

        updateSubmitAvailability();
        updateConfirmAvailability();
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
        if (!state.isOpen || state.isImporting) return;

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

    function renderV1Preview(payload) {
        const rows = Array.isArray(payload.rows) ? payload.rows : [];
        const batch = payload.batch && typeof payload.batch === 'object' ? payload.batch : {};

        state.preview = { kind: 'v1', batch, rows };
        state.batchToken = typeof batch.token === 'string' ? batch.token : null;
        state.activeFilter = 'all';
        clearV2Preview();
        elements.v1Preview?.classList.remove('hidden');
        elements.v2Preview?.classList.add('hidden');

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
        clearConfirmError();
        setStep('preview');
        updateFilter();
        updateConfirmAvailability();
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

    function v2SummaryCounts(summary) {
        return {
            lessons: numericValue(summary?.lessons),
            quizzes: numericValue(summary?.quizzes),
            questions: numericValue(summary?.questions),
            options: numericValue(summary?.options),
            errors: numericValue(summary?.errors),
            warnings: numericValue(summary?.warnings),
        };
    }

    function v2SheetRows(sheets, sheetName) {
        if (!isRecord(sheets)) return [];

        const exact = sheets[sheetName];
        const key = Object.keys(sheets).find((candidate) => candidate.toLowerCase() === sheetName.toLowerCase());
        const value = exact ?? (key ? sheets[key] : null);

        if (Array.isArray(value)) return value;
        if (isRecord(value) && Array.isArray(value.rows)) return value.rows;

        return [];
    }

    function v2RowData(row) {
        return isRecord(row?.data) ? row.data : (isRecord(row) ? row : {});
    }

    function v2Field(row, data, field) {
        if (isRecord(row) && Object.prototype.hasOwnProperty.call(row, field)) return row[field];

        return data[field];
    }

    function displayValue(value, fallback = '—') {
        if (value === null || value === undefined || value === '') return fallback;
        if (typeof value === 'boolean') return value ? 'TRUE' : 'FALSE';
        if (typeof value === 'string' || typeof value === 'number') return String(value);

        return fallback;
    }

    function booleanValue(value) {
        if (value === true || value === 1 || String(value).trim().toUpperCase() === 'TRUE') return 'TRUE';
        if (value === false || value === 0 || String(value).trim().toUpperCase() === 'FALSE') return 'FALSE';

        return displayValue(value);
    }

    function valueWithUnit(value, unit) {
        const text = displayValue(value);

        return text === '—' ? text : `${text} ${unit}`;
    }

    function v2IssueKey(sheet, rowNumber) {
        return `${String(sheet).toLowerCase()}::${String(rowNumber)}`;
    }

    function indexV2Issues(issues) {
        const index = new Map();

        issues.forEach((issue) => {
            if (!isRecord(issue) || issue.sheet === null || issue.sheet === undefined
                || issue.row_number === null || issue.row_number === undefined) {
                return;
            }

            const key = v2IssueKey(issue.sheet, issue.row_number);
            const current = index.get(key) || [];
            current.push(issue);
            index.set(key, current);
        });

        return index;
    }

    function v2RowStatus(row, data, sheetName, issuesByRow) {
        const explicitStatus = v2Field(row, data, 'status');
        if (typeof explicitStatus === 'string' && Object.prototype.hasOwnProperty.call(STATUS_PRESENTATION, explicitStatus)) {
            return explicitStatus;
        }

        const rowNumber = v2Field(row, data, 'row_number');
        const issues = issuesByRow.get(v2IssueKey(sheetName, rowNumber)) || [];

        if (issues.some((issue) => String(issue.severity).toLowerCase() === 'error')) return 'error';
        if (issues.some((issue) => String(issue.severity).toLowerCase() === 'warning')) return 'warning';

        return 'valid';
    }

    function v2Cells(sheetName, row, data) {
        const field = (name) => v2Field(row, data, name);

        switch (sheetName) {
            case 'Lessons':
                return [
                    displayValue(field('row_number')),
                    displayValue(field('lesson_code')),
                    displayValue(field('title')),
                    TYPE_LABELS[field('type')] || displayValue(field('type')),
                    valueWithUnit(field('duration_seconds') ?? field('duration'), 'giây'),
                ];
            case 'Quizzes':
                return [
                    displayValue(field('row_number')),
                    displayValue(field('lesson_code')),
                    displayValue(field('title')),
                    valueWithUnit(field('pass_score'), '%'),
                    valueWithUnit(field('time_limit_minutes'), 'phút'),
                    displayValue(field('max_attempts')),
                    booleanValue(field('is_active')),
                ];
            case 'QuizQuestions':
                return [
                    displayValue(field('row_number')),
                    displayValue(field('lesson_code')),
                    displayValue(field('question_code')),
                    displayValue(field('question')),
                    displayValue(field('type')),
                    displayValue(field('points')),
                ];
            case 'QuizOptions':
                return [
                    displayValue(field('row_number')),
                    displayValue(field('question_code')),
                    displayValue(field('option_code')),
                    displayValue(field('option_text')),
                    booleanValue(field('is_correct')),
                ];
            default:
                return [];
        }
    }

    function v2CellClass(sheetName, index) {
        const longTextIndexes = {
            Lessons: [2],
            Quizzes: [2],
            QuizQuestions: [3],
            QuizOptions: [3],
        };
        const codeIndexes = {
            Lessons: [1],
            Quizzes: [1],
            QuizQuestions: [1, 2],
            QuizOptions: [1, 2],
        };

        if (longTextIndexes[sheetName]?.includes(index)) {
            return 'max-w-[32rem] break-words px-3 py-3 font-semibold text-slate-900';
        }

        if (codeIndexes[sheetName]?.includes(index)) {
            return 'whitespace-nowrap px-3 py-3 font-mono text-xs font-bold text-slate-800';
        }

        return 'whitespace-nowrap px-3 py-3 text-slate-700';
    }

    function createV2EntityRows(sheetName, rows, issuesByRow) {
        const fragment = document.createDocumentFragment();

        if (rows.length === 0) {
            const emptyRow = createElement('tr');
            const emptyCell = createElement(
                'td',
                'px-4 py-8 text-center text-sm font-semibold text-slate-500',
                'Sheet này chưa có dữ liệu.',
            );
            emptyCell.colSpan = V2_SHEET_COLUMN_COUNTS[sheetName];
            emptyRow.append(emptyCell);
            fragment.append(emptyRow);

            return fragment;
        }

        rows.forEach((row) => {
            const safeRow = isRecord(row) ? row : {};
            const data = v2RowData(safeRow);
            const status = v2RowStatus(safeRow, data, sheetName, issuesByRow);
            const presentation = STATUS_PRESENTATION[status] || STATUS_PRESENTATION.valid;
            const mainRow = createElement('tr', presentation.rowClass);

            v2Cells(sheetName, safeRow, data).forEach((value, index) => {
                const cell = createElement('td', v2CellClass(sheetName, index), value);

                if ((sheetName === 'QuizQuestions' || sheetName === 'QuizOptions') && index === 3) {
                    cell.dataset.mathContent = '';
                }

                mainRow.append(cell);
            });

            const statusCell = createElement('td', 'px-3 py-3');
            statusCell.append(createStatusBadge(status));
            mainRow.append(statusCell);
            fragment.append(mainRow);
        });

        return fragment;
    }

    function renderV2Issues(issues) {
        if (!elements.v2Issues || !elements.v2IssuesWrap || !elements.v2IssuesEmpty) return;

        const structuredIssues = issues.filter(isRecord);
        const fragment = document.createDocumentFragment();

        structuredIssues.forEach((issue) => {
            const severity = String(issue.severity || '').toLowerCase() === 'error' ? 'error' : 'warning';
            const row = createElement('tr', STATUS_PRESENTATION[severity].rowClass);
            const severityCell = createElement('td', 'px-3 py-3');
            severityCell.append(createStatusBadge(severity));
            row.append(
                severityCell,
                createElement('td', 'px-3 py-3 font-semibold text-slate-700', V2_SHEET_LABELS[issue.sheet] || displayValue(issue.sheet)),
                createElement('td', 'whitespace-nowrap px-3 py-3 tabular-nums text-slate-700', displayValue(issue.row_number)),
                createElement('td', 'px-3 py-3 font-mono text-xs font-bold text-slate-800', displayValue(issue.field)),
                createElement('td', 'max-w-[36rem] break-words px-3 py-3 text-slate-800', displayValue(issue.message)),
            );
            fragment.append(row);
        });

        elements.v2Issues.replaceChildren(fragment);
        elements.v2IssuesEmpty.classList.toggle('hidden', structuredIssues.length > 0);
        elements.v2IssuesWrap.classList.toggle('hidden', structuredIssues.length === 0);
    }

    function setV2ActiveTab(sheetName, shouldFocus = false) {
        const activeSheet = V2_SHEET_NAMES.includes(sheetName) ? sheetName : 'Lessons';
        state.activeV2Tab = activeSheet;

        v2TabButtons.forEach((button) => {
            const isActive = button.dataset.lessonImportV2Tab === activeSheet;
            button.setAttribute('aria-selected', String(isActive));
            button.tabIndex = isActive ? 0 : -1;
            button.classList.toggle('border-slate-900', isActive);
            button.classList.toggle('bg-slate-900', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('border-slate-300', !isActive);
            button.classList.toggle('bg-white', !isActive);
            button.classList.toggle('text-slate-700', !isActive);

            if (isActive && shouldFocus) button.focus();
        });

        v2SheetPanels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.lessonImportV2SheetPanel !== activeSheet);
        });
    }

    function clearV2Preview() {
        root.querySelectorAll('[data-lesson-import-v2-count]').forEach((counter) => {
            counter.textContent = '0';
        });
        root.querySelectorAll('[data-lesson-import-v2-sheet-rows]').forEach((body) => {
            body.replaceChildren();
        });
        elements.v2Issues?.replaceChildren();
        elements.v2IssuesEmpty?.classList.remove('hidden');
        elements.v2IssuesWrap?.classList.add('hidden');
        elements.v2ErrorGuidance?.classList.add('hidden');
        setV2ActiveTab('Lessons');
    }

    function renderV2Preview(payload) {
        const batch = isRecord(payload.batch) ? payload.batch : {};
        const summary = payload.summary;
        const sheets = payload.sheets;
        const issues = payload.issues.filter(isRecord);
        const counts = v2SummaryCounts(summary);
        const issuesByRow = indexV2Issues(issues);

        state.preview = { kind: 'v2', batch, summary, sheets, issues };
        state.batchToken = typeof batch.token === 'string' ? batch.token : null;
        state.activeFilter = 'all';

        Object.entries(counts).forEach(([key, value]) => {
            const counter = root.querySelector(`[data-lesson-import-v2-count="${key}"]`);
            if (counter) counter.textContent = String(value);
        });

        V2_SHEET_NAMES.forEach((sheetName) => {
            const body = root.querySelector(`[data-lesson-import-v2-sheet-rows="${sheetName}"]`);
            if (body) body.replaceChildren(createV2EntityRows(sheetName, v2SheetRows(sheets, sheetName), issuesByRow));
        });

        renderMath(root);

        renderV2Issues(issues);
        elements.v2ErrorGuidance?.classList.toggle('hidden', counts.errors <= 0);
        elements.v1Preview?.classList.add('hidden');
        elements.v2Preview?.classList.remove('hidden');
        setV2ActiveTab('Lessons');
        clearConfirmError();
        setStep('preview');
        updateConfirmAvailability();
        setLiveMessage(`Đã kiểm tra ${counts.lessons} bài học, ${counts.quizzes} quiz, ${counts.questions} câu hỏi và ${counts.options} đáp án.`);
        elements.heading.focus();

        root.dispatchEvent(new CustomEvent('lesson-import:previewed', {
            detail: {
                batchToken: state.batchToken,
                selectedSection: state.selectedSection,
                counts,
            },
        }));
    }

    function renderPreview(payload) {
        if (isV2PreviewPayload(payload)) {
            renderV2Preview(payload);

            return;
        }

        renderV1Preview(payload);
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

            if (!payload.batch || (!Array.isArray(payload.rows) && !isV2PreviewPayload(payload))) {
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

    async function submitConfirm() {
        const { importCount, errorCount } = previewImportStats();
        if (state.isImporting || !state.batchToken || importCount <= 0 || errorCount > 0 || state.confirmUnavailable) return;

        const confirmUrl = selectedConfirmUrl();
        if (!confirmUrl) {
            showConfirmError('Chương đã chọn không hợp lệ. Vui lòng chọn file và kiểm tra lại.', true);
            return;
        }

        clearConfirmError();
        state.isImporting = true;
        updateSubmitAvailability();
        updateConfirmAvailability();
        setLiveMessage(`Đang import ${importCount} bài học. Vui lòng chờ.`);

        try {
            const response = await fetch(confirmUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ batch_token: state.batchToken }),
            });
            const payload = await readJsonSafely(response);

            if (!response.ok || !payload || payload.success !== true) {
                const message = firstValidationMessage(payload)
                    || (typeof payload?.message === 'string' ? payload.message : null)
                    || 'Không thể import bài học. Không có dữ liệu nào được thay đổi.';
                const nonRetryableCodes = new Set([
                    'batch_not_found',
                    'batch_context_mismatch',
                    'batch_expired',
                    'batch_has_errors',
                    'empty_batch',
                    'invalid_canonical_payload',
                    'canonical_row_count_mismatch',
                    'course_not_eligible',
                    'duplicate_file',
                    'invalid_batch_status',
                ]);
                showConfirmError(message, nonRetryableCodes.has(payload?.error_code));
                setLiveMessage(message);

                if (response.status >= 500) {
                    window.AppToast?.show?.({ type: 'error', message: 'Không thể import bài học. Vui lòng thử lại.' });
                }
                return;
            }

            if (typeof payload.redirect_url !== 'string' || payload.redirect_url === '') {
                showConfirmError('Bài học đã được xử lý nhưng không thể tải lại Curriculum. Vui lòng tự tải lại trang.');
                return;
            }

            window.location.assign(payload.redirect_url);
        } catch (error) {
            const message = 'Không nhận được phản hồi từ máy chủ. Hãy kiểm tra lại khóa học trước khi thử lại.';
            showConfirmError(message);
            setLiveMessage(message);
            window.AppToast?.show?.({ type: 'error', message });
        } finally {
            state.isImporting = false;
            updateSubmitAvailability();
            updateConfirmAvailability();
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
            if (state.isImporting) {
                setLiveMessage('Đang import bài học. Vui lòng chờ hoàn tất.');
                return;
            }
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
    elements.fileTrigger.addEventListener('click', () => {
        if (!elements.fileTrigger.disabled) elements.file.click();
    });
    elements.submit.addEventListener('click', submitPreview);
    elements.confirm.addEventListener('click', submitConfirm);
    chooseAnotherButton?.addEventListener('click', () => {
        if (state.isImporting) return;
        const selectedSection = state.selectedSection;
        cancelRequest();
        resetPreviewState({ selectedSection });
        elements.fileTrigger.focus();
    });
    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.activeFilter = button.dataset.lessonImportFilter || 'all';
            updateFilter();
        });
    });
    v2TabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            setV2ActiveTab(button.dataset.lessonImportV2Tab || 'Lessons');
        });
        button.addEventListener('keydown', (event) => {
            let nextIndex = index;

            if (event.key === 'ArrowRight') nextIndex = (index + 1) % v2TabButtons.length;
            else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + v2TabButtons.length) % v2TabButtons.length;
            else if (event.key === 'Home') nextIndex = 0;
            else if (event.key === 'End') nextIndex = v2TabButtons.length - 1;
            else return;

            event.preventDefault();
            setV2ActiveTab(v2TabButtons[nextIndex].dataset.lessonImportV2Tab || 'Lessons', true);
        });
    });

    updateSubmitAvailability();
    updateConfirmAvailability();
}

function initializeLessonImports() {
    document.querySelectorAll('[data-lesson-import]').forEach(initializeLessonImport);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLessonImports, { once: true });
} else {
    initializeLessonImports();
}
