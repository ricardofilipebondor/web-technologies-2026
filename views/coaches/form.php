<div class="page-header">
    <div>
        <h1 class="page-title"><?= $coach ? 'Editare' : 'Adaugare' ?></h1>
        <p class="page-subtitle">Antrenor sau colaborator</p>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field">
            <label class="form-label">Nume *</label>
            <input type="text" name="nume" class="input" value="<?= e($coach['nume'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="input" value="<?= e($coach['email'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Telefon</label>
            <input type="text" name="telefon" class="input" value="<?= e($coach['telefon'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label class="form-label">Rol *</label>
            <select name="rol" class="select" required>
                <option value="antrenor" <?= ($coach['rol'] ?? '') === 'antrenor' ? 'selected' : '' ?>>Antrenor</option>
                <option value="colaborator" <?= ($coach['rol'] ?? '') === 'colaborator' ? 'selected' : '' ?>>Colaborator</option>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Specializare</label>
            <input type="text" name="specializare" class="input" value="<?= e($coach['specializare'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label class="form-label">Disponibilitate</label>
            <input type="text" name="disponibilitate" class="input" value="<?= e($coach['disponibilitate'] ?? '') ?>">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=coaches/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
