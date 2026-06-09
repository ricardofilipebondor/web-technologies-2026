<div class="page-header">
    <div>
        <h1 class="page-title">Premii</h1>
        <p class="page-subtitle">Istoric premii acordate</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=prizes/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Titlu</th><th>Membru</th><th>Concurs</th><th>Data</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($prizes as $p): ?>
            <tr>
                <td><strong><?= e($p['titlu']) ?></strong></td>
                <td><?= e($p['member_nume'] . ' ' . $p['member_prenume']) ?></td>
                <td><?= e($p['competition_nume'] ?? '—') ?></td>
                <td><?= e($p['data_acordare']) ?></td>
                <td class="actions">
                    <a href="index.php?route=prizes/edit&id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=prizes/delete&id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
