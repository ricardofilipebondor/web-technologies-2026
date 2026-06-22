<?php

class Competition
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        return $db->query('SELECT * FROM competitions ORDER BY data DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM competitions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO competitions (nume, locatie, data, tip, domeniu) VALUES (:nume, :locatie, :data, :tip, :domeniu)');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('UPDATE competitions SET nume = :nume, locatie = :locatie, data = :data, tip = :tip, domeniu = :domeniu WHERE id = :id');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM competitions WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = getDatabaseConnection();
        return (int) $db->query('SELECT COUNT(*) FROM competitions')->fetchColumn();
    }

    public static function getRecent(int $limit = 5): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM competitions ORDER BY data DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
