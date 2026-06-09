<?php

class GroupsPlugin implements PluginInterface
{
    public function getId(): string { return 'groups'; }
    public function getName(): string { return 'Groups Service'; }
    public function getMenuLabel(): string { return 'Grupe'; }
    public function getDefaultRoute(): string { return 'groups/index'; }
    public function getServiceName(): string { return 'groups'; }
}
