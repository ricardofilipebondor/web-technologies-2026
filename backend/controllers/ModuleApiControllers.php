<?php

class ActivitiesApiController
{
    public function index(): void
    {
        RestHelper::index('activities', '/activities', Activity::all(), [
            'halls' => '/halls',
            'coaches' => '/coaches',
        ]);
    }

    public function show(array $params): void
    {
        RestHelper::show('activities', '/activities/' . $params['id'], Activity::find((int) $params['id']));
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('activities');
        $data = $this->formData();
        $error = $this->validateSchedule($data);
        if ($error) {
            Response::problem($error);
        }
        $id = Activity::create($data);
        RestHelper::created('/activities', $id, Activity::find($id));
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('activities');
        $id = (int) $params['id'];
        $data = $this->formData();
        $error = $this->validateSchedule($data, $id);
        if ($error) {
            Response::problem($error);
        }
        Activity::update($id, $data);
        RestHelper::updated('/activities/' . $id, Activity::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('activities');
        Activity::delete((int) $params['id']);
        RestHelper::deleted();
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
        $slotError = HallSlot::fitsHallSchedule($data['hall_id'], $data['data_start'], $data['data_end']);
        if ($slotError) {
            return $slotError;
        }
        return null;
    }
}

class CompetitionsApiController
{
    public function index(): void
    {
        RestHelper::index('competitions', '/competitions', Competition::all());
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        RestHelper::show('competitions', '/competitions/' . $id, Competition::find($id), [
            'report' => '/competitions/' . $id . '/report',
            'prizes' => '/competitions/' . $id . '/prizes',
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('competitions');
        $body = AuthMiddleware::getJsonBody();
        $id = Competition::create([
            'nume' => trim($body['nume'] ?? ''),
            'data' => trim($body['data'] ?? ''),
            'locatie' => trim($body['locatie'] ?? ''),
            'tip' => trim($body['tip'] ?? 'fizic'),
            'domeniu' => trim($body['domeniu'] ?? 'local'),
        ]);
        RestHelper::created('/competitions', $id, Competition::find($id));
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Competition::update($id, [
            'nume' => trim($body['nume'] ?? ''),
            'data' => trim($body['data'] ?? ''),
            'locatie' => trim($body['locatie'] ?? ''),
            'tip' => trim($body['tip'] ?? 'fizic'),
            'domeniu' => trim($body['domeniu'] ?? 'local'),
        ]);
        RestHelper::updated('/competitions/' . $id, Competition::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        Competition::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function report(array $params): void
    {
        AuthMiddleware::requireModule('competitions');
        $id = (int) $params['id'];
        if (!Competition::find($id)) {
            Response::problem('Concurs negasit.', 404);
        }
        $report = getCompetitionReport($id);
        Response::resource(array_merge($report, [
            '_links' => Hateoas::links(['self' => '/competitions/' . $id . '/report']),
        ]));
    }

    public function prizes(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $compId = (int) $params['id'];
        $competition = Competition::find($compId);
        if (!$competition) {
            Response::problem('Concurs negasit.', 404);
        }
        Response::resource([
            'competition' => Hateoas::item($competition, '/competitions/' . $compId),
            'items' => array_map(
                fn($p) => Hateoas::item($p, '/prizes/' . $p['id']),
                Prize::byCompetition($compId)
            ),
            '_links' => Hateoas::links(['self' => '/competitions/' . $compId . '/prizes']),
        ]);
    }
}

class ParticipationsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('participations');

        if (isset($_GET['report'])) {
            $this->report();
            return;
        }

        $format = $_GET['format'] ?? '';
        if ($format !== '' && isset($_GET['competition_id'])) {
            $competitionId = (int) ($_GET['competition_id'] ?? 0);
            if ($competitionId <= 0) {
                Response::problem('Selectati un concurs pentru export.');
            }
            $this->exportReport();
            return;
        }

        RestHelper::index('participations', '/participations', Participation::all(), [
            'members' => '/members',
            'competitions' => '/competitions',
        ]);
    }

    public function show(array $params): void
    {
        RestHelper::show('participations', '/participations/' . $params['id'], Participation::find((int) $params['id']));
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('participations');
        $body = AuthMiddleware::getJsonBody();
        try {
            $id = Participation::create([
                'member_id' => (int) ($body['member_id'] ?? 0),
                'competition_id' => (int) ($body['competition_id'] ?? 0),
                'punctaj' => (float) ($body['punctaj'] ?? 0),
                'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
            ]);
            RestHelper::created('/participations', $id, Participation::find($id));
        } catch (PDOException $e) {
            Response::problem('Membrul este deja inscris la acest concurs.', 409);
        }
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('participations');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Participation::update($id, [
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => (int) ($body['competition_id'] ?? 0),
            'punctaj' => (float) ($body['punctaj'] ?? 0),
            'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
        ]);
        RestHelper::updated('/participations/' . $id, Participation::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('participations');
        Participation::delete((int) $params['id']);
        RestHelper::deleted();
    }

    private function report(): void
    {
        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $report = getCompetitionReport($competitionId);
        Response::resource(array_merge($report, [
            'competitions' => Hateoas::collection(Competition::all(), '/competitions', '/competitions')['items'],
            'competitionId' => $competitionId,
            '_links' => Hateoas::links(['self' => '/participations?report=1']),
        ]));
    }

    private function exportReport(): void
    {
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

        exportList(
            $format,
            $data,
            $filename,
            ['participant', 'categorie', 'rating', 'punctaj', 'loc_obtinut'],
            'raport',
            'participare'
        );
    }
}

class RankingsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('rankings');

        $format = $_GET['format'] ?? '';
        if ($format !== '') {
            $competitionId = (int) ($_GET['competition_id'] ?? 0);
            if ($competitionId <= 0) {
                Response::problem('Selectati un concurs pentru export.');
            }
            $this->export();
            return;
        }

        $competitionId = (int) ($_GET['competition_id'] ?? 0);
        $ranking = [];
        $competition = null;

        if ($competitionId > 0) {
            $competition = Competition::find($competitionId);
            $ranking = Participation::getRanking($competitionId);
        }

        Response::resource([
            'competitions' => Hateoas::collection(Competition::all(), '/competitions', '/competitions')['items'],
            'competitionId' => $competitionId,
            'competition' => $competition ? Hateoas::item($competition, '/competitions/' . $competitionId) : null,
            'ranking' => $ranking,
            '_links' => Hateoas::links(['self' => '/rankings']),
        ]);
    }

    private function export(): void
    {
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

        exportList(
            $format,
            $data,
            $filename,
            ['loc', 'participant', 'punctaj'],
            'clasament',
            'participant'
        );
    }
}

class PrizesApiController
{
    public function index(): void
    {
        RestHelper::index('prizes', '/prizes', Prize::all(), [
            'members' => '/members',
            'competitions' => '/competitions',
        ]);
    }

