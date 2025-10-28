<?php 
// File: config/database.php

// require_once __DIR__ . '/../vendor/autoload.php';

// // Load .env file for local development
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
// $dotenv->safeLoad(); // Use safeLoad() to avoid throwing an error if .env doesn't exist on Railway

// // --- Connection Logic for Railway/Local ---

// // 1. Check for Railway-provided variables (Higher Priority for Production)
// // Railway injects these into the container environment.
// // if (isset($_ENV['MYSQLHOST'], $_ENV['MYSQLPORT'], $_ENV['MYSQLUSER'], $_ENV['MYSQLPASSWORD'], $_ENV['MYSQLDATABASE'])) {
// //     $db_host = $_ENV['MYSQLHOST'];
// //     $db_port = $_ENV['MYSQLPORT']; // Crucial addition: Railway uses ports
// //     $db_user = $_ENV['MYSQLUSER'];
// //     $db_pass = $_ENV['MYSQLPASSWORD'];
// //     $db_name = $_ENV['MYSQLDATABASE'];

// // } 
// // 2. Fallback to your application's DB_ variables (For Local Development)

//     // These variables come from your loaded .env file
//     $db_host = $_ENV['DB_HOST'] ?? 'mysql-19ab6ec1-task-twig.c.aivencloud.com';
//     $db_port = $_ENV['DB_PORT'] ?? '18954'; // Assuming you have a DB_PORT locally
//     $db_user = $_ENV['DB_USER'] ?? 'avnadmin';
//     $db_pass = $_ENV['DB_PASS'] ?? 'AVNS_KwMhUduix-lloXbsmR7';
//     $db_name = $_ENV['DB_NAME'] ?? 'twig_project';

  

// // Build the Data Source Name (DSN)
// // NOTE: We must include the port on Railway!
// $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

// try {
//     $pdo = new PDO($dsn, $db_user, $db_pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
// } catch(PDOException $e) {
//     // In production, show a generic error to the user and log the detailed error
//     error_log("Database connection failed: " . $e->getMessage()); 
//     die("A required service is unavailable.");
// }

// return $pdo;



// File: config/database.php

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env file for local development
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad(); // Use safeLoad() to avoid throwing an error if .env doesn't exist on Railway

// --- Connection Logic for Railway/Local ---

// 1. Check for Railway-provided variables (Higher Priority for Production)
// Railway injects these into the container environment.
// if (isset($_ENV['MYSQLHOST'], $_ENV['MYSQLPORT'], $_ENV['MYSQLUSER'], $_ENV['MYSQLPASSWORD'], $_ENV['MYSQLDATABASE'])) {
//     $db_host = $_ENV['MYSQLHOST'];
//     $db_port = $_ENV['MYSQLPORT']; // Crucial addition: Railway uses ports
//     $db_user = $_ENV['MYSQLUSER'];
//     $db_pass = $_ENV['MYSQLPASSWORD'];
//     $db_name = $_ENV['MYSQLDATABASE'];

// } 
// 2. Fallback to your application's DB_ variables (For Local Development)

//     // These variables come from your loaded .env file
//     $db_host = $_ENV['DB_HOST'] ?? null;
//     $db_port = $_ENV['DB_PORT'] ?? null; // Assuming you have a DB_PORT locally
//     $db_user = $_ENV['DB_USER'] ?? null;
//     $db_pass = $_ENV['DB_PASS'] ?? null;
//     $db_name = $_ENV['DB_NAME'] ?? null;

  

// // Build the Data Source Name (DSN)
// // NOTE: We must include the port on Railway!
// $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

// try {
//     $pdo = new PDO($dsn, $db_user, $db_pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
//     // Auto-create tables if they don't exist
//     createTablesIfNotExist($pdo);
    
// } catch(PDOException $e) {
//     // In production, show a generic error to the user and log the detailed error
//     error_log("Database connection failed: " . $e->getMessage()); 
//     die("A required service is unavailable.");
// }

// /**
//  * Create necessary database tables if they don't exist
//  */
// function createTablesIfNotExist($pdo) {
//     try {
//         // Create users table
//         $pdo->exec("
//             CREATE TABLE IF NOT EXISTS users (
//                 id INT AUTO_INCREMENT PRIMARY KEY,
//                 name VARCHAR(255) NOT NULL,
//                 email VARCHAR(255) NOT NULL UNIQUE,
//                 password VARCHAR(255) NOT NULL,
//                 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//                 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
//         ");

