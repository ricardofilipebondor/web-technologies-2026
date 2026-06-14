<?php

class AuthApiController
{
    public function login(): void
    {
        $body = AuthMiddleware::getJsonBody();
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        if ($username === '' || $password === '') {
            Response::error('Completati username si parola.');
        }

        $user = User::findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::error('Username sau parola incorecte.', 401);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];

        Response::ok([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role_name'],
        ], 'Autentificare reusita.');
    }

    public function register(): void
    {
        $body = AuthMiddleware::getJsonBody();
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';
        $passwordConfirm = $body['password_confirm'] ?? '';
        $role = $body['role'] ?? 'antrenor';

        if ($username === '' || $email === '' || $password === '') {
            Response::error('Completati toate campurile.');
        }
        if (strlen($username) < 3) {
            Response::error('Username-ul trebuie sa aiba minim 3 caractere.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Email invalid.');
        }
        if (strlen($password) < 6) {
            Response::error('Parola trebuie sa aiba minim 6 caractere.');
        }
        if ($password !== $passwordConfirm) {
            Response::error('Parolele nu coincid.');
        }
        if (!in_array($role, ['antrenor', 'responsabil_financiar'], true)) {
            Response::error('Rol invalid pentru inregistrare.');
        }
        if (User::findByUsername($username)) {
            Response::error('Username-ul exista deja.', 409);
        }
        if (User::findByEmail($email)) {
            Response::error('Email-ul este deja folosit.', 409);
        }

        try {
            User::create($username, $email, $password, $role);
            Response::ok(null, 'Cont creat cu succes.');
        } catch (Throwable $e) {
            Response::error('Eroare la crearea contului.');
        }
    }

    public function me(): void
    {
        AuthMiddleware::requireLogin();
        $user = User::findById((int) $_SESSION['user_id']);
        if (!$user) {
            Response::error('Utilizator negasit.', 404);
        }
        Response::ok([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role_name'],
        ]);
    }

    public function logout(): void
    {
        session_destroy();
        Response::ok(null, 'Delogat cu succes.');
    }

    public function menu(): void
    {
        AuthMiddleware::requireLogin();
        $items = [];
        foreach (PluginManager::getMenuItems() as $item) {
            if (userCanAccess($item['module'])) {
                $items[] = $item;
            }
        }
        Response::ok($items);
    }
}
