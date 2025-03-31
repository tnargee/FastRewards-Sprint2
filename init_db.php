<?php
require_once 'Database.php';
require_once 'Config.php';

try {
    $db = new Database();
    
    // Drop existing tables if they exist
    $db->query("DROP TABLE IF EXISTS fastrewards_users CASCADE");
    
    // Create sequences
    $db->query("DROP SEQUENCE IF EXISTS fastrewards_users_id_seq CASCADE");
    $db->query("CREATE SEQUENCE fastrewards_users_id_seq");
    
    // Create users table
    $db->query("CREATE TABLE fastrewards_users (
        id INTEGER PRIMARY KEY DEFAULT nextval('fastrewards_users_id_seq'),
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "Database tables and sequences created successfully!";
    
} catch (Exception $e) {
    echo "Error creating database tables: " . $e->getMessage();
}
?> 