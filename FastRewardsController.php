<?php
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
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
                    3 => 2500, // Starbucks
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
            header("Location: index.php?command=signin");
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        
        // Validate form data
        if(isset($_POST['from_restaurant']) && isset($_POST['to_restaurant']) && isset($_POST['points'])) {
            $fromRestaurantId = intval($_POST['from_restaurant']);
            $toRestaurantId = intval($_POST['to_restaurant']);
            $points = intval($_POST['points']);
            
            // Validate points
            if($points <= 0) {
                $_SESSION['message'] = "Please enter a positive number of points to transfer.";
                header("Location: index.php?command=transfer");
                exit();
            }
            
            // Check if restaurants are different
            if($fromRestaurantId === $toRestaurantId) {
                $_SESSION['message'] = "Cannot transfer points to the same restaurant.";
                header("Location: index.php?command=transfer");
                exit();
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
                    $_SESSION['message'] = "You don't have enough points to complete this transfer.";
                    $this->db->query("ROLLBACK");
                    header("Location: index.php?command=transfer");
                    exit();
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
                
                $_SESSION['message'] = "Successfully transferred $points points with a conversion rate of 1:$conversionRate. You received $receivedPoints points.";
                header("Location: index.php?command=transfer");
                exit();
                
            } catch (Exception $e) {
                $this->db->query("ROLLBACK");
                $_SESSION['message'] = "Error during transfer: " . $e->getMessage();
                header("Location: index.php?command=transfer");
                exit();
            }
        } else {
            $_SESSION['message'] = "Please fill out all required fields.";
            header("Location: index.php?command=transfer");
            exit();
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
        session_destroy();
        header("Location: index.php?command=signin");
        exit();
    }
}
?> 