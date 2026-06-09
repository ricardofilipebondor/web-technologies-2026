<div class="page-header">
    <div>
        <h1 class="page-title">Clasamente</h1>
        <p class="page-subtitle">Clasament automat per competitie</p>
    </div>
</div>

<form method="GET" class="filter-bar">
    <input type="hidden" name="route" value="rankings/index">
    <select name="competition_id" class="select" style="flex:1" required>
        <option value="">Selecteaza concurs...</option>
        <?php foreach ($competitions as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $competitionId == $c['id'] ? 'selected' : '' ?>><?= e($c['nume']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-primary btn-sm">Genereaza</button>
</form>

<?php if ($competition): ?>
    <div class="page-header" style="margin-top:8px">
        <div>
            <h2 class="page-title" style="font-size:17px"><?= e($competition['nume']) ?></h2>
            <p class="page-subtitle">Sortat dupa punctaj descrescator</p>
        </div>
        <div class="toolbar">
            <div class="btn-group">
                <a href="index.php?route=rankings/export&competition_id=<?= $competitionId ?>&format=csv" class="btn btn-secondary btn-sm">CSV</a>
                <a href="index.php?route=rankings/export&competition_id=<?= $competitionId ?>&format=json" class="btn btn-secondary btn-sm">JSON</a>
                <a href="index.php?route=rankings/export&competition_id=<?= $competitionId ?>&format=xml" class="btn btn-secondary btn-sm">XML</a>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Loc</th><th>Participant</th><th>Punctaj</th></tr></thead>
            <tbody>
                <?php $loc = 1; foreach ($ranking as $r): ?>
                <tr>
                    <td><strong><?= $loc++ ?></strong></td>
                    <td><?= e($r['nume'] . ' ' . $r['prenume']) ?></td>
                    <td><?= e((string)$r['punctaj']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
