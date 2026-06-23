let currentUser = null;

async function initApp() {
    try {
        if (!api.getToken()) {
            throw new Error('Neautentificat');
        }
        currentUser = await api.get('/users/me');
        document.getElementById('topbarUser').innerHTML =
            `<strong>${escapeHtml(currentUser.username)}</strong> · ${escapeHtml(currentUser.role)}`;

        const menuRes = await api.get('/menu');
        renderSidebar(api.items(menuRes));
        initSidebarToggle();
        initRouter();
    } catch (err) {
        api.clearToken();
        window.location.href = 'index.html';
    }
}

function renderSidebar(items) {
    const nav = document.getElementById('sidebarNav');
    nav.innerHTML = items.map(item => `
        <li><a class="sidebar-link" href="#/${item.module}" data-module="${item.module}">${escapeHtml(item.label)}</a></li>
    `).join('');
}

function initSidebarToggle() {
    const toggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay?.classList.remove('open');
    }

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay?.classList.toggle('open');
    });
    overlay?.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
}

document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    try {
        await api.delete('/sessions');
    } finally {
        api.clearToken();
        window.location.href = 'index.html';
    }
});

document.addEventListener('DOMContentLoaded', initApp);

window.currentUser = () => currentUser;
