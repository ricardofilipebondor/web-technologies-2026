const API_BASE = '../api/auth';

const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('loginUsername').value;
        const password = document.getElementById('loginPassword').value;
        
        try {
            const response = await fetch(`${API_BASE}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('jwt_token', data.token);
                window.location.href = 'dashboard.html';
            } else {
                document.getElementById('loginMessage').innerText = data.error;
            }
        } catch (err) {
            console.error('Login failed', err);
        }
    });
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('regUsername').value;
        const email = document.getElementById('regEmail').value;
        const password = document.getElementById('regPassword').value;
        
        try {
            const response = await fetch(`${API_BASE}/register.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
            });
            const data = await response.json();
            
            if (response.ok) {
                document.getElementById('registerMessage').innerText = 'Registration successful. You can now login.';
                registerForm.reset();
            } else {
                document.getElementById('registerMessage').innerText = data.error;
            }
        } catch (err) {
            console.error('Registration failed', err);
        }
    });
}

async function checkAuthAndLoadProfile() {
    const token = localStorage.getItem('jwt_token');
    
    if (!token) {
        window.location.href = 'index.html';
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/me.php`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (response.ok) {
            const user = await response.json();
            const userInfoDiv = document.getElementById('userInfo');
            userInfoDiv.innerHTML = `
                <p><strong>Username:</strong> ${user.username}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Role:</strong> ${user.role_name}</p>
            `;
            
            const logoutBtn = document.getElementById('logoutBtn');
            logoutBtn.style.display = 'block';
            logoutBtn.addEventListener('click', () => {
                localStorage.removeItem('jwt_token');
                window.location.href = 'index.html';
            });
        } else {
            localStorage.removeItem('jwt_token');
            window.location.href = 'index.html';
        }
    } catch (err) {
        window.location.href = 'index.html';
    }
}