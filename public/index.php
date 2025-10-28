<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
$pdo = require_once __DIR__ . '/../config/database.php';

// Simple routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
    case '/dashboard':
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $controller = new DashboardController($pdo);
        $controller->index();
        break;

    case '/tickets':
        require_once __DIR__ . '/../controllers/TicketsController.php';
        $controller = new TicketsController($pdo);
        $controller->index();
        break;

    case '/tickets/create':
        require_once __DIR__ . '/../controllers/TicketsController.php';
        $controller = new TicketsController($pdo);
        $controller->create();
        break;

    case '/tickets/update':
        require_once __DIR__ . '/../controllers/TicketsController.php';
        $controller = new TicketsController($pdo);
        $controller->update();
        break;

    case '/tickets/delete':
        require_once __DIR__ . '/../controllers/TicketsController.php';
        $controller = new TicketsController($pdo);
        $controller->delete();
        break;

    case '/login':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->login();
        break;

    case '/logout':
        session_destroy();
        header('Location: /login');
        exit;
        break;

    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}