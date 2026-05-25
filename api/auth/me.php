<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/jwt_secret.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token missing or malformed']);
    exit;
}

$jwt = $matches[1];

try {
    $decoded = JWT::decode($jwt, new Key(JWT_SECRET, 'HS256'));
    
    $db = getDatabaseConnection();
    $stmt = $db->prepare('
        SELECT u.id, u.username, u.email, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.id 
        WHERE u.id = :id
    ');
    $stmt->execute(['id' => $decoded->user_id]);
    $user = $stmt->fetch();

    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
}