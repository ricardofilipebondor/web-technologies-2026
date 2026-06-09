<div class="page-header">
    <div>
        <h1 class="page-title">Activitati</h1>
        <p class="page-subtitle">Antrenamente, cursuri, workshop-uri</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=activities/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Titlu</th><th>Tip</th><th>Start</th><th>Sfarsit</th><th>Sala</th><th>Antrenor</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($activities as $a): ?>
            <tr>
                <td><strong><?= e($a['titlu']) ?></strong></td>
                <td><span class="badge"><?= e($a['tip']) ?></span></td>
                <td><?= e($a['data_start']) ?></td>
                <td><?= e($a['data_end']) ?></td>
                <td><?= e($a['hall_name']) ?></td>
                <td><?= e($a['coach_nume']) ?></td>
                <td class="actions">
                    <a href="index.php?route=activities/edit&id=<?= $a['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=activities/delete&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
