let currentUser = null;

async function initApp() {
    try {
        const res = await api.get('/auth/me');
        currentUser = res.data;
        document.getElementById('topbarUser').innerHTML =
            `<strong>${escapeHtml(currentUser.username)}</strong> · ${escapeHtml(currentUser.role)}`;

        const menuRes = await api.get('/auth/menu');
        renderSidebar(menuRes.data);
        initSidebarToggle();
        initRouter();
    } catch (err) {
        window.location.href = 'index.html';
    }
}

function renderSidebar(items) {
    const nav = document.getElementById('sidebarNav');
    const routeMap = {
        dashboard: '#/dashboard',
        members: '#/members',
        coaches: '#/coaches',
        teams: '#/teams',
        groups: '#/groups',
        halls: '#/halls',
        activities: '#/activities',
        competitions: '#/competitions',
        participations: '#/participations',
        rankings: '#/rankings',
        prizes: '#/prizes',
        trips: '#/trips',
        expenses: '#/expenses',
        reimbursements: '#/reimbursements',
        admin: '#/admin',
    };

    nav.innerHTML = items.map(item => {
        const hash = routeMap[item.module] || '#/' + item.module;
        return `<li><a class="sidebar-link" href="${hash}" data-module="${item.module}">${escapeHtml(item.label)}</a></li>`;
    }).join('');
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
        await api.post('/auth/logout');
    } finally {
        window.location.href = 'index.html';
    }
});

document.addEventListener('DOMContentLoaded', initApp);

window.currentUser = () => currentUser;
