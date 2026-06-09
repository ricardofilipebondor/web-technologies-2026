<?php

class HallController
{
    public function index(): void
    {
        $this->checkAccess();
        render('halls/index', ['halls' => Hall::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('halls/form', ['hall' => null, 'action' => 'halls/store']);
    }

    public function store(): void
    {
        $this->checkAccess();
        Hall::create([
            'denumire' => post('denumire'),
            'capacitate' => (int) post('capacitate'),
            'dotari' => post('dotari'),
        ]);
        setFlash('success', 'Sala adaugata.');
        redirect('halls/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $hall = Hall::find((int) get('id'));
        if (!$hall) redirect('halls/index');
        render('halls/form', ['hall' => $hall, 'action' => 'halls/update&id=' . $hall['id']]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Hall::update((int) get('id'), [
            'denumire' => post('denumire'),
            'capacitate' => (int) post('capacitate'),
            'dotari' => post('dotari'),
        ]);
        setFlash('success', 'Sala actualizata.');
        redirect('halls/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Hall::delete((int) get('id'));
        setFlash('success', 'Sala stearsa.');
        redirect('halls/index');
    }

    public function slots(): void
    {
        $this->checkAccess();
        $hall = Hall::find((int) get('id'));
        if (!$hall) redirect('halls/index');
        render('halls/slots', [
            'hall' => $hall,
            'slots' => HallSlot::byHall($hall['id']),
        ]);
    }

    public function addSlot(): void
    {
        $this->checkAccess();
        HallSlot::create([
            'hall_id' => (int) post('hall_id'),
            'zi_saptamana' => post('zi_saptamana'),
            'ora_start' => post('ora_start'),
            'ora_end' => post('ora_end'),
        ]);
        setFlash('success', 'Interval orar adaugat.');
        redirect('halls/slots', ['id' => post('hall_id')]);
    }

    public function deleteSlot(): void
    {
        $this->checkAccess();
        $hallId = (int) get('hall_id');
        HallSlot::delete((int) get('slot_id'));
        setFlash('success', 'Interval sters.');
        redirect('halls/slots', ['id' => $hallId]);
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('halls')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
