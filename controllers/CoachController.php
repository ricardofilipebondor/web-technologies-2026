<?php

class CoachController
{
    public function index(): void
    {
        $this->checkAccess();
        render('coaches/index', ['coaches' => Coach::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('coaches/form', ['coach' => null, 'action' => 'coaches/store']);
    }

    public function store(): void
    {
        $this->checkAccess();
        $data = $this->getFormData();
        if ($data['nume'] === '' || $data['email'] === '') {
            setFlash('danger', 'Completati campurile obligatorii.');
            redirect('coaches/create');
        }
        Coach::create($data);
        setFlash('success', 'Inregistrare adaugata.');
        redirect('coaches/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $coach = Coach::find((int) get('id'));
        if (!$coach) {
            setFlash('danger', 'Inregistrare negasita.');
            redirect('coaches/index');
        }
        render('coaches/form', ['coach' => $coach, 'action' => 'coaches/update&id=' . $coach['id']]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Coach::update((int) get('id'), $this->getFormData());
        setFlash('success', 'Inregistrare actualizata.');
        redirect('coaches/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Coach::delete((int) get('id'));
        setFlash('success', 'Inregistrare stearsa.');
        redirect('coaches/index');
    }

    public function show(): void
    {
        $this->checkAccess();
        $coach = Coach::find((int) get('id'));
        if (!$coach) {
            setFlash('danger', 'Inregistrare negasita.');
            redirect('coaches/index');
        }
        render('coaches/show', ['coach' => $coach]);
    }

    private function getFormData(): array
    {
        return [
            'nume' => post('nume'),
            'email' => post('email'),
            'telefon' => post('telefon'),
            'specializare' => post('specializare'),
            'disponibilitate' => post('disponibilitate'),
            'rol' => post('rol'),
        ];
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('coaches')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
