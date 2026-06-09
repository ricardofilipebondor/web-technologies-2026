<?php

class ActivityController
{
    public function index(): void
    {
        $this->checkAccess();
        render('activities/index', ['activities' => Activity::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('activities/form', [
            'activity' => null,
            'halls' => Hall::all(),
            'coaches' => Coach::getAntrenori(),
            'action' => 'activities/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        $data = $this->getFormData();
        $error = $this->validateSchedule($data);
        if ($error) {
            setFlash('danger', $error);
            redirect('activities/create');
        }
        Activity::create($data);
        setFlash('success', 'Activitate adaugata.');
        redirect('activities/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $activity = Activity::find((int) get('id'));
        if (!$activity) redirect('activities/index');
        render('activities/form', [
            'activity' => $activity,
            'halls' => Hall::all(),
            'coaches' => Coach::getAntrenori(),
            'action' => 'activities/update&id=' . $activity['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $data = $this->getFormData();
        $error = $this->validateSchedule($data, $id);
        if ($error) {
            setFlash('danger', $error);
            redirect('activities/edit', ['id' => $id]);
        }
        Activity::update($id, $data);
        setFlash('success', 'Activitate actualizata.');
        redirect('activities/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Activity::delete((int) get('id'));
        setFlash('success', 'Activitate stearsa.');
        redirect('activities/index');
    }

    private function getFormData(): array
    {
        return [
            'titlu' => post('titlu'),
            'tip' => post('tip'),
            'data_start' => $this->normalizeDatetime(post('data_start')),
            'data_end' => $this->normalizeDatetime(post('data_end')),
            'hall_id' => (int) post('hall_id'),
            'coach_id' => (int) post('coach_id'),
        ];
    }

    private function normalizeDatetime(string $value): string
    {
        $value = str_replace('T', ' ', $value);
        if (strlen($value) === 16) {
            $value .= ':00';
        }
        return $value;
    }

    private function validateSchedule(array $data, ?int $excludeId = null): ?string
    {
        if ($data['data_start'] >= $data['data_end']) {
            return 'Data de sfarsit trebuie sa fie dupa data de inceput.';
        }
        if (Activity::hasHallConflict($data['hall_id'], $data['data_start'], $data['data_end'], $excludeId)) {
            return 'Eroare: Sala este deja ocupata in acest interval de timp.';
        }
        if (Activity::hasCoachConflict($data['coach_id'], $data['data_start'], $data['data_end'], $excludeId)) {
            return 'Eroare: Antrenorul este deja alocat unei alte activitati in acelasi interval.';
        }
        return null;
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('activities')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
