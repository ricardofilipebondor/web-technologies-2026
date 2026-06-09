<?php

class ParticipationController
{
    public function index(): void
    {
        $this->checkAccess();
        render('participations/index', ['participations' => Participation::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('participations/form', [
            'participation' => null,
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
            'action' => 'participations/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        try {
            Participation::create([
                'member_id' => (int) post('member_id'),
                'competition_id' => (int) post('competition_id'),
                'punctaj' => (float) post('punctaj', 0),
                'loc_obtinut' => post('loc_obtinut') !== '' ? (int) post('loc_obtinut') : null,
            ]);
            setFlash('success', 'Participare inregistrata.');
        } catch (PDOException $e) {
            setFlash('danger', 'Membrul este deja inscris la acest concurs.');
        }
        redirect('participations/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $p = Participation::find((int) get('id'));
        if (!$p) redirect('participations/index');
        render('participations/form', [
            'participation' => $p,
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
            'action' => 'participations/update&id=' . $p['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Participation::update((int) get('id'), [
            'member_id' => (int) post('member_id'),
            'competition_id' => (int) post('competition_id'),
            'punctaj' => (float) post('punctaj', 0),
            'loc_obtinut' => post('loc_obtinut') !== '' ? (int) post('loc_obtinut') : null,
        ]);
        setFlash('success', 'Rezultat actualizat.');
        redirect('participations/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Participation::delete((int) get('id'));
        setFlash('success', 'Participare stearsa.');
        redirect('participations/index');
    }

    public function report(): void
    {
        $this->checkAccess();
        $competitionId = (int) get('competition_id');
        $report = (new CompetitionsService())->participationsReport($competitionId);
        render('participations/report', [
            'competitions' => Competition::all(),
            'competitionId' => $competitionId,
            'competition' => $report['competition'] ?? null,
            'participations' => $report['participations'] ?? [],
        ]);
    }

    public function exportReport(): void
    {
        $this->checkAccess();
        $competitionId = (int) get('competition_id');
        $format = get('format', 'csv');
        $participations = Participation::byCompetition($competitionId);
        $competition = Competition::find($competitionId);
        $filename = 'raport_participari_' . ($competition['nume'] ?? 'concurs');

        $data = array_map(fn($p) => [
            'participant' => $p['member_nume'] . ' ' . $p['member_prenume'],
            'categorie' => $p['categorie'],
            'rating' => $p['rating'],
            'punctaj' => $p['punctaj'],
            'loc_obtinut' => $p['loc_obtinut'] ?? '',
        ], $participations);

        $headers = ['participant', 'categorie', 'rating', 'punctaj', 'loc_obtinut'];

        if ($format === 'csv') {
            $rows = array_map(fn($r) => array_values($r), $data);
            DataExporter::toCsv($rows, $headers, $filename . '.csv');
        } elseif ($format === 'json') {
            DataExporter::toJson($data, $filename . '.json');
        } elseif ($format === 'xml') {
            DataExporter::toXml($data, 'raport', 'participare', $filename . '.xml');
        }
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('participations')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
