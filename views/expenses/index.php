<div class="page-header">
    <div>
        <h1 class="page-title">Cheltuieli</h1>
        <p class="page-subtitle">Costuri asociate deplasarilor</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=expenses/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Deplasare</th><th>Tip</th><th>Suma</th><th>Observatii</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($expenses as $e): ?>
            <tr>
                <td><strong><?= e($e['trip_destinatie']) ?></strong></td>
                <td><span class="badge"><?= e($e['tip']) ?></span></td>
                <td><?= number_format((float)$e['suma'], 2) ?> RON</td>
                <td><?= e($e['observatii'] ?? '') ?></td>
                <td class="actions">
                    <a href="index.php?route=expenses/edit&id=<?= $e['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=expenses/delete&id=<?= $e['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
