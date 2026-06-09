<?php

class ExpenseController
{
    public function index(): void
    {
        $this->checkAccess();
        render('expenses/index', ['expenses' => Expense::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('expenses/form', [
            'expense' => null,
            'trips' => Trip::all(),
            'action' => 'expenses/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        Expense::create([
            'trip_id' => (int) post('trip_id'),
            'tip' => post('tip'),
            'suma' => (float) post('suma'),
            'observatii' => post('observatii'),
        ]);
        setFlash('success', 'Cheltuiala adaugata.');
        redirect('expenses/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $expense = Expense::find((int) get('id'));
        if (!$expense) redirect('expenses/index');
        render('expenses/form', [
            'expense' => $expense,
            'trips' => Trip::all(),
            'action' => 'expenses/update&id=' . $expense['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Expense::update((int) get('id'), [
            'trip_id' => (int) post('trip_id'),
            'tip' => post('tip'),
            'suma' => (float) post('suma'),
            'observatii' => post('observatii'),
        ]);
        setFlash('success', 'Cheltuiala actualizata.');
        redirect('expenses/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Expense::delete((int) get('id'));
        setFlash('success', 'Cheltuiala stearsa.');
        redirect('expenses/index');
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('expenses')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
