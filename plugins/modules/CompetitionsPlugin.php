<?php

class CompetitionsPlugin implements PluginInterface
{
    public function getId(): string { return 'competitions'; }
    public function getName(): string { return 'Competitions Service'; }
    public function getMenuLabel(): string { return 'Concursuri'; }
    public function getDefaultRoute(): string { return 'competitions/index'; }
    public function getServiceName(): string { return 'competitions'; }
}
