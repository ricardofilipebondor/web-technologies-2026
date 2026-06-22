function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

function showAlert(message, type = 'danger') {
    const box = document.getElementById('flash-alert');
    if (!box) return;
    box.className = 'alert alert-' + type;
    box.textContent = message;
    box.style.display = 'block';
    setTimeout(() => { box.style.display = 'none'; }, 4000);
}

function confirmAction(message) {
    return window.confirm(message);
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

window.utils = { escapeHtml, showAlert, confirmAction, renderTable, pageHeader, labeledField, filterField };
