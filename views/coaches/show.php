<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($coach['nume']) ?></h1>
        <p class="page-subtitle"><span class="badge"><?= e($coach['rol']) ?></span></p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=coaches/edit&id=<?= $coach['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
        <a href="index.php?route=coaches/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<div class="card" style="max-width:480px">
    <div class="card-body detail-list">
        <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><?= e($coach['email']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Telefon</span><span class="detail-value"><?= e($coach['telefon']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Specializare</span><span class="detail-value"><?= e($coach['specializare']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Disponibilitate</span><span class="detail-value"><?= e($coach['disponibilitate']) ?></span></div>
    </div>
</div>
