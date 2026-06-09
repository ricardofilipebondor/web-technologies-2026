<div class="page-header">
    <div>
        <h1 class="page-title"><?= $expense ? 'Editare cheltuiala' : 'Cheltuiala noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Deplasare *</label>
            <select name="trip_id" class="select" required>
                <?php foreach ($trips as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($expense['trip_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                        <?= e($t['destinatie']) ?> (<?= e($t['data_plecare']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Tip *</label>
            <select name="tip" class="select" required>
                <?php foreach (['transport','cazare','masa'] as $tip): ?>
                    <option value="<?= $tip ?>" <?= ($expense['tip'] ?? '') === $tip ? 'selected' : '' ?>><?= ucfirst($tip) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Suma (RON) *</label>
            <input type="number" step="0.01" name="suma" class="input" value="<?= e((string)($expense['suma'] ?? '')) ?>" required>
        </div>
        <div class="form-field full">
            <label class="form-label">Observatii</label>
            <textarea name="observatii" class="textarea"><?= e($expense['observatii'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=expenses/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
