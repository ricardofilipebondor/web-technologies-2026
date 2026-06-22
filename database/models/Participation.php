<?php

class Participation
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT participations.*,
                members.nume AS member_nume, members.prenume AS member_prenume,
                competitions.nume AS competition_nume
                FROM participations
                JOIN members ON participations.member_id = members.id
                JOIN competitions ON participations.competition_id = competitions.id
                ORDER BY competitions.data DESC';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM participations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO participations (member_id, competition_id, punctaj, loc_obtinut)
            VALUES (:member_id, :competition_id, :punctaj, :loc_obtinut)
        ');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE participations SET
                member_id = :member_id, competition_id = :competition_id,
                punctaj = :punctaj, loc_obtinut = :loc_obtinut
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM participations WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getRanking(int $competitionId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT participations.punctaj, participations.loc_obtinut,
                   members.nume, members.prenume, members.id AS member_id
            FROM participations
            JOIN members ON participations.member_id = members.id
            WHERE participations.competition_id = :cid
            ORDER BY participations.punctaj DESC
        ');
        $stmt->execute(['cid' => $competitionId]);
        return $stmt->fetchAll();
    }

    public static function byCompetition(int $competitionId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT participations.*,
                   members.nume AS member_nume, members.prenume AS member_prenume,
                   members.categorie, members.rating
            FROM participations
            JOIN members ON participations.member_id = members.id
            WHERE participations.competition_id = :cid
            ORDER BY participations.punctaj DESC
        ');
        $stmt->execute(['cid' => $competitionId]);
        return $stmt->fetchAll();
    }

    public static function byMember(int $memberId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT participations.*, competitions.nume AS competition_nume,
                   competitions.data, competitions.domeniu, competitions.tip
            FROM participations
            JOIN competitions ON participations.competition_id = competitions.id
            WHERE participations.member_id = :mid
            ORDER BY competitions.data DESC
        ');
        $stmt->execute(['mid' => $memberId]);
        return $stmt->fetchAll();
    }
}
