<?php

class MembersApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('members');

        $format = $_GET['format'] ?? '';
        if ($format !== '') {
            $this->export($format);
            return;
        }

        $search = $_GET['search'] ?? '';
        $categorie = $_GET['categorie'] ?? '';
        RestHelper::index('members', '/members', Member::all($search, $categorie), [
            'coaches' => '/coaches',
            'imports' => '/members/imports',
        ]);
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('members');
        $profile = (new MembersService())->profile((int) $params['id']);
        if (!$profile) {
            Response::problem('Membru negasit.', 404);
        }
        $profile['_links'] = Hateoas::links([
            'self' => '/members/' . $params['id'],
            'collection' => '/members',
            'prizes' => '/members/' . $params['id'] . '/prizes',
        ]);
        Response::resource($profile);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('members');
        $data = $this->formData();
        if ($data['nume'] === '' || $data['prenume'] === '' || $data['email'] === '') {
            Response::problem('Completati campurile obligatorii.');
        }
        $id = Member::create($data);
        $member = Member::find($id);
        RestHelper::created('/members', $id, $member, ['coaches' => '/coaches']);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('members');
        $id = (int) $params['id'];
        Member::update($id, $this->formData());
        RestHelper::updated('/members/' . $id, Member::find($id));
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('members');
        Member::delete((int) $params['id']);
        RestHelper::deleted();
    }

    public function prizes(array $params): void
    {
        AuthMiddleware::requireModule('prizes');
        $memberId = (int) $params['id'];
        $member = Member::find($memberId);
        if (!$member) {
            Response::problem('Membru negasit.', 404);
        }
        Response::resource([
            'member' => Hateoas::item($member, '/members/' . $memberId),
            'items' => array_map(
                fn($p) => Hateoas::item($p, '/prizes/' . $p['id']),
                Prize::byMember($memberId)
            ),
            '_links' => Hateoas::links(['self' => '/members/' . $memberId . '/prizes']),
        ]);
    }

    public function import(): void
    {
        AuthMiddleware::requireModule('members');
        $body = AuthMiddleware::getJsonBody();
        $rows = $body['rows'] ?? [];
        $count = $this->importRows($rows);
        Response::created([
            'imported' => $count,
            '_links' => Hateoas::links(['self' => '/members/imports', 'members' => '/members']),
        ], '/members/imports');
    }

    public function importFile(): void
    {
        AuthMiddleware::requireModule('members');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            Response::problem('Selectati un fisier valid.');
        }

        $type = $_POST['type'] ?? 'csv';
        $tmp = $_FILES['file']['tmp_name'];
        $rows = [];

        if ($type === 'csv') {
            $rows = DataImporter::fromCsv($tmp);
        } elseif ($type === 'json') {
            $rows = DataImporter::fromJson($tmp);
        } elseif ($type === 'xml') {
            $rows = DataImporter::fromXml($tmp, 'member');
        } else {
            Response::problem('Format invalid.');
        }

        $count = $this->importRows($rows);
        Response::created([
            'imported' => $count,
            '_links' => Hateoas::links(['self' => '/members/imports', 'members' => '/members']),
        ], '/members/imports');
    }

    private function export(string $format): void
    {
        $members = Member::all();

        if ($format === 'csv') {
            $rows = array_map(fn($m) => [
                $m['id'], $m['nume'], $m['prenume'], $m['data_nasterii'],
                $m['email'], $m['telefon'], $m['categorie'], $m['rating'],
                $m['adresa'], $m['coach_id'] ?? '',
            ], $members);
            DataExporter::toCsv($rows, ['id','nume','prenume','data_nasterii','email','telefon','categorie','rating','adresa','coach_id'], 'membri.csv');
        } elseif ($format === 'json') {
            DataExporter::toJson($members, 'membri.json');
        } elseif ($format === 'xml') {
            DataExporter::toXml($members, 'members', 'member', 'membri.xml');
        }
        Response::problem('Format invalid.');
    }

    private function importRows(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            if (empty($row['nume']) || empty($row['prenume'])) {
                continue;
            }
            Member::create([
                'nume' => $row['nume'],
                'prenume' => $row['prenume'],
                'data_nasterii' => $row['data_nasterii'] ?? '2000-01-01',
                'email' => $row['email'] ?? 'necunoscut@email.ro',
                'telefon' => $row['telefon'] ?? '',
                'categorie' => $row['categorie'] ?? 'amator',
                'rating' => (int) ($row['rating'] ?? 0),
                'adresa' => $row['adresa'] ?? '',
                'coach_id' => !empty($row['coach_id']) ? (int) $row['coach_id'] : null,
            ]);
            $count++;
        }
        return $count;
    }

    private function formData(): array
    {
        $body = AuthMiddleware::getJsonBody();
        return [
            'nume' => trim($body['nume'] ?? ''),
            'prenume' => trim($body['prenume'] ?? ''),
            'data_nasterii' => trim($body['data_nasterii'] ?? ''),
            'email' => trim($body['email'] ?? ''),
            'telefon' => trim($body['telefon'] ?? ''),
            'categorie' => trim($body['categorie'] ?? 'amator'),
            'rating' => (int) ($body['rating'] ?? 0),
            'adresa' => trim($body['adresa'] ?? ''),
            'coach_id' => isset($body['coach_id']) && $body['coach_id'] !== '' ? (int) $body['coach_id'] : null,
        ];
    }
}
