<?php

class AdminApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('admin');
        Response::ok([
            'users' => User::all(),
            'roles' => User::getRoles(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::requireModule('admin');
        $body = AuthMiddleware::getJsonBody();
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';
        $role = trim($body['role'] ?? 'antrenor');

        if ($username === '' || $email === '' || $password === '') {
            Response::error('Completati toate campurile.');
        }
        if (strlen($password) < 6) {
            Response::error('Parola trebuie sa aiba minim 6 caractere.');
        }

        try {
            User::createByAdmin($username, $email, $password, $role);
            Response::ok(null, 'Utilizator creat.');
        } catch (RuntimeException $e) {
            Response::error($e->getMessage(), 409);
        }
    }

    public function updateRole(array $params): void
    {
        AuthMiddleware::requireModule('admin');
        $body = AuthMiddleware::getJsonBody();
        $role = trim($body['role'] ?? '');

        if ($role === '') {
            Response::error('Rol invalid.');
        }

        $id = (int) $params['id'];
        if ($id === (int) ($_SESSION['user_id'] ?? 0)) {
            Response::error('Nu puteti modifica propriul rol.');
        }

        try {
            User::updateRole($id, $role);
            Response::ok(null, 'Rol actualizat.');
        } catch (RuntimeException $e) {
            Response::error($e->getMessage());
        }
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('admin');
        $id = (int) $params['id'];

        if ($id === (int) ($_SESSION['user_id'] ?? 0)) {
            Response::error('Nu puteti sterge propriul cont.');
        }

        User::delete($id);
        Response::ok(null, 'Utilizator sters.');
    }
}
