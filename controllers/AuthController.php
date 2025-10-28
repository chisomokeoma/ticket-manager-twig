<?php
// class AuthController {
//     private $pdo;
//     private $twig;

//     public function __construct($pdo) {
//         $this->pdo = $pdo;
        
//         $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
//         $this->twig = new \Twig\Environment($loader, [
//             'cache' => false,
//             'debug' => true
//         ]);
//     }



//     public function login() {
        
//         // If already logged in, redirect to dashboard
//         if (isset($_SESSION['user_id'])) {
//             header('Location: /dashboard');
//             exit;
//         }

//         $errorMessage = null;

//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             $email = trim($_POST['email'] ?? '');
//             $password = $_POST['password'] ?? '';

//             if (empty($email) || empty($password)) {
//                 $errorMessage = 'Please fill in all fields';
//             } else {
//                 // Get user from database
//                 $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
//                 $stmt->execute([$email]);
//                 $user = $stmt->fetch();

//                 if ($user && password_verify($password, $user['password'])) {
//                     // Login successful
//                     $_SESSION['user_id'] = $user['id'];
//                     $_SESSION['user_email'] = $user['email'];
//                     $_SESSION['user_name'] = $user['name'];
                    
//                     header('Location: /dashboard');
//                     exit;
//                 } else {
//                     $errorMessage = 'Invalid email or password';
//                 }
//             }
//         }

//         echo $this->twig->render('login.twig', [
//             'errorMessage' => $errorMessage
//         ]);
//     }
// }



class AuthController {
    private $pdo;
    private $twig;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);
    }

    public function login() {
        
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $name = trim($_POST['name'] ?? '');

            if (empty($email) || empty($password)) {
                $errorMessage = 'Please fill in all fields';
            } else {
                // Get user from database
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // User exists - verify password
                    if (password_verify($password, $user['password'])) {
                        // Login successful
                        $this->loginUser($user);
                        header('Location: /dashboard');
                        exit;
                    } else {
                        $errorMessage = 'Invalid email or password';
                    }
                } else {
                    // User doesn't exist - create new user and log them in
                    $user = $this->createUser($email, $password, $name);
                    
                    if ($user) {
                        $this->loginUser($user);
                        header('Location: /dashboard');
                        exit;
                    } else {
                        $errorMessage = 'Failed to create account. Please try again.';
                    }
                }
            }
        }

        echo $this->twig->render('login.twig', [
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * Create a new user account
     */
    private function createUser($email, $password, $name = null) {
        try {
            // If no name provided, generate from email
            if (empty($name)) {
                $name = explode('@', $email)[0];
                $name = ucfirst($name);
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, email, password) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $email, $hashedPassword]);

            // Get the newly created user
            $userId = $this->pdo->lastInsertId();
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log in a user by setting session variables
     */
    private function loginUser($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
    }

    /**
     * Logout function
     */
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}