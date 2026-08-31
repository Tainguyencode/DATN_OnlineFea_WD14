// Do not preserve one-time server notifications in a back/forward cache snapshot.
function clearFlashToasts() {
    document.querySelectorAll('[data-flash-message]').forEach((toast) => toast.remove());
}

window.addEventListener('pagehide', clearFlashToasts);
window.addEventListener('pageshow', (event) => {
    const historyNavigation = event.persisted
        || window.performance?.getEntriesByType('navigation')[0]?.type === 'back_forward';
    if (!historyNavigation) return;

    clearFlashToasts();
    // Only refresh pages explicitly marked as needing current server state.
    // A reload has navigation type "reload", so this does not loop.
    if (document.querySelector('[data-refresh-on-history]')) {
        window.location.reload();
    }
});
