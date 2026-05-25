<?php

require_once 'config/database.php';

try {
    $db = getDatabaseConnection();
    
    $db->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_name TEXT NOT NULL UNIQUE
        );
        
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_id INTEGER NOT NULL,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles (id)
        );
        
        INSERT OR IGNORE INTO roles (role_name) VALUES 
        ('admin'), 
        ('coach'), 
        ('collaborator'), 
        ('member_junior'), 
        ('member_senior'), 
        ('member_amateur'), 
        ('member_professional');
    ");
    
    echo "Database tables and roles initialized successfully!";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database Error: " . $e->getMessage();
}