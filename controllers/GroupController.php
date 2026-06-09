<?php

class GroupController
{
    public function index(): void
    {
        $this->checkAccess();
        render('groups/index', ['groups' => GroupModel::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('groups/form', [
            'group' => null,
            'coaches' => Coach::getAntrenori(),
            'action' => 'groups/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        GroupModel::create([
            'denumire' => post('denumire'),
            'nivel' => post('nivel'),
            'coach_id' => post('coach_id') !== '' ? (int) post('coach_id') : null,
        ]);
        setFlash('success', 'Grupa adaugata.');
        redirect('groups/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $group = GroupModel::find((int) get('id'));
        if (!$group) {
            redirect('groups/index');
        }
        render('groups/form', [
            'group' => $group,
            'coaches' => Coach::getAntrenori(),
            'action' => 'groups/update&id=' . $group['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        GroupModel::update((int) get('id'), [
            'denumire' => post('denumire'),
            'nivel' => post('nivel'),
            'coach_id' => post('coach_id') !== '' ? (int) post('coach_id') : null,
        ]);
        setFlash('success', 'Grupa actualizata.');
        redirect('groups/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        GroupModel::delete((int) get('id'));
        setFlash('success', 'Grupa stearsa.');
        redirect('groups/index');
    }

    public function members(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $group = GroupModel::find($id);
        if (!$group) {
            redirect('groups/index');
        }
        render('groups/members', [
            'group' => $group,
            'members' => GroupModel::getMembers($id),
            'available' => GroupModel::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        $this->checkAccess();
        GroupModel::addMember((int) post('group_id'), (int) post('member_id'));
        setFlash('success', 'Membru adaugat in grupa.');
        redirect('groups/members', ['id' => post('group_id')]);
    }

    public function removeMember(): void
    {
        $this->checkAccess();
        GroupModel::removeMember((int) get('group_id'), (int) get('member_id'));
        setFlash('success', 'Membru eliminat din grupa.');
        redirect('groups/members', ['id' => get('group_id')]);
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('groups')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
