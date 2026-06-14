<?php

class Expense
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT expenses.*, trips.destinatie AS trip_destinatie
                FROM expenses
                JOIN trips ON expenses.trip_id = trips.id
                ORDER BY expenses.id DESC';
        return $db->query($sql)->fetchAll();
    }

    public static function byTrip(int $tripId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM expenses WHERE trip_id = :tid ORDER BY id');
        $stmt->execute(['tid' => $tripId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO expenses (trip_id, tip, suma, observatii) VALUES (:trip_id, :tip, :suma, :observatii)');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('UPDATE expenses SET trip_id = :trip_id, tip = :tip, suma = :suma, observatii = :observatii WHERE id = :id');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
