<div class="page-header">
    <div>
        <h1 class="page-title">Echipe</h1>
        <p class="page-subtitle">Performante individuale si de echipa</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=teams/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Denumire</th><th>Descriere</th><th>Membri</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($teams as $t): ?>
            <tr>
                <td><strong><?= e($t['denumire']) ?></strong></td>
                <td><?= e($t['descriere'] ?? '') ?></td>
                <td><?= e((string)$t['member_count']) ?></td>
                <td class="actions">
                    <a href="index.php?route=teams/members&id=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Membri</a>
                    <a href="index.php?route=teams/results&id=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Rezultate</a>
                    <a href="index.php?route=teams/edit&id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=teams/delete&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
