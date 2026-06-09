<div class="page-header">
    <div>
        <h1 class="page-title">Deplasari</h1>
        <p class="page-subtitle">Calatorii si competitiile externe</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=trips/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Destinatie</th><th>Echipa</th><th>Membri</th><th>Plecare</th><th>Total</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($trips as $t): ?>
            <tr>
                <td><strong><?= e($t['destinatie']) ?></strong></td>
                <td><?= e($t['team_nume'] ?? '—') ?></td>
                <td><?= e((string)($t['member_count'] ?? 0)) ?></td>
                <td><?= e($t['data_plecare']) ?></td>
                <td><?= number_format((float)$t['total_cheltuieli'], 2) ?> RON</td>
                <td class="actions">
                    <a href="index.php?route=trips/members&id=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Membri</a>
                    <a href="index.php?route=reimbursements/show&id=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Decont</a>
                    <a href="index.php?route=trips/edit&id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=trips/delete&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
