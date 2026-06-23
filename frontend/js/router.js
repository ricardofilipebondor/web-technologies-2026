function setActiveLink(module) {
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.toggle('active', link.dataset.module === module);
    });
}

async function navigate() {
    const hash = location.hash.slice(1) || '/dashboard';
    const parts = hash.replace(/^\//, '').split('/');
    const module = parts[0] || 'dashboard';
    const container = document.getElementById('page-content');

    setActiveLink(module);

    const page = window.pages[module];
    if (!page || !page.render) {
        container.innerHTML = '<p>Pagina negasita.</p>';
        return;
    }

    container.innerHTML = '<p>Se incarca...</p>';
    try {
        await page.render(parts.slice(1));
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">${escapeHtml(err.message)}</div>`;
        showAlert(err.message || 'Eroare la incarcarea paginii.', 'danger');
    }
}

function initRouter() {
    window.addEventListener('hashchange', navigate);
    if (!location.hash) {
        location.hash = '#/dashboard';
    } else {
        navigate();
    }
}

window.initRouter = initRouter;
