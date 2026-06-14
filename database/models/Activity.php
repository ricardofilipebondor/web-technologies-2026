<?php

class Activity
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT activities.*, halls.denumire AS hall_name, coaches.nume AS coach_nume
                FROM activities
                JOIN halls ON activities.hall_id = halls.id
                JOIN coaches ON activities.coach_id = coaches.id
                ORDER BY activities.data_start DESC';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM activities WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO activities (titlu, tip, data_start, data_end, hall_id, coach_id)
            VALUES (:titlu, :tip, :data_start, :data_end, :hall_id, :coach_id)
        ');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('
            UPDATE activities SET
                titlu = :titlu, tip = :tip, data_start = :data_start,
                data_end = :data_end, hall_id = :hall_id, coach_id = :coach_id
            WHERE id = :id
        ');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM activities WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = getDatabaseConnection();
        return (int) $db->query('SELECT COUNT(*) FROM activities')->fetchColumn();
    }

    public static function getRecent(int $limit = 5): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT activities.*, halls.denumire AS hall_name
            FROM activities
            JOIN halls ON activities.hall_id = halls.id
            ORDER BY activities.data_start DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function hasHallConflict(int $hallId, string $start, string $end, ?int $excludeId = null): bool
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT COUNT(*) FROM activities
                WHERE hall_id = :hall_id
                AND data_start < :end AND data_end > :start';
        $params = ['hall_id' => $hallId, 'start' => $start, 'end' => $end];

        if ($excludeId) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function hasCoachConflict(int $coachId, string $start, string $end, ?int $excludeId = null): bool
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT COUNT(*) FROM activities
                WHERE coach_id = :coach_id
                AND data_start < :end AND data_end > :start';
        $params = ['coach_id' => $coachId, 'start' => $start, 'end' => $end];

        if ($excludeId) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
