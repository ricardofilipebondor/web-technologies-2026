<?php

class TeamController
{
    public function index(): void
    {
        $this->checkAccess();
        render('teams/index', ['teams' => Team::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('teams/form', ['team' => null, 'action' => 'teams/store']);
    }

    public function store(): void
    {
        $this->checkAccess();
        Team::create(['denumire' => post('denumire'), 'descriere' => post('descriere')]);
        setFlash('success', 'Echipa adaugata.');
        redirect('teams/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $team = Team::find((int) get('id'));
        if (!$team) redirect('teams/index');
        render('teams/form', ['team' => $team, 'action' => 'teams/update&id=' . $team['id']]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Team::update((int) get('id'), ['denumire' => post('denumire'), 'descriere' => post('descriere')]);
        setFlash('success', 'Echipa actualizata.');
        redirect('teams/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Team::delete((int) get('id'));
        setFlash('success', 'Echipa stearsa.');
        redirect('teams/index');
    }

    public function members(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $team = Team::find($id);
        if (!$team) redirect('teams/index');
        render('teams/members', [
            'team' => $team,
            'members' => Team::getMembers($id),
            'available' => Team::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        $this->checkAccess();
        Team::addMember((int) post('team_id'), (int) post('member_id'));
        setFlash('success', 'Membru adaugat in echipa.');
        redirect('teams/members', ['id' => post('team_id')]);
    }

    public function removeMember(): void
    {
        $this->checkAccess();
        Team::removeMember((int) get('team_id'), (int) get('member_id'));
        setFlash('success', 'Membru eliminat.');
        redirect('teams/members', ['id' => get('team_id')]);
    }

    public function results(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $team = Team::find($id);
        if (!$team) redirect('teams/index');
        render('teams/results', [
            'team' => $team,
            'results' => Team::getResults($id),
            'competitions' => Competition::all(),
        ]);
    }

    public function addResult(): void
    {
        $this->checkAccess();
        Team::addResult([
            'team_id' => (int) post('team_id'),
            'competition_id' => (int) post('competition_id'),
            'punctaj_total' => (float) post('punctaj_total', 0),
            'loc_obtinut' => post('loc_obtinut') !== '' ? (int) post('loc_obtinut') : null,
            'observatii' => post('observatii'),
        ]);
        setFlash('success', 'Rezultat echipa inregistrat.');
        redirect('teams/results', ['id' => post('team_id')]);
    }

    public function deleteResult(): void
    {
        $this->checkAccess();
        Team::deleteResult((int) get('result_id'));
        setFlash('success', 'Rezultat sters.');
        redirect('teams/results', ['id' => get('id')]);
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('teams')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
