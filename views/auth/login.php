<div class="login-page">
    <div class="login-card">
        <div class="login-logo">eSC</div>
        <p class="login-tagline">Chess Club Manager</p>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?route=auth/doLogin">
            <div class="form-field" style="margin-bottom:14px">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="input" required autofocus>
            </div>
            <div class="form-field" style="margin-bottom:20px">
                <label class="form-label">Parola</label>
                <input type="password" name="password" class="input" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Autentificare</button>
        </form>

        <div class="login-hint" style="text-align:center">
            Nu ai cont? <a href="index.php?route=auth/register" style="text-decoration:underline;color:var(--text)">Inregistreaza-te</a>
        </div>
    </div>
</div>
