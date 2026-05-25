<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/jwt_secret.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing credentials']);
    exit;
}

$db = getDatabaseConnection();
$stmt = $db->prepare('SELECT id, role_id, password_hash FROM users WHERE username = :username');
$stmt->execute(['username' => $input['username']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'])) {
    $payload = [
        'iss' => 'http://localhost',
        'aud' => 'http://localhost',
        'iat' => time(),
        'exp' => time() + 3600,
        'user_id' => $user['id'],
        'role_id' => $user['role_id']
    ];
    
    $jwt = JWT::encode($payload, JWT_SECRET, 'HS256');
    
    echo json_encode(['message' => 'Login successful', 'token' => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}