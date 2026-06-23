function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;   
}

function showAlert(message, type = 'danger') {
    const box = document.getElementById('flash-alert');
    if (!box) return;
    if (showAlert._timer) clearTimeout(showAlert._timer);
    box.className = 'alert toast alert-' + type;
    box.textContent = message;
    box.style.display = 'block';
    showAlert._timer = setTimeout(() => { box.style.display = 'none'; }, 4500);
}

async function runAction(action, { success = null, error = null, onSuccess = null } = {}) {
    try {
        const result = await action();
        if (success) showAlert(success, 'success');
        if (onSuccess) await onSuccess(result);
        return result;
    } catch (err) {
        showAlert(error || err.message || 'Eroare la operatie.', 'danger');
        return null;
    }
}

function confirmAction(message) {
    return window.confirm(message);
}

async function downloadExport(path, filename) {
    try {
        await api.download(path, filename);
    } catch (err) {
        showAlert(err.message || 'Eroare la descarcare.', 'danger');
    }
}

function renderTable(headers, rows, rowHtml) {
    return `
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr>${headers.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr></thead>
                <tbody>${rows.map(rowHtml).join('')}</tbody>
            </table>
        </div>`;
}

function labeledField(label, controlHtml, full = false) {
    const cls = full ? 'form-field full' : 'form-field';
    return `<label class="${cls}"><span class="form-label">${escapeHtml(label)}</span>${controlHtml}</label>`;
}

function filterField(label, controlHtml) {
    return `<div class="form-field filter-field"><span class="form-label">${escapeHtml(label)}</span>${controlHtml}</div>`;
}

function pageHeader(title, subtitle, toolbarHtml = '') {
    return `
        <div class="page-header">
            <div>
                <h1 class="page-title">${escapeHtml(title)}</h1>
                <p class="page-subtitle">${escapeHtml(subtitle)}</p>
            </div>
            ${toolbarHtml ? `<div class="toolbar">${toolbarHtml}</div>` : ''}
        </div>`;
}

window.utils = { escapeHtml, showAlert, runAction, confirmAction, renderTable, pageHeader, labeledField, filterField };
