<?php
// Author: Tenzin Nargee, Kyle Vitayanuvatti

require_once 'Database.php';
require_once 'Config.php';

try {
    $db = new Database();
    
    // Drop existing tables if they exist
    $db->query("DROP TABLE IF EXISTS fastrewards_transactions CASCADE");
    $db->query("DROP TABLE IF EXISTS fastrewards_point_balances CASCADE");
    $db->query("DROP TABLE IF EXISTS fastrewards_restaurants CASCADE");
    $db->query("DROP TABLE IF EXISTS fastrewards_users CASCADE");
    
    // Drop sequences
    $db->query("DROP SEQUENCE IF EXISTS fastrewards_users_id_seq CASCADE");
    $db->query("DROP SEQUENCE IF EXISTS fastrewards_restaurants_id_seq CASCADE");
    $db->query("DROP SEQUENCE IF EXISTS fastrewards_transactions_id_seq CASCADE");
    
    // Create sequences
    $db->query("CREATE SEQUENCE fastrewards_users_id_seq");
    $db->query("CREATE SEQUENCE fastrewards_restaurants_id_seq");
    $db->query("CREATE SEQUENCE fastrewards_transactions_id_seq");
    
    // Create users table
    $db->query("CREATE TABLE fastrewards_users (
        id INTEGER PRIMARY KEY DEFAULT nextval('fastrewards_users_id_seq'),
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create restaurants table
    $db->query("CREATE TABLE fastrewards_restaurants (
        id INTEGER PRIMARY KEY DEFAULT nextval('fastrewards_restaurants_id_seq'),
        name VARCHAR(255) NOT NULL,
        logo_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create point balances table
    $db->query("CREATE TABLE fastrewards_point_balances (
        user_id INTEGER NOT NULL REFERENCES fastrewards_users(id) ON DELETE CASCADE,
        restaurant_id INTEGER NOT NULL REFERENCES fastrewards_restaurants(id) ON DELETE CASCADE,
        points INTEGER NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, restaurant_id)
    )");
    
    // Create transactions table
    $db->query("CREATE TABLE fastrewards_transactions (
        id INTEGER PRIMARY KEY DEFAULT nextval('fastrewards_transactions_id_seq'),
        user_id INTEGER NOT NULL REFERENCES fastrewards_users(id) ON DELETE CASCADE,
        from_restaurant_id INTEGER REFERENCES fastrewards_restaurants(id) ON DELETE CASCADE,
        to_restaurant_id INTEGER REFERENCES fastrewards_restaurants(id) ON DELETE CASCADE,
        points_transferred INTEGER NOT NULL,
        points_received INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert default restaurants
    $db->query("INSERT INTO fastrewards_restaurants (name, logo_path) VALUES ('McDonald''s', 'assets/mcd.png')");
    $db->query("INSERT INTO fastrewards_restaurants (name, logo_path) VALUES ('Chipotle', 'assets/chipotle.png')");
    $db->query("INSERT INTO fastrewards_restaurants (name, logo_path) VALUES ('Starbucks', 'assets/starbucks.png')");
    $db->query("INSERT INTO fastrewards_restaurants (name, logo_path) VALUES ('Wawa', 'assets/wawa.png')");
    
    echo "Database tables and sequences created successfully!";
    
} catch (Exception $e) {
    echo "Error creating database tables: " . $e->getMessage();
}
?> 