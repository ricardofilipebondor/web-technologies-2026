<?php

class HallSlot
{
    public static function byHall(int $hallId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM hall_slots WHERE hall_id = :hid ORDER BY zi_saptamana, ora_start');
        $stmt->execute(['hid' => $hallId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO hall_slots (hall_id, zi_saptamana, ora_start, ora_end) VALUES (:hall_id, :zi_saptamana, :ora_start, :ora_end)');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM hall_slots WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
