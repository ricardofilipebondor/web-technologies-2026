window.pages.teams = {
    async render(sub) {
        if (sub[0] === 'members' && sub[1]) return this.showMembers(sub[1]);
        if (sub[0] === 'results' && sub[1]) return this.showResults(sub[1]);
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/teams');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Echipe', 'Echipe de performanta', `<a href="#/teams/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Denumire','Descriere','Actiuni'], res.data, t => `
                <tr>
                    <td><strong>${escapeHtml(t.denumire)}</strong></td>
                    <td>${escapeHtml(t.descriere || '')}</td>
                    <td class="actions">
                        <a href="#/teams/members/${t.id}" class="btn btn-ghost btn-sm">Membri</a>
                        <a href="#/teams/results/${t.id}" class="btn btn-ghost btn-sm">Rezultate</a>
                        <a href="#/teams/form/${t.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${t.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/teams/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        let t = { denumire:'', descriere:'' };
        if (id) {
            const res = await api.get('/teams/' + id);
            t = res.data.team;
        }
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Echipa', '', `<a href="#/teams" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="teamForm">
                <input class="input" name="denumire" value="${escapeHtml(t.denumire)}" required>
                <input class="input" name="descriere" value="${escapeHtml(t.descriere)}" style="margin-top:0.5rem">
                <button class="btn btn-primary" style="margin-top:1rem">Salveaza</button>
            </form>`;
        document.getElementById('teamForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (id) await api.put('/teams/' + id, body);
            else await api.post('/teams', body);
            location.hash = '#/teams';
        });
    },

    async showMembers(id) {
        const res = await api.get('/teams/' + id + '/members');
        const { team, members, available } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Membri echipa: ' + team.denumire, '', `<a href="#/teams" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="filter-bar" id="addMemberForm">
                <select class="select" name="member_id" style="flex:1">${available.map(m => `<option value="${m.id}">${escapeHtml(m.nume + ' ' + m.prenume)}</option>`).join('')}</select>
                <button class="btn btn-primary btn-sm">Adauga</button>
            </form>
            ${renderTable(['Membru','Actiuni'], members, m => `
                <tr><td>${escapeHtml(m.nume + ' ' + m.prenume)}</td>
                <td><button class="btn btn-danger btn-sm" data-remove="${m.id}">Elimina</button></td></tr>`)}
        `;
        document.getElementById('addMemberForm').addEventListener('submit', async e => {
            e.preventDefault();
            const member_id = new FormData(e.target).get('member_id');
            await api.post('/teams/members', { team_id: id, member_id });
            this.showMembers(id);
        });
        document.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', async () => {
                await api.delete('/teams/' + id + '/members/' + btn.dataset.remove);
                this.showMembers(id);
            });
        });
    },

    async showResults(id) {
        const res = await api.get('/teams/' + id + '/results');
        const { team, results, competitions } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Rezultate: ' + team.denumire, '', `<a href="#/teams" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="resultForm">
                <select class="select" name="competition_id">${competitions.map(c => `<option value="${c.id}">${escapeHtml(c.nume)}</option>`).join('')}</select>
                <input class="input" name="punctaj_total" type="number" step="0.01" placeholder="Punctaj" style="margin-top:0.5rem">
                <input class="input" name="loc_obtinut" type="number" placeholder="Loc" style="margin-top:0.5rem">
                <button class="btn btn-primary btn-sm" style="margin-top:0.5rem">Adauga rezultat</button>
            </form>
            ${renderTable(['Concurs','Punctaj','Loc','Actiuni'], results, r => `
                <tr><td>${escapeHtml(r.competition_nume)}</td><td>${r.punctaj_total}</td><td>${r.loc_obtinut || '—'}</td>
                <td><button class="btn btn-danger btn-sm" data-del="${r.id}">Sterge</button></td></tr>`)}
        `;
        document.getElementById('resultForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            body.team_id = id;
            body.punctaj_total = parseFloat(body.punctaj_total) || 0;
            await api.post('/teams/results', body);
            this.showResults(id);
        });
        document.querySelectorAll('[data-del]').forEach(btn => {
            btn.addEventListener('click', async () => {
                await api.delete('/teams/results/' + btn.dataset.del);
                this.showResults(id);
            });
        });
    }
};

window.pages.groups = {
    async render(sub) {
        if (sub[0] === 'members' && sub[1]) return this.showMembers(sub[1]);
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/groups');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Grupe', 'Grupe de antrenament', `<a href="#/groups/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Denumire','Nivel','Antrenor','Actiuni'], res.data, g => `
                <tr>
                    <td><strong>${escapeHtml(g.denumire)}</strong></td>
                    <td><span class="badge">${escapeHtml(g.nivel)}</span></td>
                    <td>${escapeHtml(g.coach_nume || '—')}</td>
                    <td class="actions">
                        <a href="#/groups/members/${g.id}" class="btn btn-ghost btn-sm">Membri</a>
                        <a href="#/groups/form/${g.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${g.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/groups/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const coaches = (await api.get('/groups/coaches')).data;
        let g = { denumire:'', nivel:'incepatori', coach_id:'' };
        if (id) {
            g = (await api.get('/groups')).data.find(x => String(x.id) === String(id)) || g;
        }
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Grup', '', `<a href="#/groups" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="groupForm">
                <input class="input" name="denumire" value="${escapeHtml(g.denumire)}" required>
                <select class="select" name="nivel" style="margin-top:0.5rem">
                    ${['incepatori','intermediar','avansat','competitie'].map(n => `<option value="${n}" ${g.nivel===n?'selected':''}>${n}</option>`).join('')}
                </select>
                <select class="select" name="coach_id" style="margin-top:0.5rem"><option value="">— Antrenor —</option>${coaches.map(c => `<option value="${c.id}" ${String(g.coach_id)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}</select></select>
                <button class="btn btn-primary" style="margin-top:1rem">Salveaza</button>
            </form>`;
        document.getElementById('groupForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (id) await api.put('/groups/' + id, body);
            else await api.post('/groups', body);
            location.hash = '#/groups';
        });
    },

    async showMembers(id) {
        const res = await api.get('/groups/' + id + '/members');
        const { group, members, available } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Membri grup: ' + group.denumire, '', `<a href="#/groups" class="btn btn-secondary btn-sm">Inapoi</a>`)}
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
            await api.post('/groups/members', { group_id: id, member_id: new FormData(e.target).get('member_id') });
            this.showMembers(id);
        });
        document.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', async () => {
                await api.delete('/groups/' + id + '/members/' + btn.dataset.remove);
                this.showMembers(id);
            });
        });
    }
};
