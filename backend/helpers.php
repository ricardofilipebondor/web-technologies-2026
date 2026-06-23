<?php

function getMemberProfile(int $id): ?array
{
    $member = Member::findWithCoach($id);
    if (!$member) {
        return null;
    }
    return [
        'member' => $member,
        'participations' => Participation::byMember($id),
        'prizes' => Prize::byMember($id),
        'groups' => Member::getGroups($id),
    ];
}

function getTripReport(int $tripId): ?array
{
    $trip = Trip::find($tripId);
    if (!$trip) {
        return null;
    }
    return [
        'trip' => $trip,
        'members' => Trip::getMembers($tripId),
        'expenses' => Expense::byTrip($tripId),
        'total' => Trip::getTotalExpenses($tripId),
    ];
}

function getCompetitionReport(int $competitionId): array
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

function getTeamDetails(int $teamId): array
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

function exportList(string $format, array $data, string $name, array $headers, string $xmlRoot, string $xmlItem): void
{
    if ($format === 'csv') {
        $rows = array_map(fn($row) => array_values($row), $data);
        DataExporter::toCsv($rows, $headers, $name . '.csv');
    }
    if ($format === 'json') {
        DataExporter::toJson($data, $name . '.json');
    }
    if ($format === 'xml') {
        DataExporter::toXml($data, $xmlRoot, $xmlItem, $name . '.xml');
    }
    Response::problem('Format invalid.');
}
