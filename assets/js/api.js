const APP_ROOT = window.location.protocol === 'file:'
    ? 'http://localhost/EatToGo'
    : new URL('.', window.location.href).href.replace(/\/$/, '');
const API_BASE = `${APP_ROOT}/api`;
const nativeFetch = window.fetch.bind(window);
let csrfToken = null;

async function getCsrfToken() {
    if (csrfToken) return csrfToken;
    try {
        const response = await nativeFetch(`${API_BASE}/session.php`, { credentials: 'same-origin' });
        const data = await response.json();
        csrfToken = data.csrfToken || null;
    } catch (e) {}
    return csrfToken;
}

window.fetch = async function securedFetch(resource, options = {}) {
    const url = typeof resource === 'string' ? resource : resource?.url;
    const method = (options.method || 'GET').toUpperCase();
    const isApiRequest = url && String(url).startsWith(API_BASE);
    if (isApiRequest && !['GET', 'HEAD', 'OPTIONS'].includes(method)) {
        const token = await getCsrfToken();
        options = {
            ...options,
            credentials: options.credentials || 'same-origin',
            headers: { ...(options.headers || {}), ...(token ? { 'X-CSRF-Token': token } : {}) }
        };
    }
    return nativeFetch(resource, options);
};

async function apiCall(endpoint, options = {}) {
    const method = (options.method || 'GET').toUpperCase();
    const token = ['GET', 'HEAD', 'OPTIONS'].includes(method) ? null : await getCsrfToken();
    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers: { 'Content-Type': 'application/json', ...(token ? { 'X-CSRF-Token': token } : {}), ...options.headers },
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
