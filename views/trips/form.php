<div class="page-header">
    <div>
        <h1 class="page-title"><?= $trip ? 'Editare deplasare' : 'Deplasare noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Destinatie *</label>
            <input type="text" name="destinatie" class="input" value="<?= e($trip['destinatie'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Data plecare *</label>
            <input type="date" name="data_plecare" class="input" value="<?= e($trip['data_plecare'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Data intoarcere *</label>
            <input type="date" name="data_intoarcere" class="input" value="<?= e($trip['data_intoarcere'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Echipa reprezentativa</label>
            <select name="team_id" class="select">
                <option value="">—</option>
                <?php foreach ($teams as $tm): ?>
                    <option value="<?= $tm['id'] ?>" <?= ($trip['team_id'] ?? '') == $tm['id'] ? 'selected' : '' ?>><?= e($tm['denumire']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field full">
            <label class="form-label">Scop</label>
            <textarea name="scop" class="textarea"><?= e($trip['scop'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=trips/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
