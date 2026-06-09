<div class="page-header">
    <div>
        <h1 class="page-title"><?= $hall ? 'Editare sala' : 'Sala noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Denumire *</label>
            <input type="text" name="denumire" class="input" value="<?= e($hall['denumire'] ?? '') ?>" required>
        </div>
        <div class="form-field">
            <label class="form-label">Capacitate *</label>
            <input type="number" name="capacitate" class="input" value="<?= e((string)($hall['capacitate'] ?? '')) ?>" required>
        </div>
        <div class="form-field full">
            <label class="form-label">Dotari</label>
            <textarea name="dotari" class="textarea"><?= e($hall['dotari'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=halls/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
