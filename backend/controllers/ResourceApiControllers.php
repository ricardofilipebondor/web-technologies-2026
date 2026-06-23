<?php

class CoachesApiController
{
    public function index(): void
    {
        RestHelper::index('coaches', '/coaches', Coach::all());
    }

    public function show(array $params): void
    {
        RestHelper::show('coaches', '/coaches/' . $params['id'], Coach::find((int) $params['id']));
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('coaches');
        $data = $this->formData();
        if ($data['nume'] === '' || $data['email'] === '') {
            Response::problem('Completati campurile obligatorii.');
        }
        $id = Coach::create($data);
        RestHelper::created('/coaches', $id, Coach::find($id));
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('coaches');
        $id = (int) $params['id'];
        Coach::update($id, $this->formData());
        RestHelper::updated('/coaches/' . $id, Coach::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('coaches');
        Coach::delete((int) $params['id']);
        RestHelper::deleted();
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
        RestHelper::index('teams', '/teams', Team::all());
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $team = Team::find($id);
        if (!$team) {
            Response::problem('Echipa negasita.', 404);
        }
        $data = getTeamDetails($id);
        $data['_links'] = Hateoas::links([
            'self' => '/teams/' . $id,
            'members' => '/teams/' . $id . '/members',
            'results' => '/teams/' . $id . '/results',
        ]);
        Response::resource($data);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('teams');
        $body = AuthMiddleware::getJsonBody();
        $id = Team::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
        ]);
        RestHelper::created('/teams', $id, Team::find($id), [
            'members' => '/teams/' . $id . '/members',
            'results' => '/teams/' . $id . '/results',
        ]);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Team::update($id, [
            'denumire' => trim($body['denumire'] ?? ''),
            'descriere' => trim($body['descriere'] ?? ''),
        ]);
        RestHelper::updated('/teams/' . $id, Team::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $team = Team::find($id);
        if (!$team) {
            Response::problem('Echipa negasita.', 404);
        }
        Response::resource([
            'team' => Hateoas::item($team, '/teams/' . $id),
            'members' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Team::getMembers($id)),
            'available' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Team::getAvailableMembers($id)),
            '_links' => Hateoas::links(['self' => '/teams/' . $id . '/members']),
        ]);
    }

    public function addMember(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $teamId = (int) $params['id'];
        if (!Team::find($teamId)) {
            Response::problem('Echipa negasita.', 404);
        }
        $body = AuthMiddleware::getJsonBody();
        Team::addMember($teamId, (int) $body['member_id']);
        Response::noContent();
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::removeMember((int) $params['team_id'], (int) $params['member_id']);
        Response::noContent();
    }

    public function results(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $id = (int) $params['id'];
        $team = Team::find($id);
        if (!$team) {
            Response::problem('Echipa negasita.', 404);
        }
        Response::resource([
            'team' => Hateoas::item($team, '/teams/' . $id),
            'results' => array_map(
                fn($r) => Hateoas::item($r, '/teams/' . $id . '/results/' . $r['id']),
                Team::getResults($id)
            ),
            'competitions' => Hateoas::collection(Competition::all(), '/competitions', '/competitions')['items'],
            '_links' => Hateoas::links(['self' => '/teams/' . $id . '/results']),
        ]);
    }

    public function addResult(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        $teamId = (int) $params['id'];
        if (!Team::find($teamId)) {
            Response::problem('Echipa negasita.', 404);
        }
        $body = AuthMiddleware::getJsonBody();
        $resultId = Team::addResult([
            'team_id' => $teamId,
            'competition_id' => (int) $body['competition_id'],
            'punctaj_total' => (float) ($body['punctaj_total'] ?? 0),
            'loc_obtinut' => isset($body['loc_obtinut']) && $body['loc_obtinut'] !== '' ? (int) $body['loc_obtinut'] : null,
            'observatii' => trim($body['observatii'] ?? ''),
        ]);
        Response::created(
            ['id' => $resultId, '_links' => Hateoas::links(['self' => '/teams/' . $teamId . '/results/' . $resultId])],
            '/teams/' . $teamId . '/results/' . $resultId
        );
    }

    public function deleteResult(array $params): void
    {
        AuthMiddleware::requireModule('teams');
        Team::deleteResult((int) $params['result_id']);
        Response::noContent();
    }
}

