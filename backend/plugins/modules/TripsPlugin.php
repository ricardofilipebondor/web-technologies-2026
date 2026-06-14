<?php

class TripsPlugin implements PluginInterface
{
    public function getId(): string { return 'trips'; }
    public function getName(): string { return 'Trips Service'; }
    public function getMenuLabel(): string { return 'Deplasari'; }
    public function getDefaultRoute(): string { return 'trips/index'; }
    public function getServiceName(): string { return 'trips'; }
}
