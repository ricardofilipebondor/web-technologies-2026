<div class="page-header">
    <div>
        <h1 class="page-title"><?= $group ? 'Editare grupa' : 'Grupa noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Denumire *</label>
            <input type="text" name="denumire" class="input" value="<?= e($group['denumire'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Nivel *</label>
            <input type="text" name="nivel" class="input" value="<?= e($group['nivel'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Antrenor</label>
            <select name="coach_id" class="select">
                <option value="">—</option>
                <?php foreach ($coaches as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($group['coach_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nume']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=groups/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
