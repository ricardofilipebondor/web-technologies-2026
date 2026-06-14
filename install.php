<?php

require_once __DIR__ . '/database/db.php';

$sqlFile = __DIR__ . '/database/schema/database.sql';

if (!file_exists($sqlFile)) {
    die('Fisierul database/schema/database.sql nu a fost gasit.');
}

$dbPath = __DIR__ . '/database/esc.sqlite';

if (!is_dir(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0755, true);
}

if (file_exists($dbPath)) {
    unlink($dbPath);
}

$lines = explode("\n", file_get_contents($sqlFile));
$cleaned = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
        continue;
    }
    $cleaned[] = $line;
}

$sql = implode("\n", $cleaned);
$statements = preg_split('/;\s*\n/', $sql);

try {
    $pdo = getDatabaseConnection();

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }

    echo '<h2>Baza de date a fost creata cu succes!</h2>';
    echo '<p>Cont administrator:</p>';
    echo '<ul>';
    echo '<li><strong>admin</strong> / <strong>admin pass</strong></li>';
    echo '</ul>';
    echo '<p>Alte conturi se creeaza prin pagina de <a href="/frontend/index.html">Inregistrare</a>.</p>';
    echo '<p><a href="/frontend/index.html">Mergi la aplicatie</a></p>';
} catch (PDOException $e) {
    echo '<h2>Eroare la instalare</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
