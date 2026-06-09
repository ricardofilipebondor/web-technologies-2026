<div class="page-header">
    <div>
        <h1 class="page-title">Antrenori & Colaboratori</h1>
        <p class="page-subtitle">Echipa clubului</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=coaches/create" class="btn btn-primary btn-sm">+ Adauga</a>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nume</th><th>Email</th><th>Telefon</th><th>Specializare</th><th>Rol</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($coaches as $c): ?>
            <tr>
                <td><strong><?= e($c['nume']) ?></strong></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['telefon']) ?></td>
                <td><?= e($c['specializare']) ?></td>
                <td><span class="badge"><?= e($c['rol']) ?></span></td>
                <td class="actions">
                    <a href="index.php?route=coaches/show&id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Vizualizeaza</a>
                    <a href="index.php?route=coaches/edit&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="index.php?route=coaches/delete&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
