<?php

class AuthController
{
    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('dashboard/index');
        }
        renderLogin('auth/login');
    }

    public function doLogin(): void
    {
        $username = post('username');
        $password = post('password');

        if ($username === '' || $password === '') {
            setFlash('danger', 'Completati username si parola.');
            redirect('auth/login');
        }

        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];
            redirect('dashboard/index');
        }

        setFlash('danger', 'Username sau parola incorecte.');
        redirect('auth/login');
    }

    public function register(): void
    {
        if (isLoggedIn()) {
            redirect('dashboard/index');
        }
        renderLogin('auth/register');
    }

    public function doRegister(): void
    {
        $username = post('username');
        $email = post('email');
        $password = post('password');
        $passwordConfirm = post('password_confirm');
        $role = post('role', 'antrenor');

        if ($username === '' || $email === '' || $password === '') {
            setFlash('danger', 'Completati toate campurile.');
            redirect('auth/register');
        }

        if (strlen($username) < 3) {
            setFlash('danger', 'Username-ul trebuie sa aiba minim 3 caractere.');
            redirect('auth/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('danger', 'Email invalid.');
            redirect('auth/register');
        }

        if (strlen($password) < 6) {
            setFlash('danger', 'Parola trebuie sa aiba minim 6 caractere.');
            redirect('auth/register');
        }

        if ($password !== $passwordConfirm) {
            setFlash('danger', 'Parolele nu coincid.');
            redirect('auth/register');
        }

        if (!in_array($role, ['antrenor', 'responsabil_financiar'], true)) {
            setFlash('danger', 'Rol invalid pentru inregistrare.');
            redirect('auth/register');
        }

        if (User::findByUsername($username)) {
            setFlash('danger', 'Username-ul exista deja.');
            redirect('auth/register');
        }

        if (User::findByEmail($email)) {
            setFlash('danger', 'Email-ul este deja folosit.');
            redirect('auth/register');
        }

        try {
            User::create($username, $email, $password, $role);
            setFlash('success', 'Cont creat cu succes. Te poti autentifica.');
            redirect('auth/login');
        } catch (PDOException $e) {
            setFlash('danger', 'Eroare la crearea contului.');
            redirect('auth/register');
        }
    }

    public function logout(): void
    {
        session_destroy();
        session_start();
        redirect('auth/login');
    }
}
