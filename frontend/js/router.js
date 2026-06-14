const routes = {
    dashboard: (sub) => window.pages.dashboard.render(sub),
    members: (sub) => window.pages.members.render(sub),
    coaches: (sub) => window.pages.coaches.render(sub),
    teams: (sub) => window.pages.teams.render(sub),
    groups: (sub) => window.pages.groups.render(sub),
    halls: (sub) => window.pages.halls.render(sub),
    activities: (sub) => window.pages.activities.render(sub),
    competitions: (sub) => window.pages.competitions.render(sub),
    participations: (sub) => window.pages.participations.render(sub),
    rankings: (sub) => window.pages.rankings.render(sub),
    prizes: (sub) => window.pages.prizes.render(sub),
    trips: (sub) => window.pages.trips.render(sub),
    expenses: (sub) => window.pages.expenses.render(sub),
    reimbursements: (sub) => window.pages.reimbursements.render(sub),
    admin: (sub) => window.pages.admin.render(sub),
};

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

    const handler = routes[module];
    if (!handler) {
        container.innerHTML = '<p>Pagina negasita.</p>';
        return;
    }

    container.innerHTML = '<p>Se incarca...</p>';
    try {
        await handler(parts.slice(1));
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">${escapeHtml(err.message)}</div>`;
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
