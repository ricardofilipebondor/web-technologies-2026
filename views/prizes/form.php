<div class="page-header">
    <div>
        <h1 class="page-title"><?= $prize ? 'Editare premiu' : 'Premiu nou' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Titlu *</label>
            <input type="text" name="titlu" class="input" value="<?= e($prize['titlu'] ?? '') ?>" required>
        </div>
        <div class="form-field full">
            <label class="form-label">Descriere</label>
            <textarea name="descriere" class="textarea"><?= e($prize['descriere'] ?? '') ?></textarea>
        </div>
        <div class="form-field">
            <label class="form-label">Membru *</label>
            <select name="member_id" class="select" required>
                <?php foreach ($members as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($prize['member_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                        <?= e($m['nume'] . ' ' . $m['prenume']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Concurs</label>
            <select name="competition_id" class="select">
                <option value="">—</option>
                <?php foreach ($competitions as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($prize['competition_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['nume']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Data acordare *</label>
            <input type="date" name="data_acordare" class="input" value="<?= e($prize['data_acordare'] ?? '') ?>" required>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=prizes/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
