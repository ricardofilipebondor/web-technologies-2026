<?php

class PrizeController
{
    public function index(): void
    {
        $this->checkAccess();
        render('prizes/index', ['prizes' => Prize::all()]);
    }

    public function create(): void
    {
        $this->checkAccess();
        render('prizes/form', [
            'prize' => null,
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
            'action' => 'prizes/store',
        ]);
    }

    public function store(): void
    {
        $this->checkAccess();
        Prize::create([
            'titlu' => post('titlu'),
            'descriere' => post('descriere'),
            'member_id' => (int) post('member_id'),
            'competition_id' => post('competition_id') !== '' ? (int) post('competition_id') : null,
            'data_acordare' => post('data_acordare'),
        ]);
        setFlash('success', 'Premiu adaugat.');
        redirect('prizes/index');
    }

    public function edit(): void
    {
        $this->checkAccess();
        $prize = Prize::find((int) get('id'));
        if (!$prize) redirect('prizes/index');
        render('prizes/form', [
            'prize' => $prize,
            'members' => Member::getForSelect(),
            'competitions' => Competition::all(),
            'action' => 'prizes/update&id=' . $prize['id'],
        ]);
    }

    public function update(): void
    {
        $this->checkAccess();
        Prize::update((int) get('id'), [
            'titlu' => post('titlu'),
            'descriere' => post('descriere'),
            'member_id' => (int) post('member_id'),
            'competition_id' => post('competition_id') !== '' ? (int) post('competition_id') : null,
            'data_acordare' => post('data_acordare'),
        ]);
        setFlash('success', 'Premiu actualizat.');
        redirect('prizes/index');
    }

    public function delete(): void
    {
        $this->checkAccess();
        Prize::delete((int) get('id'));
        setFlash('success', 'Premiu sters.');
        redirect('prizes/index');
    }

    public function byMember(): void
    {
        $this->checkAccess();
        $memberId = (int) get('member_id');
        $member = Member::find($memberId);
        if (!$member) {
            setFlash('danger', 'Membru negasit.');
            redirect('members/index');
        }
        render('prizes/by_member', [
            'member' => $member,
            'prizes' => Prize::byMember($memberId),
        ]);
    }

    public function byCompetition(): void
    {
        $this->checkAccess();
        $compId = (int) get('competition_id');
        $competition = Competition::find($compId);
        if (!$competition) {
            setFlash('danger', 'Concurs negasit.');
            redirect('competitions/index');
        }
        render('prizes/by_competition', [
            'competition' => $competition,
            'prizes' => Prize::byCompetition($compId),
        ]);
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('prizes')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
