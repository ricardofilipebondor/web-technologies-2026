window.pages.trips = {
    async render(sub) {
        if (sub[0] === 'members' && sub[1]) return this.showMembers(sub[1]);
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/trips');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deplasari', 'Deplasari echipa', `<a href="#/trips/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Destinatie','Plecare','Intoarcere','Echipa','Actiuni'], res.data, t => `
                <tr>
                    <td><strong>${escapeHtml(t.destinatie)}</strong></td>
                    <td>${escapeHtml(t.data_plecare)}</td>
                    <td>${escapeHtml(t.data_intoarcere)}</td>
                    <td>${escapeHtml(t.team_nume || '—')}</td>
                    <td class="actions">
                        <a href="#/trips/members/${t.id}" class="btn btn-ghost btn-sm">Membri</a>
                        <a href="#/trips/form/${t.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${t.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/trips/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const meta = (await api.get('/trips/meta')).data;
        let t = { destinatie:'', data_plecare:'', data_intoarcere:'', scop:'', team_id:'' };
        if (id) t = (await api.get('/trips')).data.find(x => String(x.id) === String(id)) || t;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deplasare', '', `<a href="#/trips" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="tripForm">
                <input class="input" name="destinatie" value="${escapeHtml(t.destinatie)}" required>
                <input class="input" name="data_plecare" type="date" value="${escapeHtml(t.data_plecare)}" style="margin-top:0.5rem">
                <input class="input" name="data_intoarcere" type="date" value="${escapeHtml(t.data_intoarcere)}" style="margin-top:0.5rem">
                <input class="input" name="scop" value="${escapeHtml(t.scop)}" style="margin-top:0.5rem">
                <select class="select" name="team_id" style="margin-top:0.5rem"><option value="">—</option>${meta.teams.map(tm => `<option value="${tm.id}">${escapeHtml(tm.denumire)}</option>`).join('')}</select>
                <button class="btn btn-primary" style="margin-top:1rem">Salveaza</button>
            </form>`;
        document.getElementById('tripForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (id) await api.put('/trips/' + id, body);
            else await api.post('/trips', body);
            location.hash = '#/trips';
        });
    },

    async showMembers(id) {
        const res = await api.get('/trips/' + id + '/members');
        const { trip, members, available } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Membri deplasare: ' + trip.destinatie, '', `<a href="#/trips" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="filter-bar" id="addForm">
                <select class="select" name="member_id" style="flex:1">${available.map(m => `<option value="${m.id}">${escapeHtml(m.nume + ' ' + m.prenume)}</option>`).join('')}</select>
                <button class="btn btn-primary btn-sm">Adauga</button>
            </form>
            ${renderTable(['Membru','Actiuni'], members, m => `
                <tr><td>${escapeHtml(m.nume + ' ' + m.prenume)}</td>
                <td><button class="btn btn-danger btn-sm" data-remove="${m.id}">Elimina</button></td></tr>`)}
        `;
        document.getElementById('addForm').addEventListener('submit', async e => {
            e.preventDefault();
            await api.post('/trips/members', { trip_id: id, member_id: new FormData(e.target).get('member_id') });
            this.showMembers(id);
        });
        document.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', async () => {
                await api.delete('/trips/' + id + '/members/' + btn.dataset.remove);
                this.showMembers(id);
            });
        });
    }
};

window.pages.expenses = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/expenses');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Cheltuieli', 'Cheltuieli deplasari', `<a href="#/expenses/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Deplasare','Tip','Suma','Observatii','Actiuni'], res.data, e => `
                <tr>
                    <td>${escapeHtml(e.trip_destinatie || '')}</td>
                    <td>${escapeHtml(e.tip)}</td>
                    <td>${e.suma} RON</td>
                    <td>${escapeHtml(e.observatii || '')}</td>
                    <td class="actions">
                        <a href="#/expenses/form/${e.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${e.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/expenses/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const meta = (await api.get('/expenses/meta')).data;
        let e = { trip_id:'', tip:'transport', suma:0, observatii:'' };
        if (id) e = (await api.get('/expenses')).data.find(x => String(x.id) === String(id)) || e;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Cheltuiala', '', `<a href="#/expenses" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="expForm">
                <select class="select" name="trip_id">${meta.trips.map(t => `<option value="${t.id}">${escapeHtml(t.destinatie)}</option>`).join('')}</select>
                <select class="select" name="tip" style="margin-top:0.5rem">${['transport','cazare','masa'].map(t => `<option value="${t}">${t}</option>`).join('')}</select>
                <input class="input" name="suma" type="number" step="0.01" value="${e.suma}" style="margin-top:0.5rem">
                <input class="input" name="observatii" value="${escapeHtml(e.observatii)}" style="margin-top:0.5rem">
                <button class="btn btn-primary" style="margin-top:1rem">Salveaza</button>
            </form>`;
        document.getElementById('expForm').addEventListener('submit', async ev => {
            ev.preventDefault();
            const body = Object.fromEntries(new FormData(ev.target).entries());
            body.suma = parseFloat(body.suma) || 0;
            if (id) await api.put('/expenses/' + id, body);
            else await api.post('/expenses', body);
            location.hash = '#/expenses';
        });
    }
};

window.pages.reimbursements = {
    async render(sub) {
        if (sub[0] === 'show' && sub[1]) return this.showDetail(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/reimbursements');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deconturi', 'Rapoarte de decont')}
            ${renderTable(['Destinatie','Plecare','Echipa','Actiuni'], res.data, t => `
                <tr>
                    <td><strong>${escapeHtml(t.destinatie)}</strong></td>
                    <td>${escapeHtml(t.data_plecare)}</td>
                    <td>${escapeHtml(t.team_nume || '—')}</td>
                    <td><a href="#/reimbursements/show/${t.id}" class="btn btn-ghost btn-sm">Detalii</a></td>
                </tr>`)}
        `;
    },

    async showDetail(id) {
        const res = await api.get('/reimbursements/' + id);
        const { trip, members, expenses, total } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Decont: ' + trip.destinatie, '', `
                <a href="#/reimbursements" class="btn btn-secondary btn-sm">Inapoi</a>
                <a href="${api.exportUrl('/reimbursements/' + id + '/export?format=csv')}" class="btn btn-secondary btn-sm">Export CSV</a>
                <a href="${api.exportUrl('/reimbursements/' + id + '/export?format=pdf')}" class="btn btn-secondary btn-sm">Export PDF</a>
            `)}
            <div class="card card-body">
                <p>Echipa: ${escapeHtml(trip.team_nume || '—')}</p>
                <p>${escapeHtml(trip.data_plecare)} — ${escapeHtml(trip.data_intoarcere)}</p>
                <p>Scop: ${escapeHtml(trip.scop || '')}</p>
            </div>
            <div class="card" style="margin-top:1rem"><div class="card-header">Membri</div>
                ${members.map(m => `<div class="list-item"><div class="list-item-title">${escapeHtml(m.nume + ' ' + m.prenume)}</div></div>`).join('')}
            </div>
            <div class="card" style="margin-top:1rem"><div class="card-header">Cheltuieli (Total: ${total} RON)</div>
                ${renderTable(['Tip','Suma','Observatii'], expenses, e => `
                    <tr><td>${escapeHtml(e.tip)}</td><td>${e.suma} RON</td><td>${escapeHtml(e.observatii || '')}</td></tr>`)}
            </div>`;
    }
};
