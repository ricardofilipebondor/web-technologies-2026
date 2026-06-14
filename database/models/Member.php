<?php

class Member
{
    public static function all(string $search = '', string $categorie = ''): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT members.*, coaches.nume AS coach_nume
                FROM members
                LEFT JOIN coaches ON members.coach_id = coaches.id
                WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (members.nume LIKE :s OR members.prenume LIKE :s OR members.email LIKE :s)';
            $params['s'] = '%' . $search . '%';
        }
        if ($categorie !== '') {
            $sql .= ' AND members.categorie = :cat';
            $params['cat'] = $categorie;
        }

        $sql .= ' ORDER BY members.nume, members.prenume';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM members WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO members (nume, prenume, data_nasterii, email, telefon, categorie, rating, adresa, coach_id)
            VALUES (:nume, :prenume, :data_nasterii, :email, :telefon, :categorie, :rating, :adresa, :coach_id)
        ');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE members SET
                nume = :nume, prenume = :prenume, data_nasterii = :data_nasterii,
                email = :email, telefon = :telefon, categorie = :categorie,
                rating = :rating, adresa = :adresa, coach_id = :coach_id
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM members WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = getDatabaseConnection();
        return (int) $db->query('SELECT COUNT(*) FROM members')->fetchColumn();
    }

    public static function getForSelect(): array
    {
        $db = getDatabaseConnection();
        return $db->query('SELECT id, nume, prenume FROM members ORDER BY nume')->fetchAll();
    }

    public static function findWithCoach(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT members.*, coaches.nume AS coach_nume
            FROM members
            LEFT JOIN coaches ON members.coach_id = coaches.id
            WHERE members.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getGroups(int $memberId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT groups.* FROM groups
            JOIN group_members ON groups.id = group_members.group_id
            WHERE group_members.member_id = :mid
        ');
        $stmt->execute(['mid' => $memberId]);
        return $stmt->fetchAll();
    }
}
