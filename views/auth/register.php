<div class="login-page">
    <div class="login-card">
        <div class="login-logo">eSC</div>
        <p class="login-tagline">Creare cont nou</p>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?route=auth/doRegister">
            <div class="form-field" style="margin-bottom:14px">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="input" required autofocus>
            </div>
            <div class="form-field" style="margin-bottom:14px">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="input" required>
            </div>
            <div class="form-field" style="margin-bottom:14px">
                <label class="form-label">Rol</label>
                <select name="role" class="select" required>
                    <option value="antrenor">Antrenor</option>
                    <option value="responsabil_financiar">Responsabil Financiar</option>
                </select>
            </div>
            <div class="form-field" style="margin-bottom:14px">
                <label class="form-label">Parola</label>
                <input type="password" name="password" class="input" required minlength="6">
            </div>
            <div class="form-field" style="margin-bottom:20px">
                <label class="form-label">Confirma parola</label>
                <input type="password" name="password_confirm" class="input" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Inregistrare</button>
        </form>

        <div class="login-hint" style="text-align:center">
            Ai deja cont? <a href="index.php?route=auth/login" style="text-decoration:underline;color:var(--text)">Autentificare</a>
        </div>
    </div>
</div>
