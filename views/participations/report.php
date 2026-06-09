<div class="page-header">
    <div>
        <h1 class="page-title">Raport participari</h1>
        <p class="page-subtitle">Participanti si rezultate per competitie</p>
    </div>
</div>

<form method="GET" class="filter-bar">
    <input type="hidden" name="route" value="participations/report">
    <select name="competition_id" class="select" style="flex:1" required>
        <option value="">Selecteaza competitie...</option>
        <?php foreach ($competitions as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $competitionId == $c['id'] ? 'selected' : '' ?>>
                <?= e($c['nume']) ?> (<?= e($c['domeniu']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-primary btn-sm">Genereaza raport</button>
</form>

<?php if ($competition): ?>
    <div class="page-header" style="margin-top:8px">
        <div>
            <h2 class="page-title" style="font-size:17px"><?= e($competition['nume']) ?></h2>
            <p class="page-subtitle"><?= e($competition['locatie']) ?> · <?= e($competition['data']) ?> · <?= e($competition['domeniu']) ?></p>
        </div>
        <div class="toolbar">
            <div class="btn-group">
                <a href="index.php?route=participations/exportReport&competition_id=<?= $competitionId ?>&format=csv" class="btn btn-secondary btn-sm">CSV</a>
                <a href="index.php?route=participations/exportReport&competition_id=<?= $competitionId ?>&format=json" class="btn btn-secondary btn-sm">JSON</a>
                <a href="index.php?route=participations/exportReport&competition_id=<?= $competitionId ?>&format=xml" class="btn btn-secondary btn-sm">XML</a>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Participant</th><th>Categorie</th><th>Rating</th><th>Punctaj</th><th>Loc</th></tr></thead>
            <tbody>
                <?php foreach ($participations as $p): ?>
                <tr>
                    <td><strong><?= e($p['member_nume'] . ' ' . $p['member_prenume']) ?></strong></td>
                    <td><span class="badge"><?= e($p['categorie']) ?></span></td>
                    <td><?= e((string)$p['rating']) ?></td>
                    <td><?= e((string)$p['punctaj']) ?></td>
                    <td><?= e((string)($p['loc_obtinut'] ?? '—')) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
