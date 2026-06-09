<?php

class CoachesPlugin implements PluginInterface
{
    public function getId(): string { return 'coaches'; }
    public function getName(): string { return 'Coaches Service'; }
    public function getMenuLabel(): string { return 'Antrenori'; }
    public function getDefaultRoute(): string { return 'coaches/index'; }
    public function getServiceName(): string { return 'coaches'; }
}
