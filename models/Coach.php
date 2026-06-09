<?php

class Coach
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        return $db->query('SELECT * FROM coaches ORDER BY nume')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM coaches WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO coaches (nume, email, telefon, specializare, disponibilitate, rol)
            VALUES (:nume, :email, :telefon, :specializare, :disponibilitate, :rol)
        ');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE coaches SET
                nume = :nume, email = :email, telefon = :telefon,
                specializare = :specializare, disponibilitate = :disponibilitate, rol = :rol
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM coaches WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = getDatabaseConnection();
        return (int) $db->query("SELECT COUNT(*) FROM coaches WHERE rol = 'antrenor'")->fetchColumn();
    }

    public static function getAntrenori(): array
    {
        $db = getDatabaseConnection();
        return $db->query("SELECT * FROM coaches WHERE rol = 'antrenor' ORDER BY nume")->fetchAll();
    }
}
