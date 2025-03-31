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
        include 'views/rewards.php';
    }

    public function showTransfer() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        include 'views/transfer.php';
    }

    public function showTransactions() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=signin");
            exit();
        }
        include 'views/transactions.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?command=signin");
        exit();
    }
}
?> 