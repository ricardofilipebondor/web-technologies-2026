async function handleLogin(e) {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const res = await api.post('/sessions', { username, password });
        api.setToken(res.access_token);
        window.location.href = 'app.html';
    } catch (err) {
        document.getElementById('loginMessage').textContent = err.message;
    }
}

async function handleRegister(e) {
    e.preventDefault();
    const username = document.getElementById('regUsername').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;
    const password_confirm = document.getElementById('regPasswordConfirm').value;
    const role = document.getElementById('regRole').value;

    try {
        await api.post('/users', { username, email, password, password_confirm, role });
        document.getElementById('registerMessage').textContent = 'Cont creat. Te poti autentifica.';
        document.getElementById('registerForm').reset();
    } catch (err) {
        document.getElementById('registerMessage').textContent = err.message;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
});
