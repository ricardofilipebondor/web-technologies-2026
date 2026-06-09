<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($trip['destinatie']) ?></h1>
        <p class="page-subtitle">Membri echipa reprezentativa la deplasare</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=trips/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<form method="POST" action="index.php?route=trips/addMember" class="filter-bar">
    <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
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
        <thead><tr><th>Nume</th><th>Categorie</th><th>Email</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($members as $m): ?>
            <tr>
                <td><strong><?= e($m['nume'] . ' ' . $m['prenume']) ?></strong></td>
                <td><span class="badge"><?= e($m['categorie']) ?></span></td>
                <td><?= e($m['email']) ?></td>
                <td>
                    <a href="index.php?route=trips/removeMember&trip_id=<?= $trip['id'] ?>&member_id=<?= $m['id'] ?>"
                       class="btn btn-danger btn-sm" onclick="return confirm('Eliminati?')">Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
