<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Privire de ansamblu asupra clubului</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Membri</div>
        <div class="stat-value"><?= $memberCount ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Antrenori</div>
        <div class="stat-value"><?= $coachCount ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Concursuri</div>
        <div class="stat-value"><?= $competitionCount ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Activitati</div>
        <div class="stat-value"><?= $activityCount ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Deplasari</div>
        <div class="stat-value"><?= $tripCount ?></div>
    </div>
</div>

<div class="grid-3">
    <div class="card">
        <div class="card-header">Ultimele concursuri</div>
        <?php foreach ($recentCompetitions as $c): ?>
            <div class="list-item">
                <div>
                    <div class="list-item-title"><?= e($c['nume']) ?></div>
                    <div class="list-item-meta"><?= e($c['locatie']) ?></div>
                </div>
                <span class="badge"><?= e($c['data']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <div class="card-header">Ultimele activitati</div>
        <?php foreach ($recentActivities as $a): ?>
            <div class="list-item">
                <div>
                    <div class="list-item-title"><?= e($a['titlu']) ?></div>
                    <div class="list-item-meta"><?= e($a['data_start']) ?> · <?= e($a['hall_name']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <div class="card-header">Ultimele premii</div>
        <?php foreach ($recentPrizes as $p): ?>
            <div class="list-item">
                <div>
                    <div class="list-item-title"><?= e($p['titlu']) ?></div>
                    <div class="list-item-meta"><?= e($p['nume'] . ' ' . $p['prenume']) ?></div>
                </div>
                <span class="badge"><?= e($p['data_acordare']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">Membri (Ajax)</div>
    <div id="api-live-content" class="card-body">Se incarca...</div>
</div>
<script src="assets/js/microservices.js"></script>
