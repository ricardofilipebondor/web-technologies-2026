<?php
require_once __DIR__ . '/../../plugins/PluginManager.php';
$menuItems = PluginManager::getMenuItems();
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-label">Module (Plugins)</div>
    <ul class="sidebar-nav">
        <?php foreach ($menuItems as $item): ?>
            <?php if (userCanAccess($item['module'])): ?>
                <li>
                    <a class="sidebar-link <?= ($module === $item['module']) ? 'active' : '' ?>"
                       href="index.php?route=<?= e($item['route']) ?>">
                        <?= e($item['label']) ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</aside>
<main class="main-content">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
