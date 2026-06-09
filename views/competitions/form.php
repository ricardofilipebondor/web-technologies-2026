<div class="page-header">
    <div>
        <h1 class="page-title"><?= $competition ? 'Editare concurs' : 'Concurs nou' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Nume *</label>
            <input type="text" name="nume" class="input" value="<?= e($competition['nume'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Locatie *</label>
            <input type="text" name="locatie" class="input" value="<?= e($competition['locatie'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Data *</label>
            <input type="date" name="data" class="input" value="<?= e($competition['data'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Tip *</label>
            <select name="tip" class="select" required>
                <option value="online" <?= ($competition['tip'] ?? '') === 'online' ? 'selected' : '' ?>>Online</option>
                <option value="fizic" <?= ($competition['tip'] ?? '') === 'fizic' ? 'selected' : '' ?>>Fizic</option>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Domeniu *</label>
            <select name="domeniu" class="select" required>
                <option value="local" <?= ($competition['domeniu'] ?? 'local') === 'local' ? 'selected' : '' ?>>Local</option>
                <option value="international" <?= ($competition['domeniu'] ?? '') === 'international' ? 'selected' : '' ?>>International</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=competitions/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
