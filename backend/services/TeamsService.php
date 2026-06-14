<?php

class TeamsService
{
    public function list(): array
    {
        return Team::all();
    }

    public function performanceHistory(int $teamId): array
    {
        $team = Team::find($teamId);
        if (!$team) {
            return [];
        }
        return [
            'team' => $team,
            'members' => Team::getMembers($teamId),
            'results' => Team::getResults($teamId),
        ];
    }
}
