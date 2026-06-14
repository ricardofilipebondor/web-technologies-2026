<?php

class MembersPlugin implements PluginInterface
{
    public function getId(): string { return 'members'; }
    public function getName(): string { return 'Members Service'; }
    public function getMenuLabel(): string { return 'Membri'; }
    public function getDefaultRoute(): string { return 'members/index'; }
    public function getServiceName(): string { return 'members'; }
}
