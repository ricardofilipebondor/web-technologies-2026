<?php

class CoachesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('coaches');
        Response::ok(Coach::all());
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('coaches');
        $coach = Coach::find((int) $params['id']);
        if (!$coach) {
            Response::error('Inregistrare negasita.', 404);
        }
        Response::ok($coach);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('coaches');
        $data = $this->formData();
        if ($data['nume'] === '' || $data['email'] === '') {
            Response::error('Completati campurile obligatorii.');
        }
        Coach::create($data);
        Response::ok(null, 'Inregistrare adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('coaches');
        Coach::update((int) $params['id'], $this->formData());
        Response::ok(null, 'Inregistrare actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('coaches');
        Coach::delete((int) $params['id']);
        Response::ok(null, 'Inregistrare stearsa.');
    }

    private function formData(): array
    {
        $body = AuthMiddleware::getJsonBody();
        return [
            'nume' => trim($body['nume'] ?? ''),
            'email' => trim($body['email'] ?? ''),
            'telefon' => trim($body['telefon'] ?? ''),
            'specializare' => trim($body['specializare'] ?? ''),
            'disponibilitate' => trim($body['disponibilitate'] ?? ''),
            'rol' => trim($body['rol'] ?? 'antrenor'),
        ];
    }
}

class TeamsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('teams');
        Response::ok(Team::all());
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $data = (new TeamsService())->performanceHistory((int) $params['id']);
        if (empty($data)) {
            Response::error('Echipa negasita.', 404);
        }
        Response::ok($data);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('teams');
        $body = AuthMiddleware::getJsonBody();
        Team::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
        ]);
        Response::ok(null, 'Echipa adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $body = AuthMiddleware::getJsonBody();
        Team::update((int) $params['id'], [
            'denumire' => trim($body['denumire'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
        ]);
        Response::ok(null, 'Echipa actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::delete((int) $params['id']);
        Response::ok(null, 'Echipa stearsa.');
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $team = Team::find($id);
        if (!$team) {
            Response::error('Echipa negasita.', 404);
        }
        Response::ok([
            'team' => $team,
            'members' => Team::getMembers($id),
            'available' => Team::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        AuthMiddleware::requireModule('teams');
        $body = AuthMiddleware::getJsonBody();
        Team::addMember((int) $body['team_id'], (int) $body['member_id']);
        Response::ok(null, 'Membru adaugat in echipa.');
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::removeMember((int) $params['team_id'], (int) $params['member_id']);
        Response::ok(null, 'Membru eliminat.');
    }

    public function results(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $team = Team::find($id);
        if (!$team) {
            Response::error('Echipa negasita.', 404);
        }
        Response::ok([
            'team' => $team,
            'results' => Team::getResults($id),
            'competitions' => Competition::all(),
        ]);
    }

    public function addResult(): void
    {
        AuthMiddleware::requireModule('teams');
        $body = AuthMiddleware::getJsonBody();
        Team::addResult([
            'team_id' => (int) $body['team_id'],
            'competition_id' => (int) $body['competition_id'],
            'punctaj_total' => (float) ($body['punctaj_total'] ?? 0),
            'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        Response::ok(null, 'Rezultat echipa inregistrat.');
    }

    public function deleteResult(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::deleteResult((int) $params['result_id']);
        Response::ok(null, 'Rezultat sters.');
    }
}

class GroupsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('groups');
        Response::ok(Group::all());
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('groups');
        $body = AuthMiddleware::getJsonBody();
        Group::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'nivel' => trim($body['nivel'] ?? 'incepatori'),
            'coach_id' => !empty($body['coach_id']) ? (int) $body['coach_id'] : null,
        ]);
        Response::ok(null, 'Grup adaugat.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        $body = AuthMiddleware::getJsonBody();
        Group::update((int) $params['id'], [
            'denumire' => trim($body['denumire'] ?? ''),
            'nivel' => trim($body['nivel'] ?? 'incepatori'),
            'coach_id' => !empty($body['coach_id']) ? (int) $body['coach_id'] : null,
        ]);
        Response::ok(null, 'Grup actualizat.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        Group::delete((int) $params['id']);
        Response::ok(null, 'Grup sters.');
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        $id = (int) $params['id'];
        $group = Group::find($id);
        if (!$group) {
            Response::error('Grup negasit.', 404);
        }
        Response::ok([
            'group' => $group,
            'members' => Group::getMembers($id),
            'available' => Group::getAvailableMembers($id),
        ]);
    }

    public function addMember(): void
    {
        AuthMiddleware::requireModule('groups');
        $body = AuthMiddleware::getJsonBody();
        Group::addMember((int) $body['group_id'], (int) $body['member_id']);
        Response::ok(null, 'Membru adaugat in grup.');
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        Group::removeMember((int) $params['group_id'], (int) $params['member_id']);
        Response::ok(null, 'Membru eliminat.');
    }

    public function coaches(): void
    {
        AuthMiddleware::requireModule('groups');
        Response::ok(Coach::getAntrenori());
    }
}

class HallsApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('halls');
        Response::ok(Hall::all());
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('halls');
        $body = AuthMiddleware::getJsonBody();
        Hall::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'capacitate' => (int) ($body['capacitate'] ?? 0),
            'dotari' => trim($body['dotari'] ?? ''),
        ]);
        Response::ok(null, 'Sala adaugata.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        $body = AuthMiddleware::getJsonBody();
        Hall::update((int) $params['id'], [
            'denumire' => trim($body['denumire'] ?? ''),
            'capacitate' => (int) ($body['capacitate'] ?? 0),
            'dotari' => trim($body['dotari'] ?? ''),
        ]);
        Response::ok(null, 'Sala actualizata.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        Hall::delete((int) $params['id']);
        Response::ok(null, 'Sala stearsa.');
    }

    public function slots(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        $id = (int) $params['id'];
        $hall = Hall::find($id);
        if (!$hall) {
            Response::error('Sala negasita.', 404);
        }
        Response::ok([
            'hall' => $hall,
            'slots' => HallSlot::byHall($id),
        ]);
    }

    public function addSlot(): void
    {
        AuthMiddleware::requireModule('halls');
        $body = AuthMiddleware::getJsonBody();
        HallSlot::create([
            'hall_id' => (int) $body['hall_id'],
            'zi_saptamana' => trim($body['zi_saptamana'] ?? ''),
            'ora_start' => trim($body['ora_start'] ?? ''),
            'ora_end' => trim($body['ora_end'] ?? ''),
        ]);
        Response::ok(null, 'Interval adaugat.');
    }

    public function deleteSlot(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        HallSlot::delete((int) $params['slot_id']);
        Response::ok(null, 'Interval sters.');
    }
}
