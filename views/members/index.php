<div class="page-header">
    <div>
        <h1 class="page-title">Membri</h1>
        <p class="page-subtitle">Gestioneaza membrii clubului</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=members/import" class="btn btn-secondary btn-sm">Import</a>
        <div class="btn-group">
            <a href="index.php?route=members/export&format=csv" class="btn btn-secondary btn-sm">CSV</a>
            <a href="index.php?route=members/export&format=json" class="btn btn-secondary btn-sm">JSON</a>
            <a href="index.php?route=members/export&format=xml" class="btn btn-secondary btn-sm">XML</a>
        </div>
        <a href="index.php?route=members/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<form method="GET" class="filter-bar">
    <input type="hidden" name="route" value="members/index">
    <input type="text" name="search" class="input" placeholder="Cauta nume, email..." value="<?= e($search) ?>" style="flex:1;min-width:180px">
    <select name="categorie" class="select" style="width:auto;min-width:160px">
        <option value="">Toate categoriile</option>
        <?php foreach (['junior','senior','amator','profesionist'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $categorie === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-secondary btn-sm">Filtreaza</button>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nume</th><th>Email</th><th>Telefon</th><th>Categorie</th><th>Rating</th><th>Antrenor</th><th>Actiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
            <tr>
                <td><strong><?= e($m['nume'] . ' ' . $m['prenume']) ?></strong></td>
                <td><?= e($m['email']) ?></td>
                <td><?= e($m['telefon']) ?></td>
                <td><span class="badge"><?= e($m['categorie']) ?></span></td>
                <td><?= e((string)$m['rating']) ?></td>
                <td><?= e($m['coach_nume'] ?? '—') ?></td>
                <td class="actions">
                    <a href="index.php?route=members/show&id=<?= $m['id'] ?>" class="btn btn-ghost btn-sm">Profil</a>
                    <a href="index.php?route=prizes/byMember&member_id=<?= $m['id'] ?>" class="btn btn-ghost btn-sm">Premii</a>
                    <a href="index.php?route=members/edit&id=<?= $m['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=members/delete&id=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
