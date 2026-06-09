<div class="page-header">
    <div>
        <h1 class="page-title"><?= $member ? 'Editare membru' : 'Membru nou' ?></h1>
        <p class="page-subtitle">Completeaza informatiile membrului</p>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field">
            <label class="form-label">Nume *</label>
            <input type="text" name="nume" class="input" value="<?= e($member['nume'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Prenume *</label>
            <input type="text" name="prenume" class="input" value="<?= e($member['prenume'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Data nasterii *</label>
            <input type="date" name="data_nasterii" class="input" value="<?= e($member['data_nasterii'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="input" value="<?= e($member['email'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Telefon</label>
            <input type="text" name="telefon" class="input" value="<?= e($member['telefon'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label class="form-label">Categorie *</label>
            <select name="categorie" class="select" required>
                <?php foreach (['junior','senior','amator','profesionist'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= ($member['categorie'] ?? '') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Rating</label>
            <input type="number" name="rating" class="input" value="<?= e((string)($member['rating'] ?? 0)) ?>">
        </div>
        <div class="form-field">
            <label class="form-label">Antrenor</label>
            <select name="coach_id" class="select">
                <option value="">—</option>
                <?php foreach ($coaches as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($member['coach_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nume']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field full">
            <label class="form-label">Adresa</label>
            <textarea name="adresa" class="textarea"><?= e($member['adresa'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=members/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
