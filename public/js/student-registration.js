(() => {
    function validate(data) {
        const errors = {};
        const value = name => String(data.get(name) || '').trim();
        if (!value('name')) errors.name = ['Vui lòng nhập họ và tên.'];
        else if (value('name').length > 255) errors.name = ['Họ và tên không được vượt quá 255 ký tự.'];
        if (!value('email')) errors.email = ['Vui lòng nhập địa chỉ email.'];
        else if (!/^[^\s@]+@[^\s@]+$/.test(value('email'))) errors.email = ['Địa chỉ email không đúng định dạng.'];
        if (!value('phone')) errors.phone = ['Vui lòng nhập số điện thoại.'];
        else if (!/^[0-9+\-\s().]{8,20}$/.test(value('phone'))) errors.phone = ['Số điện thoại không đúng định dạng (8–20 ký tự).'];
        const password = String(data.get('password') || '');
        if (!password) errors.password = ['Vui lòng nhập mật khẩu.'];
        else if ([...password].length < 8 || !/\p{Ll}/u.test(password) || !/\p{Lu}/u.test(password) || !/\p{N}/u.test(password) || !/[\p{Z}\p{S}\p{P}]/u.test(password)) {
            errors.password = ['Mật khẩu cần ít nhất 8 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt.'];
        }
        if (!data.get('password_confirmation')) errors.password_confirmation = ['Vui lòng xác nhận mật khẩu.'];
        else if (data.get('password_confirmation') !== password) errors.password_confirmation = ['Xác nhận mật khẩu không khớp.'];
        if (!value('captcha_answer')) errors.captcha_answer = ['Vui lòng nhập kết quả phép tính xác nhận.'];
        if (!data.get('terms')) errors.terms = ['Bạn phải đồng ý với điều khoản dành cho học viên.'];
        return errors;
    }

    function showErrors(form, errors) {
        form.querySelectorAll('[data-registration-error]').forEach(node => node.remove());
        form.querySelectorAll('[data-registration-invalid]').forEach(field => {
            field.style.borderColor = '';
            field.removeAttribute('aria-invalid');
            field.removeAttribute('aria-describedby');
            field.removeAttribute('data-registration-invalid');
        });
        let first;
        for (const [name, messages] of Object.entries(errors)) {
            const field = form.elements.namedItem(name === 'captcha_token' ? 'captcha_answer' : name);
            const message = document.createElement('p');
            message.dataset.registrationError = '';
            message.className = 'mt-1 text-xs font-semibold text-red-600 dark:text-red-400';
            message.setAttribute('role', 'alert');
            message.textContent = Array.isArray(messages) ? messages.join(' ') : messages;
            if (field && field.type !== 'hidden') {
                message.id = `registration-error-${name}`;
                field.style.borderColor = '#dc2626';
                field.setAttribute('aria-invalid', 'true');
                field.setAttribute('aria-describedby', message.id);
                field.dataset.registrationInvalid = '';
                const anchor = field.type === 'checkbox' ? field.parentElement.parentElement : field.parentElement;
                anchor.appendChild(message);
                first ||= field;
            } else form.prepend(message);
        }
        first?.focus();
    }

    window.validateStudentRegistration = validate;
    window.submitStudentRegistration = async form => {
        const data = new FormData(form);
        const errors = validate(data);
        showErrors(form, errors);
        if (Object.keys(errors).length) return;
        try {
            const response = await fetch(form.action, {
                method: 'POST', body: data, credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const result = await response.json().catch(() => ({}));
            if (response.status === 422) {
                showErrors(form, result.errors || { form: ['Thông tin đăng ký không hợp lệ.'] });
                if (result.captcha) {
                    form.elements.namedItem('captcha_token').value = result.captcha.token;
                    form.elements.namedItem('captcha_answer').value = '';
                    form.querySelector('[data-captcha-question]').textContent = result.captcha.question;
                }
                return;
            }
            if (response.ok && result.redirect) {
                window.location.assign(result.redirect);
                return;
            }
            showErrors(form, { form: [response.status === 419
                ? 'Phiên đăng ký đã hết hạn. Vui lòng tải lại trang trước khi thử lại.'
                : response.status === 429 ? 'Bạn thao tác quá nhanh. Vui lòng chờ một lát rồi thử lại.'
                : 'Chưa thể xác nhận kết quả đăng ký. Vui lòng kiểm tra trước khi thử lại.'] });
        } catch {
            showErrors(form, { form: ['Mất kết nối khi đăng ký. Dữ liệu bạn nhập vẫn được giữ lại; vui lòng kiểm tra kết nối.'] });
        }
    };
})();
