<?php

class DashboardPlugin implements PluginInterface
{
    public function getId(): string { return 'dashboard'; }
    public function getName(): string { return 'Dashboard Service'; }
    public function getMenuLabel(): string { return 'Dashboard'; }
    public function getDefaultRoute(): string { return 'dashboard/index'; }
    public function getServiceName(): string { return 'dashboard'; }
}
