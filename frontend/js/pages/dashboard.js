window.pages = window.pages || {};

window.pages.dashboard = {
    async render() {
        const res = await api.get('/dashboard');
        const d = res.data;
        const container = document.getElementById('page-content');

        container.innerHTML = `
            ${pageHeader('Dashboard', 'Privire de ansamblu asupra clubului')}
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-label">Membri</div><div class="stat-value">${d.memberCount}</div></div>
                <div class="stat-card"><div class="stat-label">Antrenori</div><div class="stat-value">${d.coachCount}</div></div>
                <div class="stat-card"><div class="stat-label">Concursuri</div><div class="stat-value">${d.competitionCount}</div></div>
                <div class="stat-card"><div class="stat-label">Activitati</div><div class="stat-value">${d.activityCount}</div></div>
                <div class="stat-card"><div class="stat-label">Deplasari</div><div class="stat-value">${d.tripCount}</div></div>
            </div>
            <div class="grid-3">
                <div class="card"><div class="card-header">Ultimele concursuri</div>
                    ${d.recentCompetitions.map(c => `<div class="list-item"><div><div class="list-item-title">${escapeHtml(c.nume)}</div><div class="list-item-meta">${escapeHtml(c.locatie)}</div></div><span class="badge">${escapeHtml(c.data)}</span></div>`).join('')}
                </div>
                <div class="card"><div class="card-header">Ultimele activitati</div>
                    ${d.recentActivities.map(a => `<div class="list-item"><div><div class="list-item-title">${escapeHtml(a.titlu)}</div><div class="list-item-meta">${escapeHtml(a.data_start)} · ${escapeHtml(a.hall_name || '')}</div></div></div>`).join('')}
                </div>
                <div class="card"><div class="card-header">Ultimele premii</div>
                    ${d.recentPrizes.map(p => `<div class="list-item"><div><div class="list-item-title">${escapeHtml(p.titlu)}</div><div class="list-item-meta">${escapeHtml(p.nume + ' ' + p.prenume)}</div></div><span class="badge">${escapeHtml(p.data_acordare)}</span></div>`).join('')}
                </div>
            </div>`;
    }
};
