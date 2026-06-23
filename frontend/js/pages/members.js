window.pages.members = {
    async render(sub) {
        const action = sub[0];
        if (action === 'show' && sub[1]) return this.showProfile(sub[1]);
        if (action === 'form') return this.showForm(sub[1]);
        if (action === 'import') return this.showImport();
        return this.showList();
    },

    async showList() {
        const search = document.getElementById('memberSearch')?.value || '';
        const categorie = document.getElementById('memberCat')?.value || '';
        const qs = new URLSearchParams({ search, categorie }).toString();
        const res = await api.get('/members?' + qs);
        const members = api.items(res);
        const container = document.getElementById('page-content');

        container.innerHTML = `
            ${pageHeader('Membri', 'Gestioneaza membrii clubului', `
                <a href="#/members/import" class="btn btn-secondary btn-sm">Import</a>
                <button type="button" class="btn btn-secondary btn-sm" data-export="csv">CSV</button>
                <button type="button" class="btn btn-secondary btn-sm" data-export="json">JSON</button>
                <button type="button" class="btn btn-secondary btn-sm" data-export="xml">XML</button>
                <a href="#/members/form" class="btn btn-primary btn-sm">+ Adauga</a>
            `)}
            <form class="filter-bar" id="memberFilter">
                ${filterField('Cautare', `<input type="text" id="memberSearch" class="input" placeholder="Nume, email..." value="${escapeHtml(search)}">`)}
                ${filterField('Categorie', `<select id="memberCat" class="select">
                    <option value="">Toate</option>
                    ${['junior','senior','amator','profesionist'].map(c => `<option value="${c}" ${categorie===c?'selected':''}>${c}</option>`).join('')}
                </select>`)}
                <button class="btn btn-secondary btn-sm">Filtreaza</button>
            </form>
            ${renderTable(['Nume','Email','Categorie','Rating','Antrenor','Actiuni'], members, m => `
                <tr>
                    <td><strong>${escapeHtml(m.nume + ' ' + m.prenume)}</strong></td>
                    <td>${escapeHtml(m.email)}</td>
                    <td><span class="badge">${escapeHtml(m.categorie)}</span></td>
                    <td>${escapeHtml(String(m.rating))}</td>
                    <td>${escapeHtml(m.coach_nume || '—')}</td>
                    <td class="actions">
                        <a href="#/members/show/${m.id}" class="btn btn-ghost btn-sm">Profil</a>
                        <a href="#/members/form/${m.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${m.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;

        document.getElementById('memberFilter').addEventListener('submit', e => { e.preventDefault(); this.showList(); });
        container.querySelectorAll('[data-export]').forEach(btn => {
            btn.addEventListener('click', () => downloadExport('/members?format=' + btn.dataset.export, 'membri.' + btn.dataset.export));
        });
        container.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti membru?')) return;
                await api.delete('/members/' + btn.dataset.delete);
                showAlert('Membru sters.', 'success');
                this.showList();
            });
        });
    },

    async showProfile(id) {
        const res = await api.get('/members/' + id);
        const { member, participations, prizes, groups } = res;
        const container = document.getElementById('page-content');

        container.innerHTML = `
            ${pageHeader(member.nume + ' ' + member.prenume, 'Profil membru', `<a href="#/members" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <div class="grid-2">
                <div class="card"><div class="card-header">Date personale</div>
                    <p>Email: ${escapeHtml(member.email)}</p>
                    <p>Telefon: ${escapeHtml(member.telefon)}</p>
                    <p>Categorie: ${escapeHtml(member.categorie)}</p>
                    <p>Rating: ${escapeHtml(String(member.rating))}</p>
                    <p>Antrenor: ${escapeHtml(member.coach_nume || '—')}</p>
                </div>
                <div class="card"><div class="card-header">Grupuri</div>
                    ${groups.length ? groups.map(g => `<div class="list-item"><div class="list-item-title">${escapeHtml(g.denumire)}</div></div>`).join('') : '<p>Fara grupuri.</p>'}
                </div>
            </div>
            <div class="card" style="margin-top:1rem"><div class="card-header">Participari</div>
                ${participations.length ? participations.map(p => `<div class="list-item"><div class="list-item-title">${escapeHtml(p.competition_nume)}</div><div class="list-item-meta">Punctaj: ${p.punctaj}</div></div>`).join('') : '<p>Fara participari.</p>'}
            </div>
            <div class="card" style="margin-top:1rem"><div class="card-header">Premii</div>
                ${prizes.length ? prizes.map(p => `<div class="list-item"><div class="list-item-title">${escapeHtml(p.titlu)}</div></div>`).join('') : '<p>Fara premii.</p>'}
            </div>`;
    },

    async showForm(id) {
        const coachesRes = await api.get('/coaches');
        const coaches = api.items(coachesRes).filter(c => c.rol === 'antrenor');
        let member = { nume:'', prenume:'', data_nasterii:'', email:'', telefon:'', categorie:'amator', rating:0, adresa:'', coach_id:'' };
        if (id) {
            const res = await api.get('/members/' + id);
            member = res.member;
        }

        const container = document.getElementById('page-content');
        container.innerHTML = `
            ${pageHeader(id ? 'Editare membru' : 'Membru nou', '', `<a href="#/members" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="memberForm">
                <div class="form-grid">
                    ${labeledField('Nume', `<input class="input" name="nume" value="${escapeHtml(member.nume)}" required>`)}
                    ${labeledField('Prenume', `<input class="input" name="prenume" value="${escapeHtml(member.prenume)}" required>`)}
                    ${labeledField('Data nasterii', `<input class="input" name="data_nasterii" type="date" value="${escapeHtml(member.data_nasterii)}" required>`)}
                    ${labeledField('Email', `<input class="input" name="email" type="email" value="${escapeHtml(member.email)}" required>`)}
                    ${labeledField('Telefon', `<input class="input" name="telefon" value="${escapeHtml(member.telefon)}">`)}
                    ${labeledField('Categorie', `<select class="select" name="categorie">${['junior','senior','amator','profesionist'].map(c => `<option value="${c}" ${member.categorie===c?'selected':''}>${c}</option>`).join('')}</select>`)}
                    ${labeledField('Rating', `<input class="input" name="rating" type="number" value="${member.rating}">`)}
                    ${labeledField('Antrenor', `<select class="select" name="coach_id"><option value="">— Fara antrenor —</option>${coaches.map(c => `<option value="${c.id}" ${String(member.coach_id)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}</select>`)}
                    ${labeledField('Adresa', `<input class="input" name="adresa" value="${escapeHtml(member.adresa)}">`, true)}
                </div>
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;

        document.getElementById('memberForm').addEventListener('submit', async e => {
            e.preventDefault();
            const fd = new FormData(e.target);
            const body = Object.fromEntries(fd.entries());
            body.rating = parseInt(body.rating) || 0;
            if (id) await api.put('/members/' + id, body);
            else await api.post('/members', body);
            showAlert('Salvat cu succes.', 'success');
            location.hash = '#/members';
        });
    },

    async showImport() {
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Import membri', 'Incarca date din CSV, JSON sau XML', `<a href="#/members" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="importForm" enctype="multipart/form-data">
                ${labeledField('Format fisier', `<select class="select" name="type" id="importType">
                    <option value="csv">CSV</option>
                    <option value="json">JSON</option>
                    <option value="xml">XML</option>
                </select>`)}
                ${labeledField('Fisier', `<input type="file" name="file" class="input" accept=".csv,.json,.xml" required>`)}
                <p class="auth-msg">CSV: header cu nume, prenume, email, etc. JSON/XML: array de obiecte member.</p>
                <button class="btn btn-primary form-actions">Importa</button>
            </form>
            <div id="importResult" class="auth-msg"></div>`;

        document.getElementById('importForm').addEventListener('submit', async e => {
            e.preventDefault();
            const fd = new FormData(e.target);
            try {
                const res = await api.upload('/members/imports/file', fd);
                document.getElementById('importResult').textContent = 'Import reusit (' + res.imported + ' membri)';
                showAlert('Import reusit.', 'success');
            } catch (err) {
                document.getElementById('importResult').textContent = err.message;
            }
        });
    }
};
