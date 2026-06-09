<?php

class TripController
{
    public function index(): void
    {
        $this->checkAccess();
        render('trips/index', ['trips' => Trip::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('trips/form', [
            'trip' => null,
            'teams' => Team::getForSelect(),
            'action' => 'trips/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        Trip::create([
            'destinatie' => post('destinatie'),
            'data_plecare' => post('data_plecare'),
            'data_intoarcere' => post('data_intoarcere'),
            'scop' => post('scop'),
            'team_id' => post('team_id') !== '' ? (int) post('team_id') : null,
        ]);
        setFlash('success', 'Deplasare adaugata.');
        redirect('trips/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $trip = Trip::find((int) get('id'));
        if (!$trip) redirect('trips/index');
        render('trips/form', [
            'trip' => $trip,
            'teams' => Team::getForSelect(),
            'action' => 'trips/update&id=' . $trip['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Trip::update((int) get('id'), [
            'destinatie' => post('destinatie'),
            'data_plecare' => post('data_plecare'),
            'data_intoarcere' => post('data_intoarcere'),
            'scop' => post('scop'),
            'team_id' => post('team_id') !== '' ? (int) post('team_id') : null,
        ]);
        setFlash('success', 'Deplasare actualizata.');
        redirect('trips/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Trip::delete((int) get('id'));
        setFlash('success', 'Deplasare stearsa.');
        redirect('trips/index');
    }

    public function members(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $trip = Trip::find($id);
        if (!$trip) redirect('trips/index');
        render('trips/members', [
            'trip' => $trip,
            'members' => Trip::getMembers($id),
            'available' => Trip::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        $this->checkAccess();
        Trip::addMember((int) post('trip_id'), (int) post('member_id'));
        setFlash('success', 'Membru adaugat la deplasare.');
        redirect('trips/members', ['id' => post('trip_id')]);
    }

    public function removeMember(): void
    {
        $this->checkAccess();
        Trip::removeMember((int) get('trip_id'), (int) get('member_id'));
        setFlash('success', 'Membru eliminat.');
        redirect('trips/members', ['id' => get('trip_id')]);
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('trips')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
