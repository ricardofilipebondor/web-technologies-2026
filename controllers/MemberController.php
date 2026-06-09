<?php

class MemberController
{
    public function index(): void
    {
        $this->checkAccess();
        $search = get('search');
        $categorie = get('categorie');
        render('members/index', [
            'members' => Member::all($search, $categorie),
            'search' => $search,
            'categorie' => $categorie,
        ]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('members/form', [
            'member' => null,
            'coaches' => Coach::getAntrenori(),
            'action' => 'members/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        $data = $this->getFormData();
        if ($data['nume'] === '' || $data['prenume'] === '' || $data['email'] === '') {
            setFlash('danger', 'Completati campurile obligatorii.');
            redirect('members/create');
        }
        Member::create($data);
        setFlash('success', 'Membru adaugat cu succes.');
        redirect('members/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $member = Member::find($id);
        if (!$member) {
            setFlash('danger', 'Membru negasit.');
            redirect('members/index');
        }
        render('members/form', [
            'member' => $member,
            'coaches' => Coach::getAntrenori(),
            'action' => 'members/update&id=' . $id,
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $data = $this->getFormData();
        Member::update($id, $data);
        setFlash('success', 'Membru actualizat.');
        redirect('members/index');
    }

    public function show(): void
    {
        $this->checkAccess();
        $id = (int) get('id');
        $profile = (new MembersService())->profile($id);
        if (!$profile) {
            setFlash('danger', 'Membru negasit.');
            redirect('members/index');
        }
        render('members/show', $profile);
    }

    public function delete(): void
    {
        $this->checkAccess();
        Member::delete((int) get('id'));
        setFlash('success', 'Membru sters.');
        redirect('members/index');
    }

    public function import(): void
    {
        $this->checkAccess();
        render('members/import');
    }

    public function doImport(): void
    {
        $this->checkAccess();
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            setFlash('danger', 'Selectati un fisier valid.');
            redirect('members/import');
        }

        $type = post('type');
        $tmp = $_FILES['file']['tmp_name'];
        $rows = [];

        if ($type === 'csv') {
            $rows = DataImporter::fromCsv($tmp);
        } elseif ($type === 'json') {
            $rows = DataImporter::fromJson($tmp);
        } elseif ($type === 'xml') {
            $rows = DataImporter::fromXml($tmp, 'member');
        }

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

        setFlash('success', "Import reusit: $count membri adaugati.");
        redirect('members/index');
    }

    public function export(): void
    {
        $this->checkAccess();
        $format = get('format', 'csv');
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
    }

    private function getFormData(): array
    {
        return [
            'nume' => post('nume'),
            'prenume' => post('prenume'),
            'data_nasterii' => post('data_nasterii'),
            'email' => post('email'),
            'telefon' => post('telefon'),
            'categorie' => post('categorie'),
            'rating' => (int) post('rating', 0),
            'adresa' => post('adresa'),
            'coach_id' => post('coach_id') !== '' ? (int) post('coach_id') : null,
        ];
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('members')) {
            setFlash('danger', 'Nu aveti acces la acest modul.');
            redirect('dashboard/index');
        }
    }
}
