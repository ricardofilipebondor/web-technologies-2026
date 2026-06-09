<div class="page-header">
    <div>
        <h1 class="page-title">Raport decont</h1>
        <p class="page-subtitle"><?= e($trip['destinatie']) ?></p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=reimbursements/export&id=<?= $trip['id'] ?>&format=pdf" class="btn btn-primary btn-sm">Export PDF</a>
        <a href="index.php?route=reimbursements/export&id=<?= $trip['id'] ?>&format=csv" class="btn btn-secondary btn-sm">Export CSV</a>
        <a href="index.php?route=reimbursements/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<div class="card" style="margin-bottom:20px;max-width:560px">
    <div class="card-header">Deplasare</div>
    <div class="card-body detail-list">
        <div class="detail-row"><span class="detail-label">Destinatie</span><span class="detail-value"><?= e($trip['destinatie']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Plecare</span><span class="detail-value"><?= e($trip['data_plecare']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Intoarcere</span><span class="detail-value"><?= e($trip['data_intoarcere']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Echipa</span><span class="detail-value"><?= e($trip['team_nume'] ?? '—') ?></span></div>
        <div class="detail-row"><span class="detail-label">Scop</span><span class="detail-value"><?= e($trip['scop'] ?? '—') ?></span></div>
    </div>
</div>

<?php if (!empty($members)): ?>
<div class="card" style="margin-bottom:20px;max-width:560px">
    <div class="card-header">Membri echipa reprezentativa</div>
    <?php foreach ($members as $m): ?>
        <div class="list-item">
            <span class="list-item-title"><?= e($m['nume'] . ' ' . $m['prenume']) ?></span>
            <span class="badge"><?= e($m['categorie']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Tip</th><th>Suma</th><th>Observatii</th></tr></thead>
        <tbody>
            <?php foreach ($expenses as $e): ?>
            <tr>
                <td><span class="badge"><?= e($e['tip']) ?></span></td>
                <td><?= number_format((float)$e['suma'], 2) ?> RON</td>
                <td><?= e($e['observatii'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th>Total general</th>
                <th colspan="2"><?= number_format($total, 2) ?> RON</th>
            </tr>
        </tfoot>
    </table>
</div>
