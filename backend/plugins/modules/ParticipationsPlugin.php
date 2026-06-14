<?php

class ParticipationsPlugin implements PluginInterface
{
    public function getId(): string { return 'participations'; }
    public function getName(): string { return 'Participations Service'; }
    public function getMenuLabel(): string { return 'Participari'; }
    public function getDefaultRoute(): string { return 'participations/index'; }
    public function getServiceName(): string { return 'participations'; }
}
