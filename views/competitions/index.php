<div class="page-header">
    <div>
        <h1 class="page-title">Concursuri</h1>
        <p class="page-subtitle">Competitii online si fizice</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=competitions/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nume</th><th>Locatie</th><th>Data</th><th>Tip</th><th>Domeniu</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($competitions as $c): ?>
            <tr>
                <td><strong><?= e($c['nume']) ?></strong></td>
                <td><?= e($c['locatie']) ?></td>
                <td><?= e($c['data']) ?></td>
                <td><span class="badge"><?= e($c['tip']) ?></span></td>
                <td><span class="badge"><?= e($c['domeniu'] ?? 'local') ?></span></td>
                <td class="actions">
                    <a href="index.php?route=prizes/byCompetition&competition_id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Premii</a>
                    <a href="index.php?route=rankings/index&competition_id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Clasament</a>
                    <a href="index.php?route=competitions/edit&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=competitions/delete&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
