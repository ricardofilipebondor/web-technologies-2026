<?php

class AdminPlugin implements PluginInterface
{
    public function getId(): string { return 'admin'; }
    public function getName(): string { return 'Admin Service'; }
    public function getMenuLabel(): string { return 'Administrare'; }
    public function getDefaultRoute(): string { return 'admin/index'; }
    public function getServiceName(): string { return 'admin'; }
}
