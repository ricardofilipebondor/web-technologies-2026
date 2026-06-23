window.pages.trips = {
    async render(sub) {
        if (sub[0] === 'members' && sub[1]) return this.showMembers(sub[1]);
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deplasari', 'Deplasari echipa', `<a href="#/trips/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Destinatie','Plecare','Intoarcere','Echipa','Actiuni'], api.items(await api.get('/trips')), t => `
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
        const teams = api.items(await api.get('/teams'));
        let t = { destinatie:'', data_plecare:'', data_intoarcere:'', scop:'', team_id:'' };
        if (id) t = await api.get('/trips/' + id);
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deplasare', '', `<a href="#/trips" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="tripForm">
                ${labeledField('Destinatie', `<input class="input" name="destinatie" value="${escapeHtml(t.destinatie)}" required>`)}
                ${labeledField('Data plecare', `<input class="input" name="data_plecare" type="date" value="${escapeHtml(t.data_plecare)}">`)}
                ${labeledField('Data intoarcere', `<input class="input" name="data_intoarcere" type="date" value="${escapeHtml(t.data_intoarcere)}">`)}
                ${labeledField('Scop', `<input class="input" name="scop" value="${escapeHtml(t.scop)}">`)}
                ${labeledField('Echipa', `<select class="select" name="team_id"><option value="">— Fara echipa —</option>${teams.map(tm => `<option value="${tm.id}" ${String(t.team_id)===String(tm.id)?'selected':''}>${escapeHtml(tm.denumire)}</option>`).join('')}</select>`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
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
        const { trip, members, available } = await api.get('/trips/' + id + '/members');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Membri deplasare: ' + trip.destinatie, '', `<a href="#/trips" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="filter-bar" id="addForm">
                ${filterField('Membru', `<select class="select" name="member_id">${available.map(m => `<option value="${m.id}">${escapeHtml(m.nume + ' ' + m.prenume)}</option>`).join('')}</select>`)}
                <button class="btn btn-primary btn-sm">Adauga</button>
            </form>
            ${renderTable(['Membru','Actiuni'], members, m => `
                <tr><td>${escapeHtml(m.nume + ' ' + m.prenume)}</td>
                <td><button class="btn btn-danger btn-sm" data-remove="${m.id}">Elimina</button></td></tr>`)}
        `;
        document.getElementById('addForm').addEventListener('submit', async e => {
            e.preventDefault();
            await api.post('/trips/' + id + '/members', { member_id: new FormData(e.target).get('member_id') });
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
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Cheltuieli', 'Cheltuieli deplasari', `<a href="#/expenses/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Deplasare','Tip','Suma','Observatii','Actiuni'], api.items(await api.get('/expenses')), e => `
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
        const trips = api.items(await api.get('/trips'));
        let e = { trip_id:'', tip:'transport', suma:0, observatii:'' };
        if (id) e = await api.get('/expenses/' + id);
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Cheltuiala', '', `<a href="#/expenses" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="expForm">
                ${labeledField('Deplasare', `<select class="select" name="trip_id">${trips.map(t => `<option value="${t.id}" ${String(e.trip_id)===String(t.id)?'selected':''}>${escapeHtml(t.destinatie)}</option>`).join('')}</select>`)}
                ${labeledField('Tip cheltuiala', `<select class="select" name="tip">${['transport','cazare','masa'].map(t => `<option value="${t}" ${e.tip===t?'selected':''}>${t}</option>`).join('')}</select>`)}
                ${labeledField('Suma (RON)', `<input class="input" name="suma" type="number" step="0.01" value="${e.suma}">`)}
                ${labeledField('Observatii', `<input class="input" name="observatii" value="${escapeHtml(e.observatii)}">`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
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
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Deconturi', 'Rapoarte de decont')}
            ${renderTable(['Destinatie','Plecare','Echipa','Actiuni'], api.items(await api.get('/reimbursements')), t => `
                <tr>
                    <td><strong>${escapeHtml(t.destinatie)}</strong></td>
                    <td>${escapeHtml(t.data_plecare)}</td>
                    <td>${escapeHtml(t.team_nume || '—')}</td>
                    <td><a href="#/reimbursements/show/${t.id}" class="btn btn-ghost btn-sm">Detalii</a></td>
                </tr>`)}
        `;
    },

    async showDetail(id) {
        const { trip, members, expenses, total } = await api.get('/reimbursements/' + id);
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Decont: ' + trip.destinatie, '', `
                <a href="#/reimbursements" class="btn btn-secondary btn-sm">Inapoi</a>
                <button type="button" class="btn btn-secondary btn-sm" data-export="csv">Export CSV</button>
                <button type="button" class="btn btn-secondary btn-sm" data-export="json">Export JSON</button>
                <button type="button" class="btn btn-secondary btn-sm" data-export="pdf">Export PDF</button>
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
        document.querySelectorAll('[data-export]').forEach(btn => {
            btn.addEventListener('click', () => {
                const fmt = btn.dataset.export;
                downloadExport('/reimbursements/' + id + '/export?format=' + fmt, 'decont.' + fmt);
            });
        });
    }
};
