window.pages.admin = {
    async render() {
        const usersRes = await api.get('/users');
        const rolesRes = await api.get('/roles');
        const users = api.items(usersRes);
        const roles = api.items(rolesRes);

        document.getElementById('page-content').innerHTML = `
            ${pageHeader('Administrare', 'Gestionare utilizatori si roluri')}
            <div class="card card-body" style="margin-bottom:1.5rem">
                <h3 style="margin-top:0">Adauga utilizator</h3>
                <form id="adminUserForm" class="form-grid">
                    ${labeledField('Username', `<input class="input" name="username" required>`)}
                    ${labeledField('Email', `<input class="input" name="email" type="email" required>`)}
                    ${labeledField('Parola', `<input class="input" name="password" type="password" required>`)}
                    ${labeledField('Rol', `<select class="select" name="role">${roles.map(r => `<option value="${r.role_name}">${escapeHtml(r.role_name)}</option>`).join('')}</select>`)}
                    <button class="btn btn-primary" style="grid-column:1/-1;margin-top:0.5rem">Creeaza</button>
                </form>
            </div>
            ${renderTable(['Username','Email','Rol','Actiuni'], users, u => `
                <tr>
                    <td><strong>${escapeHtml(u.username)}</strong></td>
                    <td>${escapeHtml(u.email)}</td>
                    <td>
                        <select class="select select-sm" data-role-user="${u.id}" style="width:auto">
                            ${roles.map(r => `<option value="${r.role_name}" ${u.role_name===r.role_name?'selected':''}>${escapeHtml(r.role_name)}</option>`).join('')}
                        </select>
                    </td>
                    <td class="actions">
                        <button class="btn btn-secondary btn-sm" data-save-role="${u.id}">Salveaza rol</button>
                        <button class="btn btn-danger btn-sm" data-delete="${u.id}">Sterge</button>
                    </td>
                </tr>`)}
        `;

        document.getElementById('adminUserForm').addEventListener('submit', async e => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            await runAction(
                () => api.post('/users', body),
                { success: 'Utilizator creat.', onSuccess: () => this.render() }
            );
        });

        document.querySelectorAll('[data-save-role]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.saveRole;
                const role = document.querySelector(`[data-role-user="${id}"]`).value;
                await runAction(
                    () => api.put('/users/' + id, { role }),
                    { success: 'Rol actualizat.' }
                );
            });
        });

        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirmAction('Stergeti utilizatorul?')) return;
                await runAction(
                    () => api.delete('/users/' + btn.dataset.delete),
                    { success: 'Utilizator sters.', onSuccess: () => this.render() }
                );
            });
        });
    }
};
