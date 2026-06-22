window.pages.halls = {
    async render(sub) {
        if (sub[0] === 'slots' && sub[1]) return this.showSlots(sub[1]);
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/halls');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Sali', 'Sali de sah', `<a href="#/halls/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Denumire','Capacitate','Actiuni'], res.data, h => `
                <tr>
                    <td><strong>${escapeHtml(h.denumire)}</strong></td>
                    <td>${h.capacitate}</td>
                    <td class="actions">
                        <a href="#/halls/slots/${h.id}" class="btn btn-ghost btn-sm">Intervale</a>
                        <a href="#/halls/form/${h.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${h.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/halls/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        let h = { denumire:'', capacitate:10, dotari:'' };
        if (id) h = (await api.get('/halls')).data.find(x => String(x.id) === String(id)) || h;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Sala', '', `<a href="#/halls" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="hallForm">
                ${labeledField('Denumire', `<input class="input" name="denumire" value="${escapeHtml(h.denumire)}" required>`)}
                ${labeledField('Capacitate', `<input class="input" name="capacitate" type="number" value="${h.capacitate}">`)}
                ${labeledField('Dotari', `<input class="input" name="dotari" value="${escapeHtml(h.dotari)}">`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('hallForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            body.capacitate = parseInt(body.capacitate) || 0;
            if (id) await api.put('/halls/' + id, body);
            else await api.post('/halls', body);
            location.hash = '#/halls';
        });
    },

    async showSlots(id) {
        const res = await api.get('/halls/' + id + '/slots');
        const { hall, slots } = res.data;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Intervale: ' + hall.denumire, '', `<a href="#/halls" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="filter-bar" id="slotForm">
                ${filterField('Zi', `<select class="select" name="zi_saptamana">
                    ${['Luni','Marti','Miercuri','Joi','Vineri','Sambata','Duminica'].map(z => `<option value="${z}">${z}</option>`).join('')}
                </select>`)}
                ${filterField('Ora start', `<input class="input" name="ora_start" placeholder="ex: 10:00">`)}
                ${filterField('Ora end', `<input class="input" name="ora_end" placeholder="ex: 12:00">`)}
                <button class="btn btn-primary btn-sm">Adauga</button>
            </form>
            ${renderTable(['Zi','Start','End','Actiuni'], slots, s => `
                <tr><td>${escapeHtml(s.zi_saptamana)}</td><td>${escapeHtml(s.ora_start)}</td><td>${escapeHtml(s.ora_end)}</td>
                <td><button class="btn btn-danger btn-sm" data-del="${s.id}">Sterge</button></td></tr>`)}
        `;
        document.getElementById('slotForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            body.hall_id = id;
            await api.post('/halls/slots', body);
            this.showSlots(id);
        });
        document.querySelectorAll('[data-del]').forEach(btn => {
            btn.addEventListener('click', async () => {
                await api.delete('/halls/slots/' + btn.dataset.del);
                this.showSlots(id);
            });
        });
    }
};

window.pages.activities = {
    async render(sub) {
        if (sub[0] === 'form') return this.showForm(sub[1]);
        return this.showList();
    },

    async showList() {
        const res = await api.get('/activities');
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Activitati', 'Antrenamente si evenimente', `<a href="#/activities/form" class="btn btn-primary btn-sm">+ Adauga</a>`)}
            ${renderTable(['Titlu','Tip','Start','Sala','Antrenor','Actiuni'], res.data, a => `
                <tr>
                    <td><strong>${escapeHtml(a.titlu)}</strong></td>
                    <td>${escapeHtml(a.tip)}</td>
                    <td>${escapeHtml(a.data_start)}</td>
                    <td>${escapeHtml(a.hall_name || '')}</td>
                    <td>${escapeHtml(a.coach_nume || '')}</td>
                    <td class="actions">
                        <a href="#/activities/form/${a.id}" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="${a.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti?')) return;
                await api.delete('/activities/' + btn.dataset.delete);
                this.showList();
            });
        });
    },

    async showForm(id) {
        const meta = (await api.get('/activities/meta')).data;
        let a = { titlu:'', tip:'antrenament', data_start:'', data_end:'', hall_id:'', coach_id:'' };
        if (id) a = (await api.get('/activities')).data.find(x => String(x.id) === String(id)) || a;
        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Activitate', '', `<a href="#/activities" class="btn btn-secondary btn-sm">Inapoi</a>`)}
            <form class="card card-body" id="actForm">
                ${labeledField('Titlu', `<input class="input" name="titlu" value="${escapeHtml(a.titlu)}" required>`)}
                ${labeledField('Tip', `<select class="select" name="tip">
                    ${['antrenament','curs','workshop','simultan'].map(t => `<option value="${t}" ${a.tip===t?'selected':''}>${t}</option>`).join('')}
                </select>`)}
                ${labeledField('Data start', `<input class="input" name="data_start" type="datetime-local" value="${escapeHtml((a.data_start||'').replace(' ','T').slice(0,16))}">`)}
                ${labeledField('Data end', `<input class="input" name="data_end" type="datetime-local" value="${escapeHtml((a.data_end||'').replace(' ','T').slice(0,16))}">`)}
                ${labeledField('Sala', `<select class="select" name="hall_id">${meta.halls.map(h => `<option value="${h.id}" ${String(a.hall_id)===String(h.id)?'selected':''}>${escapeHtml(h.denumire)}</option>`).join('')}</select>`)}
                ${labeledField('Antrenor', `<select class="select" name="coach_id">${meta.coaches.map(c => `<option value="${c.id}" ${String(a.coach_id)===String(c.id)?'selected':''}>${escapeHtml(c.nume)}</option>`).join('')}</select>`)}
                <button class="btn btn-primary form-actions">Salveaza</button>
            </form>`;
        document.getElementById('actForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (id) await api.put('/activities/' + id, body);
            else await api.post('/activities', body);
            location.hash = '#/activities';
        });
    }
};
