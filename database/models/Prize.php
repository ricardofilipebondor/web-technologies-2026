<?php

class Prize
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT prizes.*,
                members.nume AS nume, members.prenume AS prenume,
                competitions.nume AS competition_nume
                FROM prizes
                JOIN members ON prizes.member_id = members.id
                LEFT JOIN competitions ON prizes.competition_id = competitions.id
                ORDER BY prizes.data_acordare DESC';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM prizes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO prizes (titlu, descriere, member_id, competition_id, data_acordare)
            VALUES (:titlu, :descriere, :member_id, :competition_id, :data_acordare)
        ');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE prizes SET
                titlu = :titlu, descriere = :descriere, member_id = :member_id,
                competition_id = :competition_id, data_acordare = :data_acordare
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM prizes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getRecent(int $limit = 5): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT prizes.*, members.nume, members.prenume
            FROM prizes
            JOIN members ON prizes.member_id = members.id
            ORDER BY prizes.data_acordare DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function byMember(int $memberId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT prizes.*, competitions.nume AS competition_nume
            FROM prizes
            LEFT JOIN competitions ON prizes.competition_id = competitions.id
            WHERE prizes.member_id = :mid
            ORDER BY prizes.data_acordare DESC
        ');
        $stmt->execute(['mid' => $memberId]);
        return $stmt->fetchAll();
    }

    public static function byCompetition(int $competitionId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT prizes.*, members.nume, members.prenume
            FROM prizes
            JOIN members ON prizes.member_id = members.id
            WHERE prizes.competition_id = :cid
            ORDER BY prizes.data_acordare DESC
        ');
        $stmt->execute(['cid' => $competitionId]);
        return $stmt->fetchAll();
    }
}
