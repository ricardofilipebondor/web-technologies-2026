<?php

class ActivitiesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('activities');
        Response::ok(Activity::all());
    }

    public function meta(): void
    {
        AuthMiddleware::requireModule('activities');
        Response::ok([
            'halls' => Hall::all(),
            'coaches' => Coach::getAntrenori(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('activities');
        $data = $this->formData();
        $error = $this->validateSchedule($data);
        if ($error) {
            Response::error($error);
        }
        Activity::create($data);
        Response::ok(null, 'Activitate adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('activities');
        $id = (int) $params['id'];
        $data = $this->formData();
        $error = $this->validateSchedule($data, $id);
        if ($error) {
            Response::error($error);
        }
        Activity::update($id, $data);
        Response::ok(null, 'Activitate actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('activities');
        Activity::delete((int) $params['id']);
        Response::ok(null, 'Activitate stearsa.');
    }

    private function formData(): array
    {
        $body = AuthMiddleware::getJsonBody();
        return [
            'titlu' => trim($body['titlu'] ?? ''),
            'tip' => trim($body['tip'] ?? ''),
            'data_start' => $this->normalizeDatetime($body['data_start'] ?? ''),
            'data_end' => $this->normalizeDatetime($body['data_end'] ?? ''),
            'hall_id' => (int) ($body['hall_id'] ?? 0),
            'coach_id' => (int) ($body['coach_id'] ?? 0),
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
}

class CompetitionsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('competitions');
        Response::ok(Competition::all());
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('competitions');
        $body = AuthMiddleware::getJsonBody();
        Competition::create([
            'nume' => trim($body['nume'] ?? ''),
            'data' => trim($body['data'] ?? ''),
            'locatie' => trim($body['locatie'] ?? ''),
            'tip' => trim($body['tip'] ?? 'fizic'),
            'domeniu' => trim($body['domeniu'] ?? 'local'),
        ]);
        Response::ok(null, 'Concurs adaugat.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        $body = AuthMiddleware::getJsonBody();
        Competition::update((int) $params['id'], [
            'nume' => trim($body['nume'] ?? ''),
            'data' => trim($body['data'] ?? ''),
            'locatie' => trim($body['locatie'] ?? ''),
            'tip' => trim($body['tip'] ?? 'fizic'),
            'domeniu' => trim($body['domeniu'] ?? 'local'),
        ]);
        Response::ok(null, 'Concurs actualizat.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        Competition::delete((int) $params['id']);
        Response::ok(null, 'Concurs sters.');
    }

    public function report(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        $report = (new CompetitionsService())->participationsReport((int) $params['id']);
        Response::ok($report);
    }
}

class ParticipationsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('participations');
        Response::ok(Participation::all());
    }

    public function meta(): void
    {
        AuthMiddleware::requireModule('participations');
        Response::ok([
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('participations');
        $body = AuthMiddleware::getJsonBody();
        try {
            Participation::create([
                'member_id' => (int) ($body['member_id'] ?? 0),
                'competition_id' => (int) ($body['competition_id'] ?? 0),
                'punctaj' => (float) ($body['punctaj'] ?? 0),
                'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
            ]);
            Response::ok(null, 'Participare inregistrata.');
        } catch (PDOException $e) {
            Response::error('Membrul este deja inscris la acest concurs.');
        }
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('participations');
        $body = AuthMiddleware::getJsonBody();
        Participation::update((int) $params['id'], [
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => (int) ($body['competition_id'] ?? 0),
            'punctaj' => (float) ($body['punctaj'] ?? 0),
            'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
        ]);
        Response::ok(null, 'Rezultat actualizat.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('participations');
        Participation::delete((int) $params['id']);
        Response::ok(null, 'Participare stearsa.');
    }

    public function report(): void
    {
        AuthMiddleware::requireModule('participations');
        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $report = (new CompetitionsService())->participationsReport($competitionId);
        Response::ok(array_merge($report, [
            'competitions' => Competition::all(),
            'competitionId' => $competitionId,
        ]));
    }

    public function exportReport(): void
    {
        AuthMiddleware::requireModule('participations');
        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $format = $_GET['format'] ?? 'csv';
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
        Response::error('Format invalid.');
    }
}

class RankingsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('rankings');
        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $ranking = [];
        $competition = null;

        if ($competitionId > 0) {
            $competition = Competition::find($competitionId);
            $ranking = Participation::getRanking($competitionId);
        }

        Response::ok([
            'competitions' => Competition::all(),
            'competitionId' => $competitionId,
            'competition' => $competition,
            'ranking' => $ranking,
        ]);
    }

    public function export(): void
    {
        AuthMiddleware::requireModule('rankings');
        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $format = $_GET['format'] ?? 'csv';
        $ranking = Participation::getRanking($competitionId);
        $competition = Competition::find($competitionId);

        $data = [];
        $loc = 1;
        foreach ($ranking as $row) {
            $data[] = [
                'loc' => $loc++,
                'participant' => $row['nume'] . ' ' . $row['prenume'],
                'punctaj' => $row['punctaj'],
            ];
        }

        $filename = 'clasament_' . ($competition['nume'] ?? 'concurs');

        if ($format === 'csv') {
            $rows = array_map(fn($r) => [$r['loc'], $r['participant'], $r['punctaj']], $data);
            DataExporter::toCsv($rows, ['loc', 'participant', 'punctaj'], $filename . '.csv');
        } elseif ($format === 'json') {
            DataExporter::toJson($data, $filename . '.json');
        } elseif ($format === 'xml') {
            DataExporter::toXml($data, 'clasament', 'participant', $filename . '.xml');
        }
        Response::error('Format invalid.');
    }
}

class PrizesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('prizes');
        Response::ok(Prize::all());
    }

    public function meta(): void
    {
        AuthMiddleware::requireModule('prizes');
        Response::ok([
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('prizes');
        $body = AuthMiddleware::getJsonBody();
        Prize::create([
            'titlu' => trim($body['titlu'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => isset($body['competition_id']) && $body['competition_id'] !== '' ? (int) $body['competition_id'] : null,
            'data_acordare' => trim($body['data_acordare'] ?? ''),
        ]);
        Response::ok(null, 'Premiu adaugat.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $body = AuthMiddleware::getJsonBody();
        Prize::update((int) $params['id'], [
            'titlu' => trim($body['titlu'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => isset($body['competition_id']) && $body['competition_id'] !== '' ? (int) $body['competition_id'] : null,
            'data_acordare' => trim($body['data_acordare'] ?? ''),
        ]);
        Response::ok(null, 'Premiu actualizat.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        Prize::delete((int) $params['id']);
        Response::ok(null, 'Premiu sters.');
    }

    public function byMember(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $memberId = (int) $params['member_id'];
        $member = Member::find($memberId);
        if (!$member) {
            Response::error('Membru negasit.', 404);
        }
        Response::ok([
            'member' => $member,
            'prizes' => Prize::byMember($memberId),
        ]);
    }

    public function byCompetition(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $compId = (int) $params['competition_id'];
        $competition = Competition::find($compId);
        if (!$competition) {
            Response::error('Concurs negasit.', 404);
        }
        Response::ok([
            'competition' => $competition,
            'prizes' => Prize::byCompetition($compId),
        ]);
    }
}

class TripsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('trips');
        Response::ok(Trip::all());
    }

    public function meta(): void
    {
        AuthMiddleware::requireModule('trips');
        Response::ok(['teams' => Team::getForSelect()]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('trips');
        $body = AuthMiddleware::getJsonBody();
        Trip::create([
            'destinatie' => trim($body['destinatie'] ?? ''),
            'data_plecare' => trim($body['data_plecare'] ?? ''),
            'data_intoarcere' => trim($body['data_intoarcere'] ?? ''),
            'scop' => trim($body['scop'] ?? ''),
            'team_id' => isset($body['team_id']) && $body['team_id'] !== '' ? (int) $body['team_id'] : null,
        ]);
        Response::ok(null, 'Deplasare adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $body = AuthMiddleware::getJsonBody();
        Trip::update((int) $params['id'], [
            'destinatie' => trim($body['destinatie'] ?? ''),
            'data_plecare' => trim($body['data_plecare'] ?? ''),
            'data_intoarcere' => trim($body['data_intoarcere'] ?? ''),
            'scop' => trim($body['scop'] ?? ''),
            'team_id' => isset($body['team_id']) && $body['team_id'] !== '' ? (int) $body['team_id'] : null,
        ]);
        Response::ok(null, 'Deplasare actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        Trip::delete((int) $params['id']);
        Response::ok(null, 'Deplasare stearsa.');
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $id = (int) $params['id'];
        $trip = Trip::find($id);
        if (!$trip) {
            Response::error('Deplasare negasita.', 404);
        }
        Response::ok([
            'trip' => $trip,
            'members' => Trip::getMembers($id),
            'available' => Trip::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        AuthMiddleware::requireModule('trips');
        $body = AuthMiddleware::getJsonBody();
        Trip::addMember((int) $body['trip_id'], (int) $body['member_id']);
        Response::ok(null, 'Membru adaugat la deplasare.');
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        Trip::removeMember((int) $params['trip_id'], (int) $params['member_id']);
        Response::ok(null, 'Membru eliminat.');
    }

    public function report(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $report = (new TripsService())->report((int) $params['id']);
        if (!$report) {
            Response::error('Deplasare negasita.', 404);
        }
        Response::ok($report);
    }
}

class ExpensesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('expenses');
        $tripId = (int) ($_GET['trip_id'] ?? 0);
        if ($tripId > 0) {
            Response::ok(Expense::byTrip($tripId));
        }
        Response::ok(Expense::all());
    }

    public function meta(): void
    {
        AuthMiddleware::requireModule('expenses');
        Response::ok(['trips' => Trip::all()]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('expenses');
        $body = AuthMiddleware::getJsonBody();
        Expense::create([
            'trip_id' => (int) ($body['trip_id'] ?? 0),
            'tip' => trim($body['tip'] ?? ''),
            'suma' => (float) ($body['suma'] ?? 0),
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        Response::ok(null, 'Cheltuiala adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('expenses');
        $body = AuthMiddleware::getJsonBody();
        Expense::update((int) $params['id'], [
            'trip_id' => (int) ($body['trip_id'] ?? 0),
            'tip' => trim($body['tip'] ?? ''),
            'suma' => (float) ($body['suma'] ?? 0),
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        Response::ok(null, 'Cheltuiala actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('expenses');
        Expense::delete((int) $params['id']);
        Response::ok(null, 'Cheltuiala stearsa.');
    }
}

class ReimbursementsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('reimbursements');
        Response::ok(Trip::all());
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('reimbursements');
        $report = (new TripsService())->report((int) $params['id']);
        if (!$report) {
            Response::error('Deplasare negasita.', 404);
        }
        Response::ok($report);
    }

    public function export(array $params): void
    {
        AuthMiddleware::requireModule('reimbursements');
        $tripId = (int) $params['id'];
        $format = $_GET['format'] ?? 'csv';
        $trip = Trip::find($tripId);
        if (!$trip) {
            Response::error('Deplasare negasita.', 404);
        }
        $expenses = Expense::byTrip($tripId);
        $total = Trip::getTotalExpenses($tripId);
        $tripMembers = Trip::getMembers($tripId);

        if ($format === 'pdf') {
            $lines = [
                'Destinatie: ' . $trip['destinatie'],
                'Echipa: ' . ($trip['team_nume'] ?? '-'),
                'Plecare: ' . $trip['data_plecare'],
                'Intoarcere: ' . $trip['data_intoarcere'],
                'Scop: ' . ($trip['scop'] ?? '-'),
                '',
                'MEMBRI ECHIPA:',
            ];
            foreach ($tripMembers as $m) {
                $lines[] = '- ' . $m['nume'] . ' ' . $m['prenume'];
            }
            $lines[] = '';
            $lines[] = 'CHELTUIELI:';
            foreach ($expenses as $e) {
                $lines[] = sprintf('%s - %.2f RON - %s', $e['tip'], $e['suma'], $e['observatii'] ?? '');
            }
            $lines[] = '';
            $lines[] = sprintf('TOTAL GENERAL: %.2f RON', $total);
            PdfExporter::generateReport('Raport Decont - ' . $trip['destinatie'], $lines);
        }

        $rows = array_map(fn($e) => [$e['tip'], $e['suma'], $e['observatii'] ?? ''], $expenses);
        $rows[] = ['TOTAL', $total, ''];
        DataExporter::toCsv($rows, ['tip', 'suma', 'observatii'], 'decont_' . $tripId . '.csv');
    }
}
