<div class="page-header">
    <div>
        <h1 class="page-title">Grupe</h1>
        <p class="page-subtitle">Grupuri de antrenament</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=groups/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Denumire</th><th>Nivel</th><th>Antrenor</th><th>Membri</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($groups as $g): ?>
            <tr>
                <td><strong><?= e($g['denumire']) ?></strong></td>
                <td><span class="badge"><?= e($g['nivel']) ?></span></td>
                <td><?= e($g['coach_nume'] ?? '—') ?></td>
                <td><?= e((string)$g['member_count']) ?></td>
                <td class="actions">
                    <a href="index.php?route=groups/members&id=<?= $g['id'] ?>" class="btn btn-ghost btn-sm">Membri</a>
                    <a href="index.php?route=groups/edit&id=<?= $g['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=groups/delete&id=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
