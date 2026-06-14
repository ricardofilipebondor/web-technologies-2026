<?php

class ActivitiesPlugin implements PluginInterface
{
    public function getId(): string { return 'activities'; }
    public function getName(): string { return 'Activities Service'; }
    public function getMenuLabel(): string { return 'Activitati'; }
    public function getDefaultRoute(): string { return 'activities/index'; }
    public function getServiceName(): string { return 'activities'; }
}
