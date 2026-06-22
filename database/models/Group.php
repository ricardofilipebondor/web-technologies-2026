<?php

class Group
{
    public static function all(): array
    {
        $db = getDatabaseConnection();
        $sql = 'SELECT groups.*, coaches.nume AS coach_nume,
                (SELECT COUNT(*) FROM group_members WHERE group_id = groups.id) AS member_count
                FROM groups
                LEFT JOIN coaches ON groups.coach_id = coaches.id
                ORDER BY groups.denumire';
        return $db->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM groups WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO groups (denumire, nivel, coach_id) VALUES (:denumire, :nivel, :coach_id)');
        $stmt->execute($data);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = getDatabaseConnection();
        $data['id'] = $id;
        $stmt = $db->prepare('UPDATE groups SET denumire = :denumire, nivel = :nivel, coach_id = :coach_id WHERE id = :id');
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM groups WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getMembers(int $groupId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT members.* FROM members
            JOIN group_members ON members.id = group_members.member_id
            WHERE group_members.group_id = :gid
            ORDER BY members.nume
        ');
        $stmt->execute(['gid' => $groupId]);
        return $stmt->fetchAll();
    }

    public static function addMember(int $groupId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT OR IGNORE INTO group_members (group_id, member_id) VALUES (:gid, :mid)');
        $stmt->execute(['gid' => $groupId, 'mid' => $memberId]);
    }

    public static function removeMember(int $groupId, int $memberId): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM group_members WHERE group_id = :gid AND member_id = :mid');
        $stmt->execute(['gid' => $groupId, 'mid' => $memberId]);
    }

    public static function getAvailableMembers(int $groupId): array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT * FROM members
            WHERE id NOT IN (SELECT member_id FROM group_members WHERE group_id = :gid)
            ORDER BY nume
        ');
        $stmt->execute(['gid' => $groupId]);
        return $stmt->fetchAll();
    }
}
