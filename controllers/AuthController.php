<?php
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

            if (empty($email) || empty($password)) {
                $errorMessage = 'Please fill in all fields';
            } else {
                // Get user from database
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    header('Location: /dashboard');
                    exit;
                } else {
                    $errorMessage = 'Invalid email or password';
                }
            }
        }

        echo $this->twig->render('login.twig', [
            'errorMessage' => $errorMessage
        ]);
    }
}