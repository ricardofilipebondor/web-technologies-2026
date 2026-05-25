<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$db = getDatabaseConnection();
$stmt = $db->prepare('SELECT id FROM roles WHERE role_name = :role');
$stmt->execute(['role' => 'member_amateur']);
$role = $stmt->fetch();

if (!$role) {
    http_response_code(500);
    echo json_encode(['error' => 'Default role not found']);
    exit;
}

$hash = password_hash($input['password'], PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare('INSERT INTO users (role_id, username, email, password_hash) VALUES (:role_id, :username, :email, :password_hash)');
    $stmt->execute([
        'role_id' => $role['id'],
        'username' => $input['username'],
        'email' => $input['email'],
        'password_hash' => $hash
    ]);
    
    http_response_code(201);
    echo json_encode(['message' => 'User registered successfully']);
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode(['error' => 'Username or email already exists']);
}