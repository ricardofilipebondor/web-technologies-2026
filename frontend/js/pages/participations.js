window.pages.participations = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        if (sub[0] === 'report') return this.showReport();
        return this.showList();
    },

    async showList() {
        const res = await api.get('/participations');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Participari', 'Rezultate individuale', `
                <a href="#/participations/report" class="btn btn-secondary btn-sm">Raport</a>
                <a href="#/participations/form" class="btn btn-primary btn-sm">+ Adauga</a>
            `)}
            ${renderTable(['Membru','Concurs','Punctaj','Loc','Actiuni'], res.data, p => `
                <tr>
                    <td>${escapeHtml(p.member_nume + ' ' + p.member_prenume)}</td>
                    <td>${escapeHtml(p.competition_nume)}</td>
                    <td>${p.punctaj}</td>
                    <td>${p.loc_obtinut || '—'}</td>
                    <td class="actions">
                        <a href="#/participations/form/${p.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${p.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/participations/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const meta = (await api.get('/participations/meta')).data;
        let p = { member_id:'', competition_id:'', punctaj:0, loc_obtinut:'' };
        if (id) p = (await api.get('/participations')).data.find(x => String(x.id) === String(id)) || p;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Participare', '', `<a href="#/participations" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="partForm">
                ${labeledField('Membru', `<select class="select" name="member_id">${meta.members.map(m => `<option value="${m.id}" ${String(p.member_id)===String(m.id)?'selected':''}>${escapeHtml(m.nume + ' ' + m.prenume)}</option>`).join('')}</select>`)}
                ${labeledField('Concurs', `<select class="select" name="competition_id">${meta.competitions.map(c => `<option value="${c.id}" ${String(p.competition_id)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}</select>`)}
                ${labeledField('Punctaj', `<input class="input" name="punctaj" type="number" step="0.01" value="${p.punctaj}">`)}
                ${labeledField('Loc obtinut', `<input class="input" name="loc_obtinut" type="number" value="${p.loc_obtinut || ''}">`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('partForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            body.punctaj = parseFloat(body.punctaj) || 0;
            if (id) await api.put('/participations/' + id, body);
            else await api.post('/participations', body);
            location.hash = '#/participations';
        });
    },

    async showReport() {
        const res = await api.get('/participations/report');
        this.renderReport(res.data);
    },

    renderReport(d) {
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Raport participari', '', `<a href="#/participations" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="filter-bar" id="reportFilter">
                ${filterField('Concurs', `<select class="select" name="competition_id">
                    <option value="">Selecteaza concurs</option>
                    ${d.competitions.map(c => `<option value="${c.id}" ${String(d.competitionId)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}
                </select>`)}
                <button class="btn btn-secondary btn-sm">Afiseaza</button>
                ${d.competitionId ? `
                    <a href="${api.exportUrl('/participations/export?competition_id=' + d.competitionId + '&format=csv')}" class="btn btn-secondary btn-sm">CSV</a>
                    <a href="${api.exportUrl('/participations/export?competition_id=' + d.competitionId + '&format=json')}" class="btn btn-secondary btn-sm">JSON</a>
                    <a href="${api.exportUrl('/participations/export?competition_id=' + d.competitionId + '&format=xml')}" class="btn btn-secondary btn-sm">XML</a>
                ` : ''}
            </form>
            ${d.competition ? `<p><strong>${escapeHtml(d.competition.nume)}</strong></p>` : ''}
            ${d.participations.length ? renderTable(['Participant','Categorie','Punctaj','Loc'], d.participations, p => `
                <tr><td>${escapeHtml(p.member_nume + ' ' + p.member_prenume)}</td><td>${escapeHtml(p.categorie)}</td><td>${p.punctaj}</td><td>${p.loc_obtinut || '—'}</td></tr>`) : '<p>Selecteaza un concurs.</p>'}
        `;
        document.getElementById('reportFilter').addEventListener('submit', async e => {
            e.preventDefault();
            const cid = new FormData(e.target).get('competition_id');
            const r = await api.get('/participations/report?competition_id=' + cid);
            this.renderReport(r.data);
        });
    }
};

window.pages.rankings = {
    async render() {
        const res = await api.get('/rankings');
        this.renderRanking(res.data);
    },

    renderRanking(d) {
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Clasamente', 'Clasament per concurs')}
            <form class="filter-bar" id="rankFilter">
                ${filterField('Concurs', `<select class="select" name="competition_id">
                    <option value="">Selecteaza concurs</option>
                    ${d.competitions.map(c => `<option value="${c.id}" ${String(d.competitionId)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}
                </select>`)}
                <button class="btn btn-secondary btn-sm">Afiseaza</button>
                ${d.competitionId ? `
                    <a href="${api.exportUrl('/rankings/export?competition_id=' + d.competitionId + '&format=csv')}" class="btn btn-secondary btn-sm">CSV</a>
                    <a href="${api.exportUrl('/rankings/export?competition_id=' + d.competitionId + '&format=json')}" class="btn btn-secondary btn-sm">JSON</a>
                    <a href="${api.exportUrl('/rankings/export?competition_id=' + d.competitionId + '&format=xml')}" class="btn btn-secondary btn-sm">XML</a>
                ` : ''}
            </form>
            ${d.ranking.length ? renderTable(['Loc','Participant','Punctaj'], d.ranking.map((r,i) => ({...r, loc: i+1})), r => `
                <tr><td>${r.loc}</td><td>${escapeHtml(r.nume + ' ' + r.prenume)}</td><td>${r.punctaj}</td></tr>`) : '<p>Selecteaza un concurs.</p>'}
        `;
        document.getElementById('rankFilter').addEventListener('submit', async e => {
            e.preventDefault();
            const cid = new FormData(e.target).get('competition_id');
            const r = await api.get('/rankings?competition_id=' + cid);
            this.renderRanking(r.data);
        });
    }
};

window.pages.prizes = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/prizes');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Premii', 'Premii si distinctii', `<a href="#/prizes/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Titlu','Membru','Concurs','Data','Actiuni'], res.data, p => `
                <tr>
                    <td><strong>${escapeHtml(p.titlu)}</strong></td>
                    <td>${escapeHtml(p.nume + ' ' + p.prenume)}</td>
                    <td>${escapeHtml(p.competition_nume || '—')}</td>
                    <td>${escapeHtml(p.data_acordare)}</td>
                    <td class="actions">
                        <a href="#/prizes/form/${p.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${p.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/prizes/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const meta = (await api.get('/prizes/meta')).data;
        let p = { titlu:'', descriere:'', member_id:'', competition_id:'', data_acordare:'' };
        if (id) p = (await api.get('/prizes')).data.find(x => String(x.id) === String(id)) || p;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Premiu', '', `<a href="#/prizes" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="prizeForm">
                ${labeledField('Titlu', `<input class="input" name="titlu" value="${escapeHtml(p.titlu)}" required>`)}
                ${labeledField('Descriere', `<textarea class="textarea" name="descriere">${escapeHtml(p.descriere)}</textarea>`)}
                ${labeledField('Membru', `<select class="select" name="member_id">${meta.members.map(m => `<option value="${m.id}" ${String(p.member_id)===String(m.id)?'selected':''}>${escapeHtml(m.nume + ' ' + m.prenume)}</option>`).join('')}</select>`)}
                ${labeledField('Concurs', `<select class="select" name="competition_id"><option value="">— Fara concurs —</option>${meta.competitions.map(c => `<option value="${c.id}" ${String(p.competition_id)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}</select>`)}
                ${labeledField('Data acordare', `<input class="input" name="data_acordare" type="date" value="${escapeHtml(p.data_acordare)}">`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('prizeForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (id) await api.put('/prizes/' + id, body);
            else await api.post('/prizes', body);
            location.hash = '#/prizes';
        });
    }
};
