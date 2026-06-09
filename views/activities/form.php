<div class="page-header">
    <div>
        <h1 class="page-title"><?= $activity ? 'Editare activitate' : 'Activitate noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field">
            <label class="form-label">Titlu *</label>
            <input type="text" name="titlu" class="input" value="<?= e($activity['titlu'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Tip *</label>
            <select name="tip" class="select" required>
                <?php foreach (['antrenament','curs','workshop','simultan'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($activity['tip'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Data start *</label>
            <input type="datetime-local" name="data_start" class="input"
                   value="<?= e(isset($activity['data_start']) ? str_replace(' ', 'T', substr($activity['data_start'], 0, 16)) : '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Data end *</label>
            <input type="datetime-local" name="data_end" class="input"
                   value="<?= e(isset($activity['data_end']) ? str_replace(' ', 'T', substr($activity['data_end'], 0, 16)) : '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Sala *</label>
            <select name="hall_id" class="select" required>
                <?php foreach ($halls as $h): ?>
                    <option value="<?= $h['id'] ?>" <?= ($activity['hall_id'] ?? '') == $h['id'] ? 'selected' : '' ?>><?= e($h['denumire']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Antrenor *</label>
            <select name="coach_id" class="select" required>
                <?php foreach ($coaches as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($activity['coach_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nume']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=activities/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
