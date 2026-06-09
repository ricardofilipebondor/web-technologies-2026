<?php

class RankingsPlugin implements PluginInterface
{
    public function getId(): string { return 'rankings'; }
    public function getName(): string { return 'Rankings Service'; }
    public function getMenuLabel(): string { return 'Clasamente'; }
    public function getDefaultRoute(): string { return 'rankings/index'; }
    public function getServiceName(): string { return 'rankings'; }
}
