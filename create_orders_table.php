<?php
// Create orders table script

require_once 'Database.php';
require_once 'Config.php';

try {
    $db = new Database();
    
    // Try creating the table - if it exists, it will fail
    try {
        // Create orders table
        $sql = "CREATE TABLE fastrewards_orders (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL REFERENCES fastrewards_users(id) ON DELETE CASCADE,
            restaurant_id INTEGER NOT NULL REFERENCES fastrewards_restaurants(id) ON DELETE CASCADE,
            total_amount DECIMAL(10,2) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'completed',
            points_earned INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->query($sql);
        
        // Create order items table for storing individual items in an order
        $sql2 = "CREATE TABLE IF NOT EXISTS fastrewards_order_items (
            id SERIAL PRIMARY KEY,
            order_id INTEGER NOT NULL REFERENCES fastrewards_orders(id) ON DELETE CASCADE,
            deal_id INTEGER REFERENCES fastrewards_deals(id) ON DELETE SET NULL,
            item_name VARCHAR(255) NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->query($sql2);
        
        // Add some sample orders for the developer user (id=1)
        $db->query("INSERT INTO fastrewards_orders (user_id, restaurant_id, total_amount, points_earned) 
                    VALUES (1, 1, 15.99, 160)");
        $db->query("INSERT INTO fastrewards_orders (user_id, restaurant_id, total_amount, points_earned) 
                    VALUES (1, 2, 22.50, 225)");
        $db->query("INSERT INTO fastrewards_orders (user_id, restaurant_id, total_amount, points_earned) 
                    VALUES (1, 3, 9.95, 100)");
        
        // Add some sample order items
        $lastOrderId = $db->query("SELECT currval('fastrewards_orders_id_seq')");
        $orderId = $lastOrderId[0]['currval'] - 2; // First order ID
        
        $db->query("INSERT INTO fastrewards_order_items (order_id, item_name, quantity, price) 
                    VALUES ($orderId, 'Big Mac', 2, 11.98)");
        $db->query("INSERT INTO fastrewards_order_items (order_id, item_name, quantity, price) 
                    VALUES ($orderId, 'French Fries', 1, 4.01)");
        
        $orderId++; // Second order ID
        $db->query("INSERT INTO fastrewards_order_items (order_id, item_name, quantity, price) 
                    VALUES ($orderId, 'Burrito Bowl', 2, 19.98)");
        $db->query("INSERT INTO fastrewards_order_items (order_id, item_name, quantity, price) 
                    VALUES ($orderId, 'Chips and Guacamole', 1, 2.52)");
        
        $orderId++; // Third order ID
        $db->query("INSERT INTO fastrewards_order_items (order_id, item_name, quantity, price) 
                    VALUES ($orderId, 'Caramel Macchiato', 2, 9.95)");
        
        echo "Tables fastrewards_orders and fastrewards_order_items created successfully with sample data.\n";
    } catch (Exception $tableExistsException) {
        // Check if the error is because the table already exists
        if (strpos($tableExistsException->getMessage(), 'already exists') !== false) {
            echo "Table fastrewards_orders already exists.\n";
        } else {
            // Rethrow if it's a different error
            throw $tableExistsException;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 