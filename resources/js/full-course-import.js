function text(value) {
    return value === null || value === undefined ? '' : String(value);
}

function element(tag, className, value) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (value !== undefined) node.textContent = text(value);
    return node;
}

function renderTable(panel, rows, columns) {
    const table = element('table', 'min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-700');
    const head = document.createElement('thead');
    head.className = 'bg-slate-50 text-xs uppercase tracking-wide text-slate-600 dark:bg-slate-800 dark:text-slate-300';
    const headerRow = document.createElement('tr');
    columns.forEach(([label]) => headerRow.append(element('th', 'px-3 py-2 font-semibold', label)));
    head.append(headerRow); table.append(head);
    const body = document.createElement('tbody');
    body.className = 'divide-y divide-slate-100 text-slate-700 dark:divide-slate-800 dark:text-slate-200';
    rows.forEach((row) => {
        const tr = document.createElement('tr');
        columns.forEach(([, key]) => tr.append(element('td', 'px-3 py-2 align-top', typeof key === 'function' ? key(row) : row[key])));
        body.append(tr);
    });
    table.append(body); panel.replaceChildren(table);
}

function initialise(root) {
    if (root.dataset.fullCourseImportInitialised === 'true') return;
    root.dataset.fullCourseImportInitialised = 'true';
    const form = root.querySelector('[data-full-course-import-form]');
    if (!form) return;
    const file = form.querySelector('input[type="file"]');
    const filename = root.querySelector('[data-full-course-file-name]');
    const preview = root.querySelector('[data-full-course-preview]');
    const live = root.querySelector('[data-full-course-live]');
    const tabs = root.querySelector('[data-full-course-tabs]');
    const panel = root.querySelector('[data-full-course-panel]');
    const issues = root.querySelector('[data-full-course-issues]');
    let response = null;

    file?.addEventListener('change', () => {
        const selected = file.files?.[0];
        filename.classList.toggle('hidden', !selected);
        filename.textContent = selected ? `${selected.name} (${Math.ceil(selected.size / 1024)} KB)` : '';
    });

    const views = {
        'Khóa học': () => renderTable(panel, [response.course], [['Tiêu đề', 'title'], ['Danh mục', 'category_slug'], ['Trình độ', 'level'], ['Ngôn ngữ', 'language'], ['Giá', 'price'], ['Giá giảm', 'sale_price']]),
        'Chương': () => renderTable(panel, response.sections, [['Mã chương', 'section_code'], ['Tên', 'title'], ['Số bài', (section) => response.lessons.filter((lesson) => lesson.section_code === section.section_code).length]]),
        'Bài học': () => renderTable(panel, response.lessons, [['Chương', 'section_code'], ['Mã', 'lesson_code'], ['Tên', 'title'], ['Loại', 'type'], ['Thời lượng', 'duration_seconds'], ['Trạng thái', 'status']]),
        Quiz: () => renderTable(panel, response.quizzes, [['Tên Quiz', 'title'], ['Mã bài', 'lesson_code'], ['Số câu', (quiz) => response.questions.filter((question) => question.lesson_code === quiz.lesson_code).length], ['Điểm đạt', 'pass_score'], ['Thời gian', 'time_limit_minutes']]),
        'Câu hỏi': () => renderTable(panel, response.questions, [['Mã', 'question_code'], ['Bài quiz', 'lesson_code'], ['Nội dung', 'question'], ['Loại', 'type'], ['Điểm', 'points']]),
        'Đáp án': () => renderTable(panel, response.options, [['Câu hỏi', 'question_code'], ['Mã', 'option_code'], ['Nội dung', 'option_text'], ['Đúng', (option) => option.is_correct ? 'TRUE' : 'FALSE']]),
        'Lỗi & Cảnh báo': () => renderTable(panel, response.issues, [['Mức độ', 'severity'], ['Sheet', 'sheet'], ['Dòng', 'row_number'], ['Nội dung', 'message']]),
    };

    const show = (name) => {
        tabs.querySelectorAll('button').forEach((button) => {
            const active = button.dataset.fullCourseTab === name;
            button.className = `mr-1 border-b-2 px-3 py-2 text-sm font-semibold ${active ? 'border-indigo-600 text-indigo-700 dark:text-indigo-300' : 'border-transparent text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'}`;
            button.setAttribute('aria-selected', String(active));
        });
        views[name]();
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('button[type="submit"]');
        submit.disabled = true; submit.textContent = 'Đang kiểm tra…';
        try {
            const result = await fetch(form.dataset.previewUrl, { method: 'POST', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value }, body: new FormData(form) });
            const data = await result.json();
            if (!result.ok || !data.success) throw new Error(data.message || 'Không thể xem trước file.');
            response = data; preview.classList.remove('hidden');
            root.querySelector('[data-full-course-title]').textContent = data.course.title || 'Khóa học chưa có tiêu đề';
            root.querySelector('[data-full-course-meta]').textContent = `${data.course.category_slug || '—'} · ${data.course.level || '—'} · ${data.course.language || '—'}`;
            root.querySelector('[data-full-course-confirm-state]').textContent = data.batch.can_confirm ? 'Dữ liệu hợp lệ, sẵn sàng cho Phase 4B.' : 'Cần xử lý lỗi trước khi có thể xác nhận.';
            const summary = root.querySelector('[data-full-course-summary]'); summary.replaceChildren();
            Object.entries(data.summary).forEach(([key, value]) => { const card = element('div', 'rounded-lg bg-slate-50 p-3 dark:bg-slate-800'); card.append(element('dt', 'text-xs font-medium text-slate-500 dark:text-slate-400', key), element('dd', 'mt-1 text-lg font-bold text-slate-900 dark:text-white', value)); summary.append(card); });
            tabs.replaceChildren(); Object.keys(views).forEach((name) => { const button = element('button', '', name); button.type = 'button'; button.dataset.fullCourseTab = name; button.setAttribute('role', 'tab'); button.addEventListener('click', () => show(name)); tabs.append(button); });
            issues.replaceChildren();
            const warning = data.summary.warnings ? element('p', 'rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200', `${data.summary.warnings} cảnh báo: video chưa có nguồn video vẫn được phép xem trước.`) : null;
            if (warning) issues.append(warning);
            show('Khóa học'); live.textContent = 'Đã tạo bản xem trước workbook.';
        } catch (error) {
            window.AppToast?.show({ type: 'error', message: error.message }); live.textContent = error.message;
        } finally { submit.disabled = false; submit.textContent = 'Xem trước'; }
    });
}

function initialiseAll() { document.querySelectorAll('[data-full-course-import]').forEach(initialise); }
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialiseAll, { once: true }); else initialiseAll();
