<?php

class UsersApiController
{
    public function me(): void
    {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::user();
        Response::resource(Hateoas::item(
            RestHelper::userPayload($user),
            '/users/me',
            ['sessions' => '/sessions', 'menu' => '/menu']
        ));
    }

    public function index(): void
    {
        AuthMiddleware::requireModule('admin');
        RestHelper::index('admin', '/users', User::all());
    }

    public function store(): void
    {
        $body = AuthMiddleware::getJsonBody();

        if (AuthMiddleware::user()) {
            $this->storeAsAdmin($body);
            return;
        }

        $this->register($body);
    }

    public function update(array $params): void
    {
        AuthMiddleware::requireModule('admin');
        $body = AuthMiddleware::getJsonBody();
        $role = trim($body['role'] ?? '');

        if ($role === '') {
            Response::problem('Rol invalid.');
        }

        $id = (int) $params['id'];
        if ($id === AuthMiddleware::userId()) {
            Response::problem('Nu puteti modifica propriul rol.');
        }

        try {
            User::updateRole($id, $role);
            $user = User::findById($id);
            RestHelper::updated('/users/' . $id, RestHelper::userPayload($user));
        } catch (RuntimeException $e) {
            Response::problem($e->getMessage());
        }
    }

    public function delete(array $params): void
    {
        AuthMiddleware::requireModule('admin');
        $id = (int) $params['id'];

        if ($id === AuthMiddleware::userId()) {
            Response::problem('Nu puteti sterge propriul cont.');
        }

        User::delete($id);
        RestHelper::deleted();
    }

    private function register(array $body): void
    {
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';
        $passwordConfirm = $body['password_confirm'] ?? '';
        $role = $body['role'] ?? 'antrenor';

        if ($username === '' || $email === '' || $password === '') {
            Response::problem('Completati toate campurile.');
        }
        if (strlen($username) < 3) {
            Response::problem('Username-ul trebuie sa aiba minim 3 caractere.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::problem('Email invalid.');
        }
        if (strlen($password) < 6) {
            Response::problem('Parola trebuie sa aiba minim 6 caractere.');
        }
        if ($password !== $passwordConfirm) {
            Response::problem('Parolele nu coincid.');
        }
        if (!in_array($role, ['antrenor', 'responsabil_financiar'], true)) {
            Response::problem('Rol invalid pentru inregistrare.');
        }
        if (User::findByUsername($username)) {
            Response::problem('Username-ul exista deja.', 409);
        }
        if (User::findByEmail($email)) {
            Response::problem('Email-ul este deja folosit.', 409);
        }

        try {
            $id = User::create($username, $email, $password, $role);
            $user = User::findById($id);
            RestHelper::created('/users', $id, RestHelper::userPayload($user));
        } catch (Throwable $e) {
            Response::problem('Eroare la crearea contului.');
        }
    }

    private function storeAsAdmin(array $body): void
    {
        AuthMiddleware::requireModule('admin');
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';
        $role = trim($body['role'] ?? 'antrenor');

        if ($username === '' || $email === '' || $password === '') {
            Response::problem('Completati toate campurile.');
        }
        if (strlen($password) < 6) {
            Response::problem('Parola trebuie sa aiba minim 6 caractere.');
        }

        try {
            $id = User::createByAdmin($username, $email, $password, $role);
            $user = User::findById($id);
            RestHelper::created('/users', $id, RestHelper::userPayload($user));
        } catch (RuntimeException $e) {
            Response::problem($e->getMessage(), 409);
        }
    }
}
