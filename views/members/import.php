<div class="page-header">
    <div>
        <h1 class="page-title">Import membri</h1>
        <p class="page-subtitle">Incarca un fisier CSV, JSON sau XML</p>
    </div>
</div>

<form method="POST" action="index.php?route=members/doImport" enctype="multipart/form-data" class="form-card">
    <div class="form-grid">
        <div class="form-field">
            <label class="form-label">Tip fisier</label>
            <select name="type" class="select" required>
                <option value="csv">CSV</option>
                <option value="json">JSON</option>
                <option value="xml">XML</option>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Fisier</label>
            <input type="file" name="file" class="input" required>
        </div>
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin-top:12px">
        Campuri: nume, prenume, email, telefon, categorie, rating, adresa, data_nasterii, coach_id
    </p>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Importa</button>
        <a href="index.php?route=members/index" class="btn btn-secondary">Inapoi</a>
    </div>
</form>
