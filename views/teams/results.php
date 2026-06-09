<div class="page-header">
    <div>
        <h1 class="page-title">Rezultate echipa — <?= e($team['denumire']) ?></h1>
        <p class="page-subtitle">Istoric performante de echipa</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=teams/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<form method="POST" action="index.php?route=teams/addResult" class="filter-bar">
    <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
    <select name="competition_id" class="select" style="flex:1" required>
        <option value="">Selecteaza competitie...</option>
        <?php foreach ($competitions as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['nume']) ?> (<?= e($c['domeniu']) ?>)</option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.5" name="punctaj_total" class="input" style="width:100px" placeholder="Punctaj" required>
    <input type="number" name="loc_obtinut" class="input" style="width:80px" placeholder="Loc">
    <input type="text" name="observatii" class="input" style="flex:1" placeholder="Observatii">
    <button class="btn btn-primary btn-sm">Adauga</button>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Competitie</th><th>Data</th><th>Punctaj total</th><th>Loc</th><th>Observatii</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($results as $r): ?>
            <tr>
                <td><strong><?= e($r['competition_nume']) ?></strong></td>
                <td><?= e($r['data']) ?></td>
                <td><?= e((string)$r['punctaj_total']) ?></td>
                <td><?= e((string)($r['loc_obtinut'] ?? '—')) ?></td>
                <td><?= e($r['observatii'] ?? '') ?></td>
                <td>
                    <a href="index.php?route=teams/deleteResult&id=<?= $team['id'] ?>&result_id=<?= $r['id'] ?>"
                       class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
