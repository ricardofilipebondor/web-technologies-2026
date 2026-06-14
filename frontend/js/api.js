const API_BASE = '/backend/server.php';

async function apiRequest(path, options = {}) {
    const url = API_BASE + path;
    const config = {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            ...(options.headers || {}),
        },
        ...options,
    };

    if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
        config.body = JSON.stringify(config.body);
    }

    const response = await fetch(url, config);
    const contentType = response.headers.get('content-type') || '';

    if (!contentType.includes('application/json')) {
        if (!response.ok) {
            throw new Error('Eroare la cerere.');
        }
        return response;
    }

    const data = await response.json();
    if (!response.ok) {
        throw new Error(data.error || 'Eroare la cerere.');
    }
    return data;
}

const api = {
    get: (path) => apiRequest(path),
    post: (path, body) => apiRequest(path, { method: 'POST', body }),
    put: (path, body) => apiRequest(path, { method: 'PUT', body }),
    delete: (path) => apiRequest(path, { method: 'DELETE' }),
    exportUrl: (path) => API_BASE + path,
    upload: async (path, formData) => {
        const response = await fetch(API_BASE + path, {
            method: 'POST',
            credentials: 'include',
            body: formData,
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Eroare la cerere.');
        }
        return data;
    },
};

window.api = api;
