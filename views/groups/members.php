<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($group['denumire']) ?></h1>
        <p class="page-subtitle">Membrii grupei</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=groups/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<form method="POST" action="index.php?route=groups/addMember" class="filter-bar">
    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
    <select name="member_id" class="select" style="flex:1" required>
        <option value="">Selecteaza membru...</option>
        <?php foreach ($available as $m): ?>
            <option value="<?= $m['id'] ?>"><?= e($m['nume'] . ' ' . $m['prenume']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Adauga in grupa</button>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nume</th><th>Email</th><th>Categorie</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($members as $m): ?>
            <tr>
                <td><strong><?= e($m['nume'] . ' ' . $m['prenume']) ?></strong></td>
                <td><?= e($m['email']) ?></td>
                <td><span class="badge"><?= e($m['categorie']) ?></span></td>
                <td>
                    <a href="index.php?route=groups/removeMember&group_id=<?= $group['id'] ?>&member_id=<?= $m['id'] ?>"
                       class="btn btn-danger btn-sm" onclick="return confirm('Eliminati?')">Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