    public function show(array $params): void
    {
        RestHelper::show('prizes', '/prizes/' . $params['id'], Prize::find((int) $params['id']));
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('prizes');
        $body = AuthMiddleware::getJsonBody();
        $id = Prize::create([
            'titlu' => trim($body['titlu'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => isset($body['competition_id']) && $body['competition_id'] !== '' ? (int) $body['competition_id'] : null,
            'data_acordare' => trim($body['data_acordare'] ?? ''),
        ]);
        RestHelper::created('/prizes', $id, Prize::find($id));
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Prize::update($id, [
            'titlu' => trim($body['titlu'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
            'member_id' => (int) ($body['member_id'] ?? 0),
            'competition_id' => isset($body['competition_id']) && $body['competition_id'] !== '' ? (int) $body['competition_id'] : null,
            'data_acordare' => trim($body['data_acordare'] ?? ''),
        ]);
        RestHelper::updated('/prizes/' . $id, Prize::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        Prize::delete((int) $params['id']);
        RestHelper::deleted();
    }
}

class TripsApiController
{
    public function index(): void
    {
        RestHelper::index('trips', '/trips', Trip::all(), ['teams' => '/teams']);
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        RestHelper::show('trips', '/trips/' . $id, Trip::find($id), [
            'members' => '/trips/' . $id . '/members',
            'report' => '/trips/' . $id . '/report',
            'expenses' => '/expenses?trip_id=' . $id,
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('trips');
        $body = AuthMiddleware::getJsonBody();
        $id = Trip::create([
            'destinatie' => trim($body['destinatie'] ?? ''),
            'data_plecare' => trim($body['data_plecare'] ?? ''),
            'data_intoarcere' => trim($body['data_intoarcere'] ?? ''),
            'scop' => trim($body['scop'] ?? ''),
            'team_id' => isset($body['team_id']) && $body['team_id'] !== '' ? (int) $body['team_id'] : null,
        ]);
        RestHelper::created('/trips', $id, Trip::find($id), ['members' => '/trips/' . $id . '/members']);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Trip::update($id, [
            'destinatie' => trim($body['destinatie'] ?? ''),
            'data_plecare' => trim($body['data_plecare'] ?? ''),
            'data_intoarcere' => trim($body['data_intoarcere'] ?? ''),
            'scop' => trim($body['scop'] ?? ''),
            'team_id' => isset($body['team_id']) && $body['team_id'] !== '' ? (int) $body['team_id'] : null,
        ]);
        RestHelper::updated('/trips/' . $id, Trip::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        Trip::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $id = (int) $params['id'];
        $trip = Trip::find($id);
        if (!$trip) {
            Response::problem('Deplasare negasita.', 404);
        }
        Response::resource([
            'trip' => Hateoas::item($trip, '/trips/' . $id),
            'members' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Trip::getMembers($id)),
            'available' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Trip::getAvailableMembers($id)),
            '_links' => Hateoas::links(['self' => '/trips/' . $id . '/members']),
        ]);
    }

    public function addMember(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $tripId = (int) $params['id'];
        if (!Trip::find($tripId)) {
            Response::problem('Deplasare negasita.', 404);
        }
        $body = AuthMiddleware::getJsonBody();
        Trip::addMember($tripId, (int) $body['member_id']);
        Response::noContent();
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        Trip::removeMember((int) $params['trip_id'], (int) $params['member_id']);
        Response::noContent();
    }

    public function report(array $params): void
    {
        AuthMiddleware::requireModule('trips');
        $report = getTripReport((int) $params['id']);
        if (!$report) {
            Response::problem('Deplasare negasita.', 404);
        }
        $report['_links'] = Hateoas::links(['self' => '/trips/' . $params['id'] . '/report']);
        Response::resource($report);
    }
}

class ExpensesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('expenses');
        $tripId = (int) ($_GET['trip_id'] ?? 0);
        $items = $tripId > 0 ? Expense::byTrip($tripId) : Expense::all();
        RestHelper::index('expenses', '/expenses', $items, ['trips' => '/trips']);
    }

    public function show(array $params): void
    {
        RestHelper::show('expenses', '/expenses/' . $params['id'], Expense::find((int) $params['id']));
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('expenses');
        $body = AuthMiddleware::getJsonBody();
        $id = Expense::create([
            'trip_id' => (int) ($body['trip_id'] ?? 0),
            'tip' => trim($body['tip'] ?? ''),
            'suma' => (float) ($body['suma'] ?? 0),
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        RestHelper::created('/expenses', $id, Expense::find($id));
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('expenses');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Expense::update($id, [
            'trip_id' => (int) ($body['trip_id'] ?? 0),
            'tip' => trim($body['tip'] ?? ''),
            'suma' => (float) ($body['suma'] ?? 0),
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        RestHelper::updated('/expenses/' . $id, Expense::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('expenses');
        Expense::delete((int) $params['id']);
        RestHelper::deleted();
    }
}

class ReimbursementsApiController
{
    public function index(): void
    {
        RestHelper::index('reimbursements', '/reimbursements', Trip::all());
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('reimbursements');
        $report = getTripReport((int) $params['id']);
        if (!$report) {
            Response::problem('Deplasare negasita.', 404);
        }
        $report['_links'] = Hateoas::links([
            'self' => '/reimbursements/' . $params['id'],
            'export' => '/reimbursements/' . $params['id'] . '/export',
        ]);
        Response::resource($report);
    }

    public function export(array $params): void
    {
        AuthMiddleware::requireModule('reimbursements');
        $tripId = (int) $params['id'];
        $format = $_GET['format'] ?? 'csv';
        $trip = Trip::find($tripId);
        if (!$trip) {
            Response::problem('Deplasare negasita.', 404);
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
            PdfExporter::generateReport(
                'Raport Decont - ' . $trip['destinatie'],
                $lines,
                'decont_' . $tripId . '.pdf'
            );
        }

        if ($format === 'csv') {
            $rows = array_map(fn($e) => [$e['tip'], $e['suma'], $e['observatii'] ?? ''], $expenses);
            $rows[] = ['TOTAL', $total, ''];
            DataExporter::toCsv($rows, ['tip', 'suma', 'observatii'], 'decont_' . $tripId . '.csv');
        }

        if ($format === 'json') {
            $report = getTripReport($tripId);
            DataExporter::toJson($report, 'decont_' . $tripId . '.json');
        }

        Response::problem('Format invalid.');
    }
}
