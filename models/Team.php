<?php

class Team
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT teams.*,
                (SELECT COUNT(*) FROM team_members WHERE team_id = teams.id) AS member_count
                FROM teams ORDER BY teams.denumire';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM teams WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO teams (denumire, descriere) VALUES (:denumire, :descriere)');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('UPDATE teams SET denumire = :denumire, descriere = :descriere WHERE id = :id');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM teams WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getMembers(int $teamId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT members.* FROM members
            JOIN team_members ON members.id = team_members.member_id
            WHERE team_members.team_id = :tid ORDER BY members.nume
        ');
        $stmt->execute(['tid' => $teamId]);
        return $stmt->fetchAll();
    }

    public static function addMember(int $teamId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT OR IGNORE INTO team_members (team_id, member_id) VALUES (:tid, :mid)');
        $stmt->execute(['tid' => $teamId, 'mid' => $memberId]);
    }

    public static function removeMember(int $teamId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM team_members WHERE team_id = :tid AND member_id = :mid');
        $stmt->execute(['tid' => $teamId, 'mid' => $memberId]);
    }

    public static function getAvailableMembers(int $teamId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT * FROM members WHERE id NOT IN (SELECT member_id FROM team_members WHERE team_id = :tid)
            ORDER BY nume
        ');
        $stmt->execute(['tid' => $teamId]);
        return $stmt->fetchAll();
    }

    public static function getResults(int $teamId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT team_results.*, competitions.nume AS competition_nume, competitions.data
            FROM team_results
            JOIN competitions ON team_results.competition_id = competitions.id
            WHERE team_results.team_id = :tid
            ORDER BY competitions.data DESC
        ');
        $stmt->execute(['tid' => $teamId]);
        return $stmt->fetchAll();
    }

    public static function addResult(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO team_results (team_id, competition_id, punctaj_total, loc_obtinut, observatii)
            VALUES (:team_id, :competition_id, :punctaj_total, :loc_obtinut, :observatii)
        ');
        $stmt->execute($data);
    }

    public static function deleteResult(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM team_results WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getForSelect(): array
    {
        $db = getDatabaseConnection();
        return $db->query('SELECT id, denumire FROM teams ORDER BY denumire')->fetchAll();
    }
}
