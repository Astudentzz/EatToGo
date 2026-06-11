const APP_ROOT = window.location.protocol === 'file:'
    ? 'http://localhost/EatToGo'
    : new URL('.', window.location.href).href.replace(/\/$/, '');
const API_BASE = `${APP_ROOT}/api`;

async function apiCall(endpoint, options = {}) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers: { 'Content-Type': 'application/json', ...options.headers },
        credentials: 'same-origin'
    });
    if (!response.ok) {
        let errorMsg = `HTTP ${response.status}`;
        try {
            const err = await response.json();
            errorMsg = err.error || errorMsg;
        } catch(e) {}
        throw new Error(errorMsg);
    }
    return response.json();
}

async function checkSession() {
    try {
        return await apiCall('/session.php');
    } catch (e) {
        return { loggedIn: false };
    }
}

async function logout() {
    await apiCall('/logout.php', { method: 'POST' });
    window.location.href = 'index.html';
}

async function nav() {
    const authArea = document.getElementById('authArea');
    if (!authArea) return;
    const guestAuthHtml = `
            <a href="login.html" class="btn btn-sm btn-outline-etg">Sign In</a>
            <a href="signup.html" class="btn btn-sm btn-etg ms-2">Sign Up</a>
        `;
    authArea.innerHTML = guestAuthHtml;
    const sessionData = await checkSession();
    if (sessionData.loggedIn) {
        const user = sessionData.user;
        authArea.innerHTML = `
            <div class="dropdown">
                <span class="fw-bold">👋 ${escapeHtml(user.name)}</span>
                <a href="#" onclick="logout()" class="ms-2 text-danger">Logout</a>
            </div>
        `;
    } else {
        authArea.innerHTML = guestAuthHtml;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;');
}
