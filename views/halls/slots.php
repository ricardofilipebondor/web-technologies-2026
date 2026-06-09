<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($hall['denumire']) ?></h1>
        <p class="page-subtitle">Intervale orare disponibile (time slots)</p>
    </div>
    <div class="toolbar">
        <a href="index.php?route=halls/index" class="btn btn-ghost btn-sm">Inapoi</a>
    </div>
</div>

<form method="POST" action="index.php?route=halls/addSlot" class="filter-bar">
    <input type="hidden" name="hall_id" value="<?= $hall['id'] ?>">
    <select name="zi_saptamana" class="select" style="width:auto" required>
        <?php foreach (['Luni','Marti','Miercuri','Joi','Vineri','Sambata','Duminica'] as $zi): ?>
            <option value="<?= $zi ?>"><?= $zi ?></option>
        <?php endforeach; ?>
    </select>
    <input type="time" name="ora_start" class="input" style="width:auto" required>
    <input type="time" name="ora_end" class="input" style="width:auto" required>
    <button class="btn btn-primary btn-sm">Adauga interval</button>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Zi</th><th>Ora start</th><th>Ora end</th><th>Actiuni</th></tr></thead>
        <tbody>
            <?php foreach ($slots as $s): ?>
            <tr>
                <td><?= e($s['zi_saptamana']) ?></td>
                <td><?= e($s['ora_start']) ?></td>
                <td><?= e($s['ora_end']) ?></td>
                <td>
                    <a href="index.php?route=halls/deleteSlot&hall_id=<?= $hall['id'] ?>&slot_id=<?= $s['id'] ?>"
                       class="btn btn-danger btn-sm" onclick="return confirm('Stergeti?')">Sterge</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
