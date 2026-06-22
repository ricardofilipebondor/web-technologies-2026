<?php

class SessionsApiController
{
    public function store(): void
    {
        $body = AuthMiddleware::getJsonBody();
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        if ($username === '' || $password === '') {
            Response::problem('Completati username si parola.');
        }

        $user = User::findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::problem('Username sau parola incorecte.', 401);
        }

        $token = JwtService::encode([
            'sub' => (int) $user['id'],
            'role' => $user['role_name'],
        ]);

        $userPayload = RestHelper::userPayload($user);
        Response::created([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'expires_in' => JWT_TTL,
            'user' => Hateoas::item($userPayload, '/users/me', ['sessions' => '/sessions']),
            '_links' => Hateoas::links(['self' => '/sessions', 'user' => '/users/me']),
        ], '/sessions');
    }

    public function destroy(): void
    {
        Response::noContent();
    }
}
