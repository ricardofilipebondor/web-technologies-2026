<div class="page-header">
    <div>
        <h1 class="page-title">Premii — <?= e($competition['nume'] ?? '') ?></h1>
        <p class="page-subtitle">Istoric premii competitie</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=competitions/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Titlu</th><th>Membru</th><th>Data</th><th>Descriere</th></tr></thead>
        <tbody>
            <?php foreach ($prizes as $p): ?>
            <tr>
                <td><strong><?= e($p['titlu']) ?></strong></td>
                <td><?= e($p['nume'] . ' ' . $p['prenume']) ?></td>
                <td><?= e($p['data_acordare']) ?></td>
                <td><?= e($p['descriere'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