class GroupsApiController
{
    public function index(): void
    {
        RestHelper::index('groups', '/groups', Group::all(), ['coaches' => '/coaches']);
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        RestHelper::show('groups', '/groups/' . $id, Group::find($id), [
            'members' => '/groups/' . $id . '/members',
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('groups');
        $body = AuthMiddleware::getJsonBody();
        $id = Group::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'nivel' => trim($body['nivel'] ?? 'incepatori'),
            'coach_id' => !empty($body['coach_id']) ? (int) $body['coach_id'] : null,
        ]);
        RestHelper::created('/groups', $id, Group::find($id), ['members' => '/groups/' . $id . '/members']);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Group::update($id, [
            'denumire' => trim($body['denumire'] ?? ''),
            'nivel' => trim($body['nivel'] ?? 'incepatori'),
            'coach_id' => !empty($body['coach_id']) ? (int) $body['coach_id'] : null,
        ]);
        RestHelper::updated('/groups/' . $id, Group::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        Group::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function members(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        $id = (int) $params['id'];
        $group = Group::find($id);
        if (!$group) {
            Response::problem('Grup negasit.', 404);
        }
        Response::resource([
            'group' => Hateoas::item($group, '/groups/' . $id),
            'members' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Group::getMembers($id)),
            'available' => array_map(fn($m) => Hateoas::item($m, '/members/' . $m['id']), Group::getAvailableMembers($id)),
            '_links' => Hateoas::links(['self' => '/groups/' . $id . '/members']),
        ]);
    }

    public function addMember(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        $groupId = (int) $params['id'];
        if (!Group::find($groupId)) {
            Response::problem('Grup negasit.', 404);
        }
        $body = AuthMiddleware::getJsonBody();
        Group::addMember($groupId, (int) $body['member_id']);
        Response::noContent();
    }

    public function removeMember(array $params): void
    {
        AuthMiddleware::requireModule('groups');
        Group::removeMember((int) $params['group_id'], (int) $params['member_id']);
        Response::noContent();
    }
}

class HallsApiController
{
    public function index(): void
    {
        RestHelper::index('halls', '/halls', Hall::all());
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        RestHelper::show('halls', '/halls/' . $id, Hall::find($id), ['slots' => '/halls/' . $id . '/slots']);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('halls');
        $body = AuthMiddleware::getJsonBody();
        $id = Hall::create([
            'denumire' => trim($body['denumire'] ?? ''),
            'capacitate' => (int) ($body['capacitate'] ?? 0),
            'dotari' => trim($body['dotari'] ?? ''),
        ]);
        RestHelper::created('/halls', $id, Hall::find($id), ['slots' => '/halls/' . $id . '/slots']);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        $id = (int) $params['id'];
        $body = AuthMiddleware::getJsonBody();
        Hall::update($id, [
            'denumire' => trim($body['denumire'] ?? ''),
            'capacitate' => (int) ($body['capacitate'] ?? 0),
            'dotari' => trim($body['dotari'] ?? ''),
        ]);
        RestHelper::updated('/halls/' . $id, Hall::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        Hall::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function slots(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        $id = (int) $params['id'];
        $hall = Hall::find($id);
        if (!$hall) {
            Response::problem('Sala negasita.', 404);
        }
        Response::resource([
            'hall' => Hateoas::item($hall, '/halls/' . $id),
            'slots' => array_map(
                fn($s) => Hateoas::item($s, '/halls/' . $id . '/slots/' . $s['id']),
                HallSlot::byHall($id)
            ),
            '_links' => Hateoas::links(['self' => '/halls/' . $id . '/slots']),
        ]);
    }

    public function addSlot(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        $hallId = (int) $params['id'];
        if (!Hall::find($hallId)) {
            Response::problem('Sala negasita.', 404);
        }
        $body = AuthMiddleware::getJsonBody();
        $slotId = HallSlot::create([
            'hall_id' => $hallId,
            'zi_saptamana' => trim($body['zi_saptamana'] ?? ''),
            'ora_start' => trim($body['ora_start'] ?? ''),
            'ora_end' => trim($body['ora_end'] ?? ''),
        ]);
        $slot = HallSlot::byHall($hallId);
        $created = array_values(array_filter($slot, fn($s) => (int) $s['id'] === $slotId))[0] ?? ['id' => $slotId];
        Response::created(
            Hateoas::item($created, '/halls/' . $hallId . '/slots/' . $slotId),
            '/halls/' . $hallId . '/slots/' . $slotId
        );
    }

    public function deleteSlot(array $params): void
    {
        AuthMiddleware::requireModule('halls');
        HallSlot::delete((int) $params['slot_id']);
        Response::noContent();
    }
}
