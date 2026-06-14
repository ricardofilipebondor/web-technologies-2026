<?php

class PrizesPlugin implements PluginInterface
{
    public function getId(): string { return 'prizes'; }
    public function getName(): string { return 'Prizes Service'; }
    public function getMenuLabel(): string { return 'Premii'; }
    public function getDefaultRoute(): string { return 'prizes/index'; }
    public function getServiceName(): string { return 'prizes'; }
}
