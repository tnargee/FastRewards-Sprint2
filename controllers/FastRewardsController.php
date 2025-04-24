<?php

class FastRewardsController {
    // Add the redirect method
    private function redirect($path) {
        // Remove leading slash if present
        $path = ltrim($path, '/');
        header("Location: index.php?command=" . $path);
        exit();
    }

    public function processDeal() {
        // Check if the user is logged in and is a developer
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'developer') {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'add') {
                // Add a new deal
                $restaurantId = $_POST['restaurant_id'] ?? '';
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $pointsRequired = $_POST['points_required'] ?? '';
                
                $targetDir = __DIR__ . "/../public/uploads/deals/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $targetDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imagePath = '/uploads/deals/' . $fileName;
                    }
                }
                
                $sql = "INSERT INTO fastrewards_deals (restaurant_id, title, description, points_required, image_path) 
                        VALUES (?, ?, ?, ?, ?)";
                $this->db->query($sql, [$restaurantId, $title, $description, $pointsRequired, $imagePath]);
                
                $this->redirect('/manage_deals');
            } elseif ($action === 'edit') {
                // Edit an existing deal
                $dealId = $_POST['deal_id'] ?? '';
                $restaurantId = $_POST['restaurant_id'] ?? '';
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $pointsRequired = $_POST['points_required'] ?? '';
                
                $imagePath = $_POST['current_image'] ?? '';
                
                $targetDir = __DIR__ . "/../public/uploads/deals/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $targetDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        // Delete previous image if exists
                        if ($imagePath && file_exists(__DIR__ . "/../public" . $imagePath)) {
                            unlink(__DIR__ . "/../public" . $imagePath);
                        }
                        $imagePath = '/uploads/deals/' . $fileName;
                    }
                }
                
                $sql = "UPDATE fastrewards_deals 
                        SET restaurant_id = ?, title = ?, description = ?, points_required = ?, image_path = ? 
                        WHERE id = ?";
                $this->db->query($sql, [$restaurantId, $title, $description, $pointsRequired, $imagePath, $dealId]);
                
                $this->redirect('/manage_deals');
            } elseif ($action === 'delete') {
                // Delete a deal
                $dealId = $_POST['deal_id'] ?? '';
                
                // Get image path first to delete the file
                $sql = "SELECT image_path FROM fastrewards_deals WHERE id = ?";
                $deal = $this->db->query($sql, [$dealId])->fetch(PDO::FETCH_ASSOC);
                
                if ($deal && $deal['image_path'] && file_exists(__DIR__ . "/../public" . $deal['image_path'])) {
                    unlink(__DIR__ . "/../public" . $deal['image_path']);
                }
                
                $sql = "DELETE FROM fastrewards_deals WHERE id = ?";
                $this->db->query($sql, [$dealId]);
                
                $this->redirect('/manage_deals');
            }
        }
    }

    public function showManageMenuItems() {
        // Check if the user is logged in and is a developer
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'developer') {
            $this->redirect('/signin');
            return;
        }

        // Get all restaurants
        $restaurants = $this->db->query("SELECT * FROM fastrewards_restaurants ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        // Get all menu items
        $menuItems = $this->db->query("
            SELECT mi.*, r.name as restaurant_name
            FROM fastrewards_menu_items mi
            JOIN fastrewards_restaurants r ON mi.restaurant_id = r.id
            ORDER BY r.name, mi.name
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('manage_menu_items', [
            'restaurants' => $restaurants,
            'menuItems' => $menuItems
        ]);
    }

    public function processMenuItem() {
        // Check if the user is logged in and is a developer
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'developer') {
            $this->redirect('/signin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'add') {
                // Add a new menu item
                $restaurantId = $_POST['restaurant_id'] ?? '';
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? '';
                $active = isset($_POST['active']) ? 1 : 0;
                
                $sql = "INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price, active) 
                        VALUES (?, ?, ?, ?, ?)";
                $this->db->query($sql, [$restaurantId, $name, $description, $price, $active]);
                
                $this->redirect('/manage_menu_items');
            } elseif ($action === 'edit') {
                // Edit an existing menu item
                $itemId = $_POST['item_id'] ?? '';
                $restaurantId = $_POST['restaurant_id'] ?? '';
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? '';
                $active = isset($_POST['active']) ? 1 : 0;
                
                $sql = "UPDATE fastrewards_menu_items 
                        SET restaurant_id = ?, name = ?, description = ?, price = ?, active = ? 
                        WHERE id = ?";
                $this->db->query($sql, [$restaurantId, $name, $description, $price, $active, $itemId]);
                
                $this->redirect('/manage_menu_items');
            } elseif ($action === 'delete') {
                // Delete a menu item
                $itemId = $_POST['item_id'] ?? '';
                
                $sql = "DELETE FROM fastrewards_menu_items WHERE id = ?";
                $this->db->query($sql, [$itemId]);
                
                $this->redirect('/manage_menu_items');
            }
        }
    }

    public function showOrder($restaurantId) {
        // ... existing code ...
    }
} 