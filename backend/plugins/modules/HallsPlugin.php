<?php

class HallsPlugin implements PluginInterface
{
    public function getId(): string { return 'halls'; }
    public function getName(): string { return 'Halls Service'; }
    public function getMenuLabel(): string { return 'Sali'; }
    public function getDefaultRoute(): string { return 'halls/index'; }
    public function getServiceName(): string { return 'halls'; }
}
