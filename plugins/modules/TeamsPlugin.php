<?php

class TeamsPlugin implements PluginInterface
{
    public function getId(): string { return 'teams'; }
    public function getName(): string { return 'Teams Service'; }
    public function getMenuLabel(): string { return 'Echipe'; }
    public function getDefaultRoute(): string { return 'teams/index'; }
    public function getServiceName(): string { return 'teams'; }
}
