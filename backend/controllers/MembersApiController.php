<?php

class MembersApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('members');
        $search = $_GET['search'] ?? '';
        $categorie = $_GET['categorie'] ?? '';
        Response::ok(Member::all($search, $categorie));
    }

    public function show(array $params): void
    {
        AuthMiddleware::requireModule('members');
        $profile = (new MembersService())->profile((int) $params['id']);
        if (!$profile) {
            Response::error('Membru negasit.', 404);
        }
        Response::ok($profile);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('members');
        $data = $this->formData();
        if ($data['nume'] === '' || $data['prenume'] === '' || $data['email'] === '') {
            Response::error('Completati campurile obligatorii.');
        }
        Member::create($data);
        Response::ok(null, 'Membru adaugat cu succes.');
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('members');
        Member::update((int) $params['id'], $this->formData());
        Response::ok(null, 'Membru actualizat.');
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('members');
        Member::delete((int) $params['id']);
        Response::ok(null, 'Membru sters.');
    }

    public function coaches(): void
    {
        AuthMiddleware::requireModule('members');
        Response::ok(Coach::getAntrenori());
    }

    public function export(): void
    {
        AuthMiddleware::requireModule('members');
        $format = $_GET['format'] ?? 'csv';
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
        Response::error('Format invalid.');
    }

    public function import(): void
    {
        AuthMiddleware::requireModule('members');
        $body = AuthMiddleware::getJsonBody();
        $rows = $body['rows'] ?? [];
        $count = $this->importRows($rows);
        Response::ok(['imported' => $count], "Import reusit: $count membri adaugati.");
    }

    public function importFile(): void
    {
        AuthMiddleware::requireModule('members');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            Response::error('Selectati un fisier valid.');
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
            Response::error('Format invalid.');
        }

        $count = $this->importRows($rows);
        Response::ok(['imported' => $count], "Import reusit: $count membri adaugati.");
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
