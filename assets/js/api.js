const API_BASE = '/eattogo/api';

async function apiCall(endpoint, options = {}) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            ...options.headers
        },
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
        const data = await apiCall('/session.php');
        return data;
    } catch (e) {
        return { loggedIn: false };
    }
}