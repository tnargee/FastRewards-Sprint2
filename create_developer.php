<?php
// Script to create a developer user account
require_once 'Database.php';
require_once 'Config.php';

try {
    $db = new Database();
    
    // Check if developer already exists
    $dev = $db->fetch(
        "SELECT id FROM fastrewards_users WHERE email = $1",
        ['dev@example.com']
    );
    
    if (!$dev) {
        // Create developer user
        $password_hash = password_hash('developer123', PASSWORD_DEFAULT);
        $db->query(
            "INSERT INTO fastrewards_users (first_name, last_name, email, password_hash, role) 
             VALUES ($1, $2, $3, $4, $5)",
            ['Dev', 'Admin', 'dev@example.com', $password_hash, 'developer']
        );
        
        echo "Developer user created successfully!\n";
        echo "Email: dev@example.com\n";
        echo "Password: developer123\n";
    } else {
        echo "Developer user already exists.\n";
        echo "Email: dev@example.com\n";
        echo "Password: developer123\n";
    }
    
} catch (Exception $e) {
    echo "Error creating developer user: " . $e->getMessage();
}
?> 