//         // Create tickets table
//         $pdo->exec("
//             CREATE TABLE IF NOT EXISTS tickets (
//                 id INT AUTO_INCREMENT PRIMARY KEY,
//                 user_id INT NOT NULL,
//                 title VARCHAR(255) NOT NULL,
//                 description TEXT,
//                 status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
//                 priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
//                 company VARCHAR(255),
//                 employee VARCHAR(255),
//                 start_date DATE,
//                 end_date DATE,
//                 days_left INT DEFAULT 0,
//                 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//                 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//                 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
//                 INDEX idx_user_id (user_id),
//                 INDEX idx_status (status),
//                 INDEX idx_created_at (created_at)
//             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
//         ");

//         // Check if default user exists, if not create one
//         $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
//         $stmt->execute(['test@example.com']);
        
//         if ($stmt->fetchColumn() == 0) {
//             // Create default test user (password: password123)
//             $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
//             $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
//             $stmt->execute(['Test User', 'test@example.com', $hashedPassword]);
//         }

//     } catch (PDOException $e) {
//         error_log("Table creation failed: " . $e->getMessage());
//         // Don't die here - let the application continue, tables might already exist
//     }
// }

// return $pdo;


<?php
// File: config/database.php

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env file for local development
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad(); // Use safeLoad() to avoid throwing an error if .env doesn't exist on Railway

// --- Connection Logic for Railway/Local ---

// 1. Check for Railway-provided variables (Higher Priority for Production)
if (isset($_ENV['MYSQLHOST'], $_ENV['MYSQLPORT'], $_ENV['MYSQLUSER'], $_ENV['MYSQLPASSWORD'], $_ENV['MYSQLDATABASE'])) {
    $db_host = $_ENV['MYSQLHOST'];
    $db_port = $_ENV['MYSQLPORT'];
    $db_user = $_ENV['MYSQLUSER'];
    $db_pass = $_ENV['MYSQLPASSWORD'];
    $db_name = $_ENV['MYSQLDATABASE'];
} 
// 2. Fallback to your application's DB_ variables (For Local Development)
else {
    // These variables come from your loaded .env file
    $db_host = $_ENV['DB_HOST'] ?? null;
    $db_port = $_ENV['DB_PORT'] ?? null;
    $db_user = $_ENV['DB_USER'] ?? null;
    $db_pass = $_ENV['DB_PASS'] ?? null;
    $db_name = $_ENV['DB_NAME'] ?? null;
    
    // Validate required environment variables
    if (!$db_host || !$db_user || !$db_name) {
        die("Database configuration missing. Please check your .env file.");
    }
}

// Build the Data Source Name (DSN)
$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Auto-create tables if they don't exist
    createTablesIfNotExist($pdo);
    
} catch(PDOException $e) {
    // Log detailed error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Connection details: host={$db_host}, port={$db_port}, user={$db_user}, database={$db_name}");
    
    // Show more helpful error in development
    if (getenv('APP_ENV') !== 'production') {
        die("Database connection failed: " . $e->getMessage() . 
            "<br>Host: {$db_host}:{$db_port}<br>Database: {$db_name}<br>User: {$db_user}");
    }
    
    die("A required service is unavailable.");
}

/**
 * Create necessary database tables if they don't exist
 */
function createTablesIfNotExist($pdo) {
    try {
        // Create users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create tickets table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS tickets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
                priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
                company VARCHAR(255),
                employee VARCHAR(255),
                start_date DATE,
                end_date DATE,
                days_left INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create default user from environment variables if provided
        $defaultEmail = $_ENV['DEFAULT_USER_EMAIL'] ?? 'test@example.com';
        $defaultPassword = $_ENV['DEFAULT_USER_PASSWORD'] ?? 'password123';
        $defaultName = $_ENV['DEFAULT_USER_NAME'] ?? 'Test User';
        
        // Check if default user exists, if not create one
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$defaultEmail]);
        
        if ($stmt->fetchColumn() == 0) {
            // Create default user with credentials from .env or defaults
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$defaultName, $defaultEmail, $hashedPassword]);
        }

    } catch (PDOException $e) {
        error_log("Table creation failed: " . $e->getMessage());
        // Don't die here - let the application continue, tables might already exist
    }
}

return $pdo;