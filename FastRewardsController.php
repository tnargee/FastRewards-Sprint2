<?php
// Author: Tenzin Nargee, Kyle Vitayanuvatti

require_once 'Database.php';
require_once 'Config.php';

class FastRewardsController {
    private $db;

    public function __construct() {
        try {
            $this->db = new Database();
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function showSignIn() {
        // Store message if it exists
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
        
        // Only destroy session if we're not coming from a failed login
        if (!isset($_SESSION['login_attempt'])) {
            session_destroy();
            session_start();
        }
        
        // Restore message if it existed
        if ($message) {
            $_SESSION['message'] = $message;
        }
        
        include 'views/signin.php';
    }

    public function processSignIn() {
        if(!isset($_SESSION)) {
            session_start();
        }

        if(isset($_POST['email']) && isset($_POST['password'])) {
            $email = strtolower(trim($_POST['email'])); // Convert email to lowercase
            $password = $_POST['password'];

            try {
                // Check if user exists
                $user = $this->db->fetch(
                    "SELECT * FROM fastrewards_users WHERE email = $1",
                    [$email]
                );

                if($user) {
                    // Verify password
                    if(!password_verify($password, $user['password_hash'])) {
                        $_SESSION['message'] = "Incorrect password.";
                        $_SESSION['login_attempt'] = true;
                        header("Location: index.php?command=signin");
                        exit();
                    }
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Initialize user point balances if they don't exist
                    $this->initUserPointBalances($user['id']);
                    
                    header("Location: index.php?command=home");
                    exit();
                } else {
                    $_SESSION['message'] = "No account found with this email.";
                    $_SESSION['login_attempt'] = true;
                    header("Location: index.php?command=signin");
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Database error: " . $e->getMessage();
                header("Location: index.php?command=signin");
                exit();
            }
        } else {
            $_SESSION['message'] = "Please enter all required fields.";
            header("Location: index.php?command=signin");
            exit();
        }
    }

    public function showSignUp() {
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
        include 'views/signup.php';
    }

    public function processSignUp() {
        if(!isset($_SESSION)) {
            session_start();
        }

        if(isset($_POST['firstName']) && isset($_POST['lastName']) && 
           isset($_POST['email']) && isset($_POST['password'])) {
            
            $firstName = trim($_POST['firstName']);
            $lastName = trim($_POST['lastName']);
            $email = strtolower(trim($_POST['email'])); // Convert email to lowercase
            $password = $_POST['password'];

            // Validate input
            $errors = [];
            
            if(empty($firstName)) {
                $errors[] = "First name is required.";
            }
            
            if(empty($lastName)) {
                $errors[] = "Last name is required.";
            }
            
            if(empty($email)) {
                $errors[] = "Email is required.";
            } elseif(!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = "Please enter a valid email address.";
            }
            
            if(empty($password)) {
                $errors[] = "Password is required.";
            } elseif(strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long.";
            }

            if(!empty($errors)) {
                $_SESSION['message'] = implode("<br>", $errors);
                header("Location: index.php?command=signup");
                exit();
            }

            try {
                // Check if email already exists
                $existingUser = $this->db->fetch(
                    "SELECT id FROM fastrewards_users WHERE email = $1",
                    [$email]
                );

                if($existingUser) {
                    $_SESSION['message'] = "An account with this email already exists.";
                    header("Location: index.php?command=signup");
                    exit();
                }

                // Create new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $this->db->query(
                    "INSERT INTO fastrewards_users (first_name, last_name, email, password_hash) 
                     VALUES ($1, $2, $3, $4)",
                    [$firstName, $lastName, $email, $password_hash]
                );
                
                // Get the user ID of the newly created user
                $newUser = $this->db->fetch(
                    "SELECT id FROM fastrewards_users WHERE email = $1",
                    [$email]
                );
                
                // Initialize point balances for the new user
                $this->initUserPointBalances($newUser['id']);

                $_SESSION['message'] = "Account created successfully! Please sign in.";
                header("Location: index.php?command=signin");
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Database error: " . $e->getMessage();
                header("Location: index.php?command=signup");
                exit();
            }
        } else {
            $_SESSION['message'] = "Please enter all required fields.";
            header("Location: index.php?command=signup");
            exit();
        }
    }
    
    // Initialize default point balances for a new user
    private function initUserPointBalances($userId) {
        try {
            // Check if user already has point balances
            $existingBalances = $this->db->query(
                "SELECT * FROM fastrewards_point_balances WHERE user_id = $1",
                [$userId]
            );
            
            if (empty($existingBalances)) {
                // Get all restaurants
                $restaurants = $this->db->query("SELECT id FROM fastrewards_restaurants");
                
                // Set default point balances for each restaurant
                $defaultPoints = [
                    1 => 4500, // McDonald's
                    2 => 300,  // Chipotle
                    3 => 2500,  // Starbucks
                    4 => 500   // Wawa
                ];
                
                foreach ($restaurants as $restaurant) {
                    $restaurantId = $restaurant['id'];
                    $points = $defaultPoints[$restaurantId] ?? 0;
                    
                    $this->db->query(
                        "INSERT INTO fastrewards_point_balances (user_id, restaurant_id, points) 
                         VALUES ($1, $2, $3)",
                        [$userId, $restaurantId, $points]
                    );
                }
            }
        } catch (Exception $e) {
            // Log error but continue
            error_log("Error initializing point balances: " . $e->getMessage());
        }
    }

    public function showHome() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        include 'views/home.php';
    }

    public function showScan() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        include 'views/scan.php';
    }

    public function showRewards() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        // Get user's point balances
        $userId = $_SESSION['user_id'];
        $pointBalances = $this->getUserPointBalances($userId);
        
        include 'views/rewards.php';
    }

    public function showTransfer() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        // Get user's point balances
        $userId = $_SESSION['user_id'];
        $pointBalances = $this->getUserPointBalances($userId);
        
        // Get all restaurants
        $restaurants = $this->db->query("SELECT id, name, logo_path FROM fastrewards_restaurants ORDER BY name");
        
        // Get recent transfers
        $recentTransfers = $this->getUserTransactions($userId, 5);
        
        // Pass variables to view
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
        unset($_SESSION['message']);
        
        include 'views/transfer.php';
    }

    public function showTransactions() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        // Get user's transactions
        $userId = $_SESSION['user_id'];
        $transactions = $this->getUserTransactions($userId);
        
        include 'views/transactions.php';
    }
    
    public function getUserPointBalances($userId) {
        // Get point balances with restaurant details
        $pointBalances = $this->db->query(
            "SELECT pb.*, r.name AS restaurant_name, r.logo_path 
             FROM fastrewards_point_balances pb
             JOIN fastrewards_restaurants r ON pb.restaurant_id = r.id
             WHERE pb.user_id = $1",
            [$userId]
        );
        
        // Index by restaurant_id for easier access
        $balancesByRestaurant = [];
        foreach ($pointBalances as $balance) {
            $balancesByRestaurant[$balance['restaurant_id']] = $balance;
        }
        
        return $balancesByRestaurant;
    }
    
    public function getUserTransactions($userId, $limit = null) {
        $query = "SELECT t.*, 
                 r1.name AS from_restaurant_name, r1.logo_path AS from_logo_path,
                 r2.name AS to_restaurant_name, r2.logo_path AS to_logo_path
                 FROM fastrewards_transactions t
                 LEFT JOIN fastrewards_restaurants r1 ON t.from_restaurant_id = r1.id
                 LEFT JOIN fastrewards_restaurants r2 ON t.to_restaurant_id = r2.id
                 WHERE t.user_id = $1
                 ORDER BY t.created_at DESC";
        
        // Add limit if specified
        if ($limit) {
            $query .= " LIMIT $limit";
        }
        
        return $this->db->query($query, [$userId]);
    }
    
    public function processTransfer() {
        if(!isset($_SESSION['user_id'])) {
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Session expired. Please sign in again.']);
                exit();
            } else {
                header("Location: index.php?command=signin");
                exit();
            }
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get state information from hidden fields
        $previousFromRestaurant = isset($_POST['previous_from_restaurant']) ? $_POST['previous_from_restaurant'] : null;
        $previousToRestaurant = isset($_POST['previous_to_restaurant']) ? $_POST['previous_to_restaurant'] : null;
        $previousAmount = isset($_POST['previous_amount']) ? $_POST['previous_amount'] : null;
        $transferCount = isset($_POST['transfer_count']) ? intval($_POST['transfer_count']) : 1;
        
        // Validate form data
        if(isset($_POST['from_restaurant']) && isset($_POST['to_restaurant']) && isset($_POST['points'])) {
            $fromRestaurantId = intval($_POST['from_restaurant']);
            $toRestaurantId = intval($_POST['to_restaurant']);
            $points = intval($_POST['points']);
            
            // Validate points
            if($points <= 0) {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Please enter a positive number of points to transfer.']);
                    exit();
                } else {
                    $_SESSION['message'] = "Please enter a positive number of points to transfer.";
                    header("Location: index.php?command=transfer");
                    exit();
                }
            }
            
            // Check if restaurants are different
            if($fromRestaurantId === $toRestaurantId) {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Cannot transfer points to the same restaurant.']);
                    exit();
                } else {
                    $_SESSION['message'] = "Cannot transfer points to the same restaurant.";
                    header("Location: index.php?command=transfer");
                    exit();
                }
            }
            
            try {
                // Begin transaction
                $this->db->query("BEGIN");
                
                // Check if user has enough points
                $fromBalance = $this->db->fetch(
                    "SELECT points FROM fastrewards_point_balances 
                     WHERE user_id = $1 AND restaurant_id = $2 FOR UPDATE",
                    [$userId, $fromRestaurantId]
                );
                
                if(!$fromBalance || $fromBalance['points'] < $points) {
                    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Insufficient points balance.']);
                        exit();
                    } else {
                        $_SESSION['message'] = "Insufficient points balance.";
                        header("Location: index.php?command=transfer");
                        exit();
                    }
                }
                
                // Calculate conversion rate (example: 1:0.5)
                $conversionRate = 0.5;
                $receivedPoints = (int)($points * $conversionRate);
                
                // Deduct points from source restaurant
                $this->db->query(
                    "UPDATE fastrewards_point_balances 
                     SET points = points - $1, updated_at = CURRENT_TIMESTAMP
                     WHERE user_id = $2 AND restaurant_id = $3",
                    [$points, $userId, $fromRestaurantId]
                );
                
                // Add points to destination restaurant
                $this->db->query(
                    "UPDATE fastrewards_point_balances 
                     SET points = points + $1, updated_at = CURRENT_TIMESTAMP
                     WHERE user_id = $2 AND restaurant_id = $3",
                    [$receivedPoints, $userId, $toRestaurantId]
                );
                
                // Record transaction
                $this->db->query(
                    "INSERT INTO fastrewards_transactions 
                     (user_id, from_restaurant_id, to_restaurant_id, points_transferred, points_received)
                     VALUES ($1, $2, $3, $4, $5)",
                    [$userId, $fromRestaurantId, $toRestaurantId, $points, $receivedPoints]
                );
                
                // Commit transaction
                $this->db->query("COMMIT");
                
                // Get restaurant names for current transfer
                $currentFromRestaurant = $this->db->fetch(
                    "SELECT name FROM fastrewards_restaurants WHERE id = $1",
                    [$fromRestaurantId]
                );
                $currentToRestaurant = $this->db->fetch(
                    "SELECT name FROM fastrewards_restaurants WHERE id = $1",
                    [$toRestaurantId]
                );
                
                // Build success message
                $message = "Successfully transferred $points points from " . 
                          htmlspecialchars($currentFromRestaurant['name']) . " to " . 
                          htmlspecialchars($currentToRestaurant['name']) . 
                          " with a conversion rate of 1:$conversionRate. You received $receivedPoints points.";
                
                if ($transferCount > 1) {
                    $fromRestaurant = $this->db->fetch(
                        "SELECT name FROM fastrewards_restaurants WHERE id = $1",
                        [$previousFromRestaurant]
                    );
                    $toRestaurant = $this->db->fetch(
                        "SELECT name FROM fastrewards_restaurants WHERE id = $1",
                        [$previousToRestaurant]
                    );
                    
                    $message .= "<br><small class='text-muted'>Previous transfer: $previousAmount points from " . 
                               htmlspecialchars($fromRestaurant['name']) . " to " . 
                               htmlspecialchars($toRestaurant['name']) . "</small>";
                }
                
                // Get updated balances
                $balances = $this->getUserPointBalances($userId);
                
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'balances' => $balances
                    ]);
                    exit();
                } else {
                    $_SESSION['message'] = $message;
                    header("Location: index.php?command=transfer");
                    exit();
                }
                
            } catch (Exception $e) {
                $this->db->query("ROLLBACK");
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Error during transfer: ' . $e->getMessage()]);
                    exit();
                } else {
                    $_SESSION['message'] = "Error during transfer: " . $e->getMessage();
                    header("Location: index.php?command=transfer");
                    exit();
                }
            }
        } else {
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please fill out all required fields.']);
                exit();
            } else {
                $_SESSION['message'] = "Please fill out all required fields.";
                header("Location: index.php?command=transfer");
                exit();
            }
        }
    }
    
    // JSON response endpoint for transactions
    public function getTransactionsJson() {
        if(!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        
        // Validate query parameters
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        
        try {
            // Get user's transactions
            $transactions = $this->getUserTransactions($userId, $limit);
            
            // Set response headers
            header('Content-Type: application/json');
            
            // Return response as JSON
            echo json_encode([
                'status' => 'success',
                'transactions' => $transactions
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
        
        exit();
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?command=signin");
        exit();
    }

    // Check if current user is a developer
    public function isDeveloper() {
        if(!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['role']) && $_SESSION['role'] === 'developer';
    }
    
    // Show the deals management page for developers
    public function showManageDeals() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!$this->isDeveloper()) {
            $_SESSION['message'] = "You don't have permission to access this page.";
            header("Location: index.php?command=home");
            exit();
        }
        
        // Get all restaurants for the dropdown
        $restaurants = $this->db->query("SELECT * FROM fastrewards_restaurants ORDER BY name");
        
        // Get all deals
        $deals = $this->db->query("
            SELECT d.*, r.name as restaurant_name, r.logo_path
            FROM fastrewards_deals d
            JOIN fastrewards_restaurants r ON d.restaurant_id = r.id
            ORDER BY d.created_at DESC
        ");
        
        include 'views/manage_deals.php';
    }
    
    // Process adding or editing a deal
    public function processDeal() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!$this->isDeveloper()) {
            $_SESSION['message'] = "You don't have permission to perform this action.";
            header("Location: index.php?command=home");
            exit();
        }
        
        if(isset($_POST['deal_action'])) {
            $action = $_POST['deal_action'];
            
            if($action === 'add' && 
               isset($_POST['restaurant_id']) && 
               isset($_POST['title']) && 
               isset($_POST['points_required']) && 
               isset($_POST['image_path'])) {
                
                $restaurantId = $_POST['restaurant_id'];
                $title = trim($_POST['title']);
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $pointsRequired = intval($_POST['points_required']);
                $imagePath = trim($_POST['image_path']);
                
                if(empty($title) || $pointsRequired <= 0 || empty($imagePath)) {
                    $_SESSION['message'] = "Please fill all required fields properly.";
                    header("Location: index.php?command=manage_deals");
                    exit();
                }
                
                try {
                    $this->db->query(
                        "INSERT INTO fastrewards_deals (restaurant_id, title, description, points_required, image_path) 
                         VALUES ($1, $2, $3, $4, $5)",
                        [$restaurantId, $title, $description, $pointsRequired, $imagePath]
                    );
                    
                    $_SESSION['message'] = "Deal created successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error creating deal: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_deals");
                exit();
            }
            else if($action === 'edit' && 
                    isset($_POST['deal_id']) && 
                    isset($_POST['restaurant_id']) && 
                    isset($_POST['title']) && 
                    isset($_POST['points_required']) && 
                    isset($_POST['image_path'])) {
                
                $dealId = $_POST['deal_id'];
                $restaurantId = $_POST['restaurant_id'];
                $title = trim($_POST['title']);
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $pointsRequired = intval($_POST['points_required']);
                $imagePath = trim($_POST['image_path']);
                $active = isset($_POST['active']) ? 1 : 0;
                
                if(empty($title) || $pointsRequired <= 0 || empty($imagePath)) {
                    $_SESSION['message'] = "Please fill all required fields properly.";
                    header("Location: index.php?command=manage_deals");
                    exit();
                }
                
                try {
                    $this->db->query(
                        "UPDATE fastrewards_deals 
                         SET restaurant_id = $1, title = $2, description = $3, 
                         points_required = $4, image_path = $5, active = $6, updated_at = CURRENT_TIMESTAMP
                         WHERE id = $7",
                        [$restaurantId, $title, $description, $pointsRequired, $imagePath, $active, $dealId]
                    );
                    
                    $_SESSION['message'] = "Deal updated successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error updating deal: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_deals");
                exit();
            }
            else if($action === 'delete' && isset($_POST['deal_id'])) {
                $dealId = $_POST['deal_id'];
                
                try {
                    $this->db->query(
                        "DELETE FROM fastrewards_deals WHERE id = $1",
                        [$dealId]
                    );
                    
                    $_SESSION['message'] = "Deal deleted successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error deleting deal: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_deals");
                exit();
            }
        }
        
        $_SESSION['message'] = "Invalid request.";
        header("Location: index.php?command=manage_deals");
        exit();
    }
    
    // Get deals for the rewards page
    public function getDeals() {
        try {
            $deals = $this->db->query("
                SELECT d.*, r.name as restaurant_name, r.logo_path
                FROM fastrewards_deals d
                JOIN fastrewards_restaurants r ON d.restaurant_id = r.id
                WHERE d.active = true
                ORDER BY d.created_at DESC
            ");
            
            return $deals;
        } catch (Exception $e) {
            // Log error
            error_log("Error fetching deals: " . $e->getMessage());
            return [];
        }
    }

    // Show the menu items management page for developers
    public function showManageMenuItems() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!$this->isDeveloper()) {
            $_SESSION['message'] = "You don't have permission to access this page.";
            header("Location: index.php?command=home");
            exit();
        }
        
        // Get all restaurants for the dropdown
        $restaurants = $this->db->query("SELECT * FROM fastrewards_restaurants ORDER BY name");
        
        // Get all menu items
        $menuItems = $this->db->query("
            SELECT mi.*, r.name as restaurant_name
            FROM fastrewards_menu_items mi
            JOIN fastrewards_restaurants r ON mi.restaurant_id = r.id
            ORDER BY r.name, mi.name
        ");
        
        include 'views/manage_menu_items.php';
    }
    
    // Process adding, editing, or deleting a menu item
    public function processMenuItem() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!$this->isDeveloper()) {
            $_SESSION['message'] = "You don't have permission to perform this action.";
            header("Location: index.php?command=home");
            exit();
        }
        
        if(isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if($action === 'add' && 
               isset($_POST['restaurant_id']) && 
               isset($_POST['name']) && 
               isset($_POST['price'])) {
                
                $restaurantId = $_POST['restaurant_id'];
                $name = trim($_POST['name']);
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $price = floatval($_POST['price']);
                $active = isset($_POST['active']) ? 1 : 1; // Default to active
                
                if(empty($name) || $price <= 0) {
                    $_SESSION['message'] = "Please fill all required fields properly.";
                    header("Location: index.php?command=manage_menu_items");
                    exit();
                }
                
                try {
                    $this->db->query(
                        "INSERT INTO fastrewards_menu_items (restaurant_id, name, description, price, active) 
                         VALUES ($1, $2, $3, $4, $5)",
                        [$restaurantId, $name, $description, $price, $active]
                    );
                    
                    $_SESSION['message'] = "Menu item created successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error creating menu item: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_menu_items");
                exit();
            }
            else if($action === 'edit' && 
                    isset($_POST['item_id']) && 
                    isset($_POST['restaurant_id']) && 
                    isset($_POST['name']) && 
                    isset($_POST['price'])) {
                
                $itemId = $_POST['item_id'];
                $restaurantId = $_POST['restaurant_id'];
                $name = trim($_POST['name']);
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $price = floatval($_POST['price']);
                $active = isset($_POST['active']) ? 1 : 0;
                
                if(empty($name) || $price <= 0) {
                    $_SESSION['message'] = "Please fill all required fields properly.";
                    header("Location: index.php?command=manage_menu_items");
                    exit();
                }
                
                try {
                    $this->db->query(
                        "UPDATE fastrewards_menu_items 
                         SET restaurant_id = $1, name = $2, description = $3, 
                         price = $4, active = $5, updated_at = CURRENT_TIMESTAMP
                         WHERE id = $6",
                        [$restaurantId, $name, $description, $price, $active, $itemId]
                    );
                    
                    $_SESSION['message'] = "Menu item updated successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error updating menu item: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_menu_items");
                exit();
            }
            else if($action === 'delete' && isset($_POST['item_id'])) {
                $itemId = $_POST['item_id'];
                
                try {
                    $this->db->query(
                        "DELETE FROM fastrewards_menu_items WHERE id = $1",
                        [$itemId]
                    );
                    
                    $_SESSION['message'] = "Menu item deleted successfully!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Error deleting menu item: " . $e->getMessage();
                }
                
                header("Location: index.php?command=manage_menu_items");
                exit();
            }
        }
        
        $_SESSION['message'] = "Invalid request.";
        header("Location: index.php?command=manage_menu_items");
        exit();
    }
    
    // JSON response endpoint for point balances
    public function getPointBalancesJson() {
        if(!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $pointBalances = $this->getUserPointBalances($userId);
        
        // Format the data for the frontend
        $formattedBalances = [];
        foreach ($pointBalances as $balance) {
            $formattedBalances[] = [
                'restaurant_id' => $balance['restaurant_id'],
                'restaurant_name' => $balance['restaurant_name'],
                'points' => $balance['points'],
                'logo' => strtolower(str_replace(' ', '', $balance['restaurant_name'])) . '.png'
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($formattedBalances);
        exit();
    }
    
    // Show order page with restaurant menu items
    public function showOrder() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        // Get restaurant ID from the URL
        $restaurantId = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : null;
        
        if (!$restaurantId) {
            // If no restaurant selected, show list of restaurants
            $restaurants = $this->db->query("SELECT * FROM fastrewards_restaurants ORDER BY name");
            include 'views/order_restaurants.php';
            return;
        }
        
        // Get restaurant details
        $restaurant = $this->db->fetch(
            "SELECT * FROM fastrewards_restaurants WHERE id = $1",
            [$restaurantId]
        );
        
        if (!$restaurant) {
            $_SESSION['message'] = "Restaurant not found.";
            header("Location: index.php?command=home");
            exit();
        }
        
        // Get available deals for this restaurant
        $deals = $this->db->query(
            "SELECT * FROM fastrewards_deals WHERE restaurant_id = $1 AND active = true",
            [$restaurantId]
        );
        
        // Get user's point balance for this restaurant
        $userId = $_SESSION['user_id'];
        $pointBalances = $this->getUserPointBalances($userId);
        $currentPoints = isset($pointBalances[$restaurantId]) ? $pointBalances[$restaurantId]['points'] : 0;
        
        include 'views/order.php';
    }
    
    // Process the order submission
    public function processOrder() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!isset($_POST['restaurant_id']) || !isset($_POST['items']) || empty($_POST['items'])) {
            $_SESSION['message'] = "Invalid order data.";
            header("Location: index.php?command=home");
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $restaurantId = intval($_POST['restaurant_id']);
        $items = json_decode($_POST['items'], true);
        
        if (!is_array($items) || empty($items)) {
            $_SESSION['message'] = "No items in order.";
            header("Location: index.php?command=order&restaurant_id=" . $restaurantId);
            exit();
        }
        
        try {
            // Begin transaction
            $this->db->query("BEGIN");
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['price'] * $item['quantity']);
            }
            
            // Create order
            $this->db->query(
                "INSERT INTO fastrewards_orders (user_id, restaurant_id, total_amount) 
                VALUES ($1, $2, $3) RETURNING id",
                [$userId, $restaurantId, $totalAmount]
            );
            
            // Get the newly created order ID
            $orderId = $this->db->fetch("SELECT lastval() as id")['id'];
            
            // Insert order items
            foreach ($items as $item) {
                $dealId = isset($item['deal_id']) ? $item['deal_id'] : null;
                $this->db->query(
                    "INSERT INTO fastrewards_order_items (order_id, deal_id, item_name, quantity, price) 
                    VALUES ($1, $2, $3, $4, $5)",
                    [$orderId, $dealId, $item['name'], $item['quantity'], $item['price']]
                );
            }
            
            // Calculate points earned (10 points per dollar)
            $pointsEarned = round($totalAmount * 10);
            
            // Update order with points earned
            $this->db->query(
                "UPDATE fastrewards_orders SET points_earned = $1, status = 'completed' WHERE id = $2",
                [$pointsEarned, $orderId]
            );
            
            // Add points to user's balance
            $this->db->query(
                "UPDATE fastrewards_point_balances 
                SET points = points + $1, updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = $2 AND restaurant_id = $3",
                [$pointsEarned, $userId, $restaurantId]
            );
            
            // Commit transaction
            $this->db->query("COMMIT");
            
            $_SESSION['message'] = "Order placed successfully! You earned $pointsEarned points.";
            header("Location: index.php?command=order_confirmation&order_id=" . $orderId);
            exit();
            
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            $_SESSION['message'] = "Error processing order: " . $e->getMessage();
            header("Location: index.php?command=order&restaurant_id=" . $restaurantId);
            exit();
        }
    }
    
    // Show order confirmation page
    public function showOrderConfirmation() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        if(!isset($_GET['order_id'])) {
            header("Location: index.php?command=home");
            exit();
        }
        
        $orderId = intval($_GET['order_id']);
        $userId = $_SESSION['user_id'];
        
        // Get order details
        $order = $this->db->fetch(
            "SELECT o.*, r.name as restaurant_name, r.logo_path 
            FROM fastrewards_orders o
            JOIN fastrewards_restaurants r ON o.restaurant_id = r.id
            WHERE o.id = $1 AND o.user_id = $2",
            [$orderId, $userId]
        );
        
        if (!$order) {
            $_SESSION['message'] = "Order not found.";
            header("Location: index.php?command=home");
            exit();
        }
        
        // Get order items
        $items = $this->db->query(
            "SELECT oi.*, d.title as deal_title 
            FROM fastrewards_order_items oi
            LEFT JOIN fastrewards_deals d ON oi.deal_id = d.id
            WHERE oi.order_id = $1",
            [$orderId]
        );
        
        include 'views/order_confirmation.php';
    }
    
    // Show orders history page
    public function showOrdersHistory() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get all user orders
        $orders = $this->db->query(
            "SELECT o.*, r.name as restaurant_name, r.logo_path 
            FROM fastrewards_orders o
            JOIN fastrewards_restaurants r ON o.restaurant_id = r.id
            WHERE o.user_id = $1
            ORDER BY o.created_at DESC",
            [$userId]
        );
        
        include 'views/orders_history.php';
    }
    
    // Process file upload from scan page
    public function processFileUpload() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        
        // Check if a file was uploaded
        if(!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['message'] = "Error uploading file. Please try again.";
            header("Location: index.php?command=scan");
            exit();
        }
        
        // Validate restaurant selection
        if(!isset($_POST['restaurant_id']) || empty($_POST['restaurant_id'])) {
            $_SESSION['message'] = "Please select a restaurant.";
            header("Location: index.php?command=scan");
            exit();
        }
        
        $restaurantId = intval($_POST['restaurant_id']);
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $fileType = $_FILES['receipt']['type'];
        
        if(!in_array($fileType, $allowedTypes)) {
            $_SESSION['message'] = "Invalid file type. Please upload a JPG, PNG, or PDF file.";
            header("Location: index.php?command=scan");
            exit();
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = 'uploads/receipts/';
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . $_FILES['receipt']['name'];
        $filePath = $uploadDir . $filename;
        
        // Move file to uploads directory
        if(!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
            $_SESSION['message'] = "Error saving file. Please try again.";
            header("Location: index.php?command=scan");
            exit();
        }
        
        try {
            // Save receipt in database
            $this->db->query(
                "INSERT INTO fastrewards_receipts (user_id, restaurant_id, file_path) 
                VALUES ($1, $2, $3) RETURNING id",
                [$userId, $restaurantId, $filePath]
            );
            
            $receiptId = $this->db->fetch("SELECT lastval() as id")['id'];
            
            // For this demo, automatically approve the receipt and add points
            $pointsEarned = rand(50, 200); // Random points between 50 and 200
            
            $this->db->query(
                "UPDATE fastrewards_receipts 
                SET status = 'approved', points_earned = $1, updated_at = CURRENT_TIMESTAMP 
                WHERE id = $2",
                [$pointsEarned, $receiptId]
            );
            
            // Add points to user's balance
            $this->db->query(
                "UPDATE fastrewards_point_balances 
                SET points = points + $1, updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = $2 AND restaurant_id = $3",
                [$pointsEarned, $userId, $restaurantId]
            );
            
            $_SESSION['message'] = "Receipt uploaded successfully! You earned $pointsEarned points.";
            header("Location: index.php?command=rewards");
            exit();
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Error processing receipt: " . $e->getMessage();
            header("Location: index.php?command=scan");
            exit();
        }
    }
}
?> 