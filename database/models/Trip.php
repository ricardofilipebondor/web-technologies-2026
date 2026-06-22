<?php

class Trip
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT trips.*, teams.denumire AS team_nume,
                (SELECT COALESCE(SUM(suma), 0) FROM expenses WHERE trip_id = trips.id) AS total_cheltuieli,
                (SELECT COUNT(*) FROM trip_members WHERE trip_id = trips.id) AS member_count
                FROM trips
                LEFT JOIN teams ON trips.team_id = teams.id
                ORDER BY trips.data_plecare DESC';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT trips.*, teams.denumire AS team_nume
            FROM trips
            LEFT JOIN teams ON trips.team_id = teams.id
            WHERE trips.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO trips (destinatie, data_plecare, data_intoarcere, scop, team_id)
            VALUES (:destinatie, :data_plecare, :data_intoarcere, :scop, :team_id)
        ');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE trips SET
                destinatie = :destinatie, data_plecare = :data_plecare,
                data_intoarcere = :data_intoarcere, scop = :scop, team_id = :team_id
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM trips WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = getDatabaseConnection();
        return (int) $db->query('SELECT COUNT(*) FROM trips')->fetchColumn();
    }

    public static function getTotalExpenses(int $tripId): float
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT COALESCE(SUM(suma), 0) FROM expenses WHERE trip_id = :tid');
        $stmt->execute(['tid' => $tripId]);
        return (float) $stmt->fetchColumn();
    }

    public static function getMembers(int $tripId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT members.* FROM members
            JOIN trip_members ON members.id = trip_members.member_id
            WHERE trip_members.trip_id = :tid ORDER BY members.nume
        ');
        $stmt->execute(['tid' => $tripId]);
        return $stmt->fetchAll();
    }

    public static function addMember(int $tripId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT OR IGNORE INTO trip_members (trip_id, member_id) VALUES (:tid, :mid)');
        $stmt->execute(['tid' => $tripId, 'mid' => $memberId]);
    }

    public static function removeMember(int $tripId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM trip_members WHERE trip_id = :tid AND member_id = :mid');
        $stmt->execute(['tid' => $tripId, 'mid' => $memberId]);
    }

    public static function getAvailableMembers(int $tripId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT * FROM members WHERE id NOT IN (SELECT member_id FROM trip_members WHERE trip_id = :tid)
            ORDER BY nume
        ');
        $stmt->execute(['tid' => $tripId]);
        return $stmt->fetchAll();
    }
}
