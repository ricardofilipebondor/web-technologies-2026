<div class="page-header">
    <div>
        <h1 class="page-title"><?= $participation ? 'Editare rezultat' : 'Inscriere participant' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field">
            <label class="form-label">Membru *</label>
            <select name="member_id" class="select" required>
                <?php foreach ($members as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($participation['member_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                        <?= e($m['nume'] . ' ' . $m['prenume']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Concurs *</label>
            <select name="competition_id" class="select" required>
                <?php foreach ($competitions as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($participation['competition_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['nume']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Punctaj</label>
            <input type="number" step="0.5" name="punctaj" class="input" value="<?= e((string)($participation['punctaj'] ?? 0)) ?>">
        </div>
        <div class="form-field">
            <label class="form-label">Loc obtinut</label>
            <input type="number" name="loc_obtinut" class="input" value="<?= e((string)($participation['loc_obtinut'] ?? '')) ?>">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=participations/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
