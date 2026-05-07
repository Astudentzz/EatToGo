// EatToGo – shared UI helpers (no static data, no localStorage)
function searchHome(e) {
    e.preventDefault();
    const query = document.getElementById('q')?.value || '';
    window.location.href = `search-results.html?q=${encodeURIComponent(query)}`;
}
function forgot(e) {
    e.preventDefault();
    alert('Password reset link sent to your email (demo).');
    window.location.href = 'login.html';
}
function formatMoney(amount) {
    return 'RM ' + parseFloat(amount).toFixed(2);
}