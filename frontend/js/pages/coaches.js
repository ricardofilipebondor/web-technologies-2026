window.pages.coaches = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const coaches = api.items(await api.get('/coaches'));
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Antrenori', 'Gestioneaza antrenorii si colaboratorii', `<a href="#/coaches/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Nume','Email','Rol','Actiuni'], coaches, c => `
                <tr>
                    <td><strong>${escapeHtml(c.nume)}</strong></td>
                    <td>${escapeHtml(c.email)}</td>
                    <td>${escapeHtml(c.rol)}</td>
                    <td class="actions">
                        <a href="#/coaches/form/${c.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${c.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await runAction(
                    () => api.delete('/coaches/' + btn.dataset.delete),
                    { success: 'Antrenor sters.', onSuccess: () => this.showList() }
                );
            });
        });
    },

    async showForm(id) {
        let coach = { nume:'', email:'', telefon:'', specializare:'', disponibilitate:'', rol:'antrenor' };
        if (id) {
            coach = await api.get('/coaches/' + id);
        }
        document.getElementById('page-content').innerHTML = `
            ${pageHeader(id ? 'Editare' : 'Antrenor nou', '', `<a href="#/coaches" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="coachForm">
                <div class="form-grid">
                    ${labeledField('Nume', `<input class="input" name="nume" value="${escapeHtml(coach.nume)}" required>`)}
                    ${labeledField('Email', `<input class="input" name="email" type="email" value="${escapeHtml(coach.email)}" required>`)}
                    ${labeledField('Telefon', `<input class="input" name="telefon" value="${escapeHtml(coach.telefon)}">`)}
                    ${labeledField('Specializare', `<input class="input" name="specializare" value="${escapeHtml(coach.specializare)}">`)}
                    ${labeledField('Disponibilitate', `<input class="input" name="disponibilitate" value="${escapeHtml(coach.disponibilitate)}">`)}
                    ${labeledField('Rol', `<select class="select" name="rol">
                        <option value="antrenor" ${coach.rol==='antrenor'?'selected':''}>Antrenor</option>
                        <option value="colaborator" ${coach.rol==='colaborator'?'selected':''}>Colaborator</option>
                    </select>`)}
                </div>
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('coachForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            await runAction(async () => {
                if (id) await api.put('/coaches/' + id, body);
                else await api.post('/coaches', body);
            }, {
                success: 'Antrenor salvat.',
                onSuccess: () => { location.hash = '#/coaches'; },
            });
        });
    }
};

window.pages.competitions = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Concursuri', 'Gestioneaza competitii', `<a href="#/competitions/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Nume','Data','Locatie','Tip','Domeniu','Actiuni'], api.items(await api.get('/competitions')), c => `
                <tr>
                    <td><strong>${escapeHtml(c.nume)}</strong></td>
                    <td>${escapeHtml(c.data)}</td>
                    <td>${escapeHtml(c.locatie)}</td>
                    <td>${escapeHtml(c.tip)}</td>
                    <td>${escapeHtml(c.domeniu)}</td>
                    <td class="actions">
                        <a href="#/competitions/form/${c.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${c.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await runAction(
                    () => api.delete('/competitions/' + btn.dataset.delete),
                    { success: 'Concurs sters.', onSuccess: () => this.showList() }
                );
            });
        });
    },

    async showForm(id) {
        let c = { nume:'', data:'', locatie:'', tip:'fizic', domeniu:'local' };
        if (id) {
            c = await api.get('/competitions/' + id);
        }
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Concurs', '', `<a href="#/competitions" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="compForm">
                <div class="form-grid">
                    ${labeledField('Nume concurs', `<input class="input" name="nume" value="${escapeHtml(c.nume)}" required>`)}
                    ${labeledField('Data', `<input class="input" name="data" type="date" value="${escapeHtml(c.data)}" required>`)}
                    ${labeledField('Locatie', `<input class="input" name="locatie" value="${escapeHtml(c.locatie)}" required>`)}
                    ${labeledField('Tip', `<select class="select" name="tip">
                        <option value="fizic" ${c.tip==='fizic'?'selected':''}>Fizic</option>
                        <option value="online" ${c.tip==='online'?'selected':''}>Online</option>
                    </select>`)}
                    ${labeledField('Domeniu', `<select class="select" name="domeniu">
                        <option value="local" ${c.domeniu==='local'?'selected':''}>Local</option>
                        <option value="international" ${c.domeniu==='international'?'selected':''}>International</option>
                    </select>`)}
                </div>
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('compForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            await runAction(async () => {
                if (id) await api.put('/competitions/' + id, body);
                else await api.post('/competitions', body);
            }, {
                success: 'Concurs salvat.',
                onSuccess: () => { location.hash = '#/competitions'; },
            });
        });
    }
};
