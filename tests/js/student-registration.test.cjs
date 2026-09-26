const { test } = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const path = require('node:path');
const context = { window: {} };
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../../public/js/student-registration.js'), 'utf8'), context);
const validate = context.window.validateStudentRegistration;
const valid = { name: 'Học viên', email: 'student@example.com', phone: '0912345678', password: 'Password1!', password_confirmation: 'Password1!', captcha_answer: '10', terms: '1' };
test('empty form reports all seven visible required fields together', () => {
    assert.deepEqual(Object.keys(validate(new Map())).sort(), ['name', 'email', 'phone', 'password', 'password_confirmation', 'captcha_answer', 'terms'].sort());
});
test('invalid fields and password confirmation are reported together', () => {
    const errors = validate(new Map(Object.entries({ ...valid, email: 'bad', phone: 'abc', password: 'short', password_confirmation: 'different' })));
    assert.deepEqual(Object.keys(errors).sort(), ['email', 'phone', 'password', 'password_confirmation'].sort());
});
test('valid form passes local checks before server validation', () => {
    assert.equal(Object.keys(validate(new Map(Object.entries(valid)))).length, 0);
});
