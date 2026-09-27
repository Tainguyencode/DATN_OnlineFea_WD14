const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const blade = fs.readFileSync('resources/views/student/cart/pay.blade.php', 'utf8');
const method = blade.slice(blade.indexOf('async checkStatus()'), blade.indexOf('async applyCoupon('))
    .replace(/\{\{ route\('login'\) \}\}/g, '/login')
    .replace(/\{\{ route\('student.orders.show', \$order\) \}\}/g, '/student/orders/1')
    .replace(/\{\{ route\('student.orders'\) \}\}/g, '/student/orders');

function controller(fetch) {
    const state = {stops: 0};
    const window = {location: {href: ''}};
    const instance = vm.runInNewContext('({' + method + '})', {
        fetch, clearInterval: () => state.stops++, window, console: {error() {}},
    });
    instance.orderCode = 'ORDER';
    return {instance, state, window};
}

test('terminal states stop polling and navigate to the correct result', async () => {
    for (const [status, target] of Object.entries({paid: '/student/checkout/ORDER/success', cancelled: '/student/checkout/ORDER/failed', failed: '/student/checkout/ORDER/failed', refunded: '/student/orders/1', not_found: '/student/orders'})) {
        const {instance, state, window} = controller(async () => ({ok: status !== 'not_found', status: status === 'not_found' ? 404 : 200, json: async () => ({status})}));
        await instance.checkStatus();
        assert.equal(window.location.href, target);
        assert.equal(state.stops, 1);
        assert.equal(instance.checkingStatus, false);
    }
});

test('pending and network errors remain retryable without overlapping requests', async () => {
    let resolve, calls = 0;
    const {instance, state} = controller(() => { calls++; return new Promise(r => {resolve = r;}); });
    const first = instance.checkStatus();
    await instance.checkStatus();
    assert.equal(calls, 1);
    resolve({ok: true, status: 200, json: async () => ({status: 'pending'})});
    await first;
    assert.equal(instance.checkingStatus, false);
    assert.equal(state.stops, 0);
    const failing = controller(async () => {throw new Error('Offline');});
    await failing.instance.checkStatus();
    assert.equal(failing.instance.checkingStatus, false);
});

test('expired sessions navigate to login', async () => {
    const {instance, window} = controller(async () => ({status: 401}));
    await instance.checkStatus();
    assert.equal(window.location.href, '/login');
});
