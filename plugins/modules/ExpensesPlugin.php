<?php

class ExpensesPlugin implements PluginInterface
{
    public function getId(): string { return 'expenses'; }
    public function getName(): string { return 'Expenses Service'; }
    public function getMenuLabel(): string { return 'Cheltuieli'; }
    public function getDefaultRoute(): string { return 'expenses/index'; }
    public function getServiceName(): string { return 'expenses'; }
}
