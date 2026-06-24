<?php

class HallSlot
{
    private const DAYS = ['Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica'];

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

    public static function normalizeTime(string $time): ?string
    {
        $time = trim($time);
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $time, $m)) {
            return null;
        }
        $hours = (int) $m[1];
        $minutes = (int) $m[2];
        if ($hours > 23 || $minutes > 59) {
            return null;
        }
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function dayNameFromDate(string $datetime): ?string
    {
        $ts = strtotime($datetime);
        if ($ts === false) {
            return null;
        }
        $index = (int) date('N', $ts) - 1;
        return self::DAYS[$index] ?? null;
    }

    public static function validateNewSlot(int $hallId, string $day, string $oraStart, string $oraEnd, ?int $excludeId = null): ?string
    {
        $day = trim($day);
        if ($day === '' || !in_array($day, self::DAYS, true)) {
            return 'Zi saptamana invalida.';
        }

        $start = self::normalizeTime($oraStart);
        $end = self::normalizeTime($oraEnd);
        if ($start === null || $end === null) {
            return 'Format ora invalid. Folositi HH:MM (ex: 10:00).';
        }
        if ($start >= $end) {
            return 'Ora de sfarsit trebuie sa fie dupa ora de inceput.';
        }

        foreach (self::byHall($hallId) as $slot) {
            if ($excludeId !== null && (int) $slot['id'] === $excludeId) {
                continue;
            }
            if ($slot['zi_saptamana'] !== $day) {
                continue;
            }
            $existingStart = self::normalizeTime($slot['ora_start']) ?? $slot['ora_start'];
            $existingEnd = self::normalizeTime($slot['ora_end']) ?? $slot['ora_end'];
            if (self::timesOverlap($start, $end, $existingStart, $existingEnd)) {
                return 'Intervalul se suprapune cu un interval existent in aceeasi zi.';
            }
        }

        return null;
    }

    public static function fitsHallSchedule(int $hallId, string $dataStart, string $dataEnd): ?string
    {
        $slots = self::byHall($hallId);
        if ($slots === []) {
            return null;
        }

        $startTs = strtotime($dataStart);
        $endTs = strtotime($dataEnd);
        if ($startTs === false || $endTs === false) {
            return 'Data activitatii invalida.';
        }

        if (date('Y-m-d', $startTs) !== date('Y-m-d', $endTs)) {
            return 'Activitatea trebuie sa inceapa si sa se incheie in aceeasi zi.';
        }

        $dayName = self::dayNameFromDate($dataStart);
        if ($dayName === null) {
            return 'Data activitatii invalida.';
        }

        $actStart = self::normalizeTime(date('H:i', $startTs));
        $actEnd = self::normalizeTime(date('H:i', $endTs));
        if ($actStart === null || $actEnd === null) {
            return 'Ora activitatii invalida.';
        }

        $daySlots = array_filter($slots, fn($s) => $s['zi_saptamana'] === $dayName);
        if ($daySlots === []) {
            return 'Sala nu are intervale disponibile in ' . $dayName . '.';
        }

        foreach ($daySlots as $slot) {
            $slotStart = self::normalizeTime($slot['ora_start']) ?? $slot['ora_start'];
            $slotEnd = self::normalizeTime($slot['ora_end']) ?? $slot['ora_end'];
            if (self::timeToMinutes($slotStart) <= self::timeToMinutes($actStart)
                && self::timeToMinutes($actEnd) <= self::timeToMinutes($slotEnd)) {
                return null;
            }
        }

        return 'Activitatea nu se incadreaza in intervalele disponibile ale salii.';
    }

    private static function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $a = self::timeToMinutes($start1);
        $b = self::timeToMinutes($end1);
        $c = self::timeToMinutes($start2);
        $d = self::timeToMinutes($end2);

        return $a < $d && $c < $b;
    }

    private static function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return $hours * 60 + $minutes;
    }
}
