<?php

class Hall
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        return $db->query('SELECT * FROM halls ORDER BY denumire')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM halls WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO halls (denumire, capacitate, dotari) VALUES (:denumire, :capacitate, :dotari)');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('UPDATE halls SET denumire = :denumire, capacitate = :capacitate, dotari = :dotari WHERE id = :id');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM halls WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
