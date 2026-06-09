<div class="page-header">
    <div>
        <h1 class="page-title"><?= $team ? 'Editare echipa' : 'Echipa noua' ?></h1>
    </div>
</div>

<form method="POST" action="index.php?route=<?= e($action) ?>" class="form-card">
    <div class="form-grid">
        <div class="form-field full">
            <label class="form-label">Denumire *</label>
            <input type="text" name="denumire" class="input" value="<?= e($team['denumire'] ?? '') ?>" required>
        </div>
        <div class="form-field full">
            <label class="form-label">Descriere</label>
            <textarea name="descriere" class="textarea"><?= e($team['descriere'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="index.php?route=teams/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
