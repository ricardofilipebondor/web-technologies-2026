<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($team['denumire']) ?></h1>
        <p class="page-subtitle">Membrii echipei</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=teams/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<form method="POST" action="index.php?route=teams/addMember" class="filter-bar">
    <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
    <select name="member_id" class="select" style="flex:1" required>
        <option value="">Selecteaza membru...</option>
        <?php foreach ($available as $m): ?>
            <option value="<?= $m['id'] ?>"><?= e($m['nume'] . ' ' . $m['prenume']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-primary btn-sm">Adauga</button>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nume</th><th>Categorie</th><th>Rating</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($members as $m): ?>
            <tr>
                <td><strong><?= e($m['nume'] . ' ' . $m['prenume']) ?></strong></td>
                <td><span class="badge"><?= e($m['categorie']) ?></span></td>
                <td><?= e((string)$m['rating']) ?></td>
                <td>
                    <a href="index.php?route=teams/removeMember&team_id=<?= $team['id'] ?>&member_id=<?= $m['id'] ?>"
                       class="btn btn-danger btn-sm" onclick="return confirm('Eliminati?')">Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
