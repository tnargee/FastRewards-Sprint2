<?php
// Create menu items table script

require_once 'Database.php';
require_once 'Config.php';

try {
    $db = new Database();
    
    // Try creating the table - if it exists, it will fail
    try {
        // Create menu items table
        $sql = "CREATE TABLE fastrewards_menu_items (
            id SERIAL PRIMARY KEY,
            restaurant_id INTEGER NOT NULL REFERENCES fastrewards_restaurants(id) ON DELETE CASCADE,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->query($sql);
        
        // Add some sample menu items
        $db->query("INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price) 
                    VALUES (1, 'Big Mac', 'Classic burger with two beef patties', 5.99)");
        $db->query("INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price) 
                    VALUES (1, 'McChicken', 'Chicken sandwich with mayo', 3.99)");
        $db->query("INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price) 
                    VALUES (2, 'Burrito Bowl', 'Rice, beans, and your choice of protein', 9.99)");
        $db->query("INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price) 
                    VALUES (3, 'Caramel Macchiato', 'Espresso with vanilla and caramel', 4.95)");
        $db->query("INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price) 
                    VALUES (4, 'Hoagie', 'Classic Italian sandwich', 7.50)");
        
        echo "Table fastrewards_menu_items created successfully with sample data.\n";
    } catch (Exception $tableExistsException) {
        // Check if the error is because the table already exists
        if (strpos($tableExistsException->getMessage(), 'already exists') !== false) {
            echo "Table fastrewards_menu_items already exists.\n";
        } else {
            // Rethrow if it's a different error
            throw $tableExistsException;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 