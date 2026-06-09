<?php

class ReimbursementsPlugin implements PluginInterface
{
    public function getId(): string { return 'reimbursements'; }
    public function getName(): string { return 'Reimbursements Service'; }
    public function getMenuLabel(): string { return 'Deconturi'; }
    public function getDefaultRoute(): string { return 'reimbursements/index'; }
    public function getServiceName(): string { return 'reimbursements'; }
}
