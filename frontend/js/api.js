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
    const headers = { ...(options.headers || {}) };
    const token = getToken();
    if (token) {
        headers.Authorization = 'Bearer ' + token;
    }
    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(API_BASE + path, { ...options, headers });
    if (response.status === 204) {
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
    post: (path, body) => apiRequest(path, { method: 'POST', body: body instanceof FormData ? body : JSON.stringify(body) }),
    put: (path, body) => apiRequest(path, { method: 'PUT', body: JSON.stringify(body) }),
    delete: (path) => apiRequest(path, { method: 'DELETE' }),
    items: (res) => (res && Array.isArray(res.items) ? res.items : res),
    setToken,
    clearToken,
    getToken,
    download: async (path, filename) => {
        const token = getToken();
        const headers = token ? { Authorization: 'Bearer ' + token } : {};
        const response = await fetch(API_BASE + path, { headers });
        const contentType = response.headers.get('content-type') || '';
        const disposition = response.headers.get('content-disposition') || '';
        const isAttachment = disposition.includes('attachment');

        if (!response.ok) {
            if (contentType.includes('application/json')) {
                const data = await response.json();
                throw new Error(data.detail || data.error || 'Eroare la descarcare.');
            }
            throw new Error('Eroare la descarcare.');
        }
        if (!isAttachment && contentType.includes('application/json')) {
            const data = await response.json();
            throw new Error(data.detail || data.error || 'Eroare la descarcare.');
        }

        const blob = await response.blob();
        const match = disposition.match(/filename="?([^";\n]+)"?/i);
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = match ? match[1].trim() : (filename || 'export');
        link.click();
    },
    upload: (path, formData) => apiRequest(path, { method: 'POST', body: formData }),
};

window.api = api;
