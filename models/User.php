<?php

class User
{
    public static function findByUsername(string $username): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT users.*, roles.role_name
            FROM users
            JOIN roles ON users.role_id = roles.id
            WHERE users.username = :username
        ');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT users.*, roles.role_name
            FROM users
            JOIN roles ON users.role_id = roles.id
            WHERE users.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(string $username, string $email, string $password, string $roleName = 'antrenor'): void
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT id FROM roles WHERE role_name = :role');
        $stmt->execute(['role' => $roleName]);
        $role = $stmt->fetch();
        if (!$role) {
            throw new RuntimeException('Rol invalid.');
        }

        $stmt = $db->prepare('
            INSERT INTO users (role_id, username, email, password_hash)
            VALUES (:role_id, :username, :email, :password_hash)
        ');
        $stmt->execute([
            'role_id' => $role['id'],
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
