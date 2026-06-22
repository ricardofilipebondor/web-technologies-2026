const API_BASE = '/backend/server.php';
const TOKEN_KEY = 'esc_token';

function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

function setToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
}

function clearToken() {
    localStorage.removeItem(TOKEN_KEY);
}

async function apiRequest(path, options = {}) {
    const url = API_BASE + path;
    const headers = {
        ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
        ...(options.headers || {}),
    };
    const token = getToken();
    if (token) {
        headers.Authorization = 'Bearer ' + token;
    }

    const config = { ...options, headers };

    if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
        config.body = JSON.stringify(config.body);
    }

    const response = await fetch(url, config);
    if (response.status === 204) {
        if (!response.ok) {
            throw new Error('Eroare la cerere.');
        }
        return null;
    }

    const contentType = response.headers.get('content-type') || '';

    if (!contentType.includes('application/json')) {
        if (!response.ok) {
            throw new Error('Eroare la cerere.');
        }
        return response;
    }

    const data = await response.json();
    if (!response.ok) {
        throw new Error(data.detail || data.error || 'Eroare la cerere.');
    }
    return data;
}

const api = {
    get: (path) => apiRequest(path),
    post: (path, body) => apiRequest(path, { method: 'POST', body }),
    put: (path, body) => apiRequest(path, { method: 'PUT', body }),
    delete: (path) => apiRequest(path, { method: 'DELETE' }),
    items: (res) => (res && Array.isArray(res.items) ? res.items : res),
    setToken,
    clearToken,
    getToken,
    download: async (path, filename) => {
        const token = getToken();
        const headers = token ? { Authorization: 'Bearer ' + token } : {};
        const response = await fetch(API_BASE + path, { headers });
        if (!response.ok) {
            throw new Error('Eroare la descarcare.');
        }
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename || 'export';
        link.click();
        URL.revokeObjectURL(url);
    },
    upload: async (path, formData) => {
        const token = getToken();
        const headers = token ? { Authorization: 'Bearer ' + token } : {};
        const response = await fetch(API_BASE + path, {
            method: 'POST',
            headers,
            body: formData,
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.detail || data.error || 'Eroare la cerere.');
        }
        return data;
    },
};

window.api = api;
