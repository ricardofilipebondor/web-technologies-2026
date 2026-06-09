<?php

class CompetitionController
{
    public function index(): void
    {
        $this->checkAccess();
        render('competitions/index', ['competitions' => Competition::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('competitions/form', ['competition' => null, 'action' => 'competitions/store']);
    }

    public function store(): void
    {
        $this->checkAccess();
        Competition::create([
            'nume' => post('nume'),
            'locatie' => post('locatie'),
            'data' => post('data'),
            'tip' => post('tip'),
            'domeniu' => post('domeniu'),
        ]);
        setFlash('success', 'Concurs adaugat.');
        redirect('competitions/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $comp = Competition::find((int) get('id'));
        if (!$comp) redirect('competitions/index');
        render('competitions/form', ['competition' => $comp, 'action' => 'competitions/update&id=' . $comp['id']]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Competition::update((int) get('id'), [
            'nume' => post('nume'),
            'locatie' => post('locatie'),
            'data' => post('data'),
            'tip' => post('tip'),
            'domeniu' => post('domeniu'),
        ]);
        setFlash('success', 'Concurs actualizat.');
        redirect('competitions/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Competition::delete((int) get('id'));
        setFlash('success', 'Concurs sters.');
        redirect('competitions/index');
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('competitions')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
