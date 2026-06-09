<?php

interface PluginInterface
{
    public function getId(): string;
    public function getName(): string;
    public function getMenuLabel(): string;
    public function getDefaultRoute(): string;
    public function getServiceName(): string;
}
