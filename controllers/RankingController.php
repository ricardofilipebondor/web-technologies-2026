<?php

class RankingController
{
    public function index(): void
    {
        $this->checkAccess();
        $competitionId = (int) get('competition_id');
        $ranking = [];
        $competition = null;

        if ($competitionId > 0) {
            $competition = Competition::find($competitionId);
            $ranking = Participation::getRanking($competitionId);
        }

        render('rankings/index', [
            'competitions' => Competition::all(),
            'competitionId' => $competitionId,
            'competition' => $competition,
            'ranking' => $ranking,
        ]);
    }

    public function export(): void
    {
        $this->checkAccess();
        $competitionId = (int) get('competition_id');
        $format = get('format', 'csv');
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
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('rankings')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
