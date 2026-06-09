<?php

class CompetitionsService
{
    public function list(): array
    {
        return Competition::all();
    }

    public function participationsReport(int $competitionId): array
    {
        $competition = Competition::find($competitionId);
        if (!$competition) {
            return ['competition' => null, 'participations' => [], 'ranking' => []];
        }
        return [
            'competition' => $competition,
            'participations' => Participation::byCompetition($competitionId),
            'ranking' => Participation::getRanking($competitionId),
        ];
    }
}
