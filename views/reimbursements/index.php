<div class="page-header">
    <div>
        <h1 class="page-title">Deconturi</h1>
        <p class="page-subtitle">Rapoarte financiare per deplasare</p>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Destinatie</th><th>Plecare</th><th>Intoarcere</th><th>Total</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($trips as $t): ?>
            <tr>
                <td><strong><?= e($t['destinatie']) ?></strong></td>
                <td><?= e($t['data_plecare']) ?></td>
                <td><?= e($t['data_intoarcere']) ?></td>
                <td><?= number_format((float)$t['total_cheltuieli'], 2) ?> RON</td>
                <td>
                    <a href="index.php?route=reimbursements/show&id=<?= $t['id'] ?>" class="btn btn-primary btn-sm">Raport</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
