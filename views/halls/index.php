<div class="page-header">
    <div>
        <h1 class="page-title">Sali</h1>
        <p class="page-subtitle">Spatii de antrenament si competiție</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=halls/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Denumire</th><th>Capacitate</th><th>Dotari</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($halls as $h): ?>
            <tr>
                <td><strong><?= e($h['denumire']) ?></strong></td>
                <td><?= e((string)$h['capacitate']) ?> locuri</td>
                <td><?= e($h['dotari']) ?></td>
                <td class="actions">
                    <a href="index.php?route=halls/slots&id=<?= $h['id'] ?>" class="btn btn-ghost btn-sm">Time slots</a>
                    <a href="index.php?route=halls/edit&id=<?= $h['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=halls/delete&id=<?= $h['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
