<div class="page-header">
    <div>
        <h1 class="page-title">Participari</h1>
        <p class="page-subtitle">Inscrieri si rezultate la concursuri</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=participations/report" class="btn btn-secondary btn-sm">Raport</a>
        <a href="index.php?route=participations/create" class="btn btn-primary btn-sm">+ Inscrie</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Membru</th><th>Concurs</th><th>Punctaj</th><th>Loc</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($participations as $p): ?>
            <tr>
                <td><strong><?= e($p['member_nume'] . ' ' . $p['member_prenume']) ?></strong></td>
                <td><?= e($p['competition_nume']) ?></td>
                <td><?= e((string)$p['punctaj']) ?></td>
                <td><?= e((string)($p['loc_obtinut'] ?? '—')) ?></td>
                <td class="actions">
                    <a href="index.php?route=participations/edit&id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=participations/delete&id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
