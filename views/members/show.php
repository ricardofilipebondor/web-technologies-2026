<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($member['nume'] . ' ' . $member['prenume']) ?></h1>
        <p class="page-subtitle">
            <span class="badge"><?= e($member['categorie']) ?></span>
            · Rating <?= e((string)$member['rating']) ?>
            <?php if ($member['coach_nume']): ?> · Antrenor: <?= e($member['coach_nume']) ?><?php endif; ?>
        </p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=members/edit&id=<?= $member['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
        <a href="index.php?route=members/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<div class="grid-3" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">Date personale</div>
        <div class="card-body detail-list">
            <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><?= e($member['email']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Telefon</span><span class="detail-value"><?= e($member['telefon']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Data nasterii</span><span class="detail-value"><?= e($member['data_nasterii']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Adresa</span><span class="detail-value"><?= e($member['adresa'] ?? '—') ?></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Grupe</div>
        <?php if (empty($groups)): ?>
            <div class="list-item"><span class="list-item-meta">Nu este in nicio grupa</span></div>
        <?php else: ?>
            <?php foreach ($groups as $g): ?>
                <div class="list-item"><span class="list-item-title"><?= e($g['denumire']) ?></span></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-header">Nivel de joc</div>
        <div class="card-body">
            <div class="stat-value" style="font-size:22px"><?= e((string)$member['rating']) ?></div>
            <div class="stat-label">Rating ELO · <?= e($member['categorie']) ?></div>
        </div>
    </div>
</div>

<h2 class="page-title" style="font-size:17px;margin-bottom:12px">Istoric participari la competitii</h2>
<div class="table-wrap" style="margin-bottom:24px">
    <table class="data-table">
        <thead><tr><th>Competitie</th><th>Data</th><th>Domeniu</th><th>Tip</th><th>Punctaj</th><th>Loc</th></tr></thead>
        <tbody>
            <?php foreach ($participations as $p): ?>
            <tr>
                <td><strong><?= e($p['competition_nume']) ?></strong></td>
                <td><?= e($p['data']) ?></td>
                <td><span class="badge"><?= e($p['domeniu']) ?></span></td>
                <td><?= e($p['tip']) ?></td>
                <td><?= e((string)$p['punctaj']) ?></td>
                <td><?= e((string)($p['loc_obtinut'] ?? '—')) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h2 class="page-title" style="font-size:17px;margin-bottom:12px">Premii obtinute</h2>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Titlu</th><th>Competitie</th><th>Data</th></tr></thead>
        <tbody>
            <?php foreach ($prizes as $pr): ?>
            <tr>
                <td><strong><?= e($pr['titlu']) ?></strong></td>
                <td><?= e($pr['competition_nume'] ?? '—') ?></td>
                <td><?= e($pr['data_acordare']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
