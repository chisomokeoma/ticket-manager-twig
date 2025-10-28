<?php
class TicketsController {
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

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Get all tickets
        $stmt = $this->pdo->prepare("
            SELECT * FROM tickets 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $tickets = $stmt->fetchAll();

        // Check for messages
        $successMessage = $_SESSION['success'] ?? null;
        $errorMessage = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        echo $this->twig->render('tickets.twig', [
            'tickets' => $tickets,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage
        ]);
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tickets');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Validate input
        $errors = [];
        
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 3) {
            $errors[] = 'Title must be at least 3 characters';
        }
        
        if (!in_array($_POST['status'], ['open', 'in_progress', 'closed'])) {
            $errors[] = 'Invalid status';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: /tickets');
            exit;
        }

        // Insert ticket
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO tickets (
                    title, description, status, priority, company, 
                    employee, start_date, end_date, days_left, user_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                trim($_POST['title']),
                trim($_POST['description'] ?? ''),
                $_POST['status'],
                $_POST['priority'] ?? 'medium',
                trim($_POST['company'] ?? ''),
                trim($_POST['employee'] ?? ''),
                !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                0, // days_left
                $userId
            ]);

            $_SESSION['success'] = 'Ticket created successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to create ticket: ' . $e->getMessage();
        }

        header('Location: /tickets');
        exit;
    }

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tickets');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $ticketId = $_POST['ticket_id'] ?? null;

        if (!$ticketId) {
            $_SESSION['error'] = 'Invalid ticket ID';
            header('Location: /tickets');
            exit;
        }

        // Validate input
        $errors = [];
        
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 3) {
            $errors[] = 'Title must be at least 3 characters';
        }
        
        if (!in_array($_POST['status'], ['open', 'in_progress', 'closed'])) {
            $errors[] = 'Invalid status';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: /tickets');
            exit;
        }

        // Update ticket
        try {
            $stmt = $this->pdo->prepare("
                UPDATE tickets SET 
                    title = ?, 
                    description = ?, 
                    status = ?, 
                    priority = ?, 
                    company = ?, 
                    employee = ?, 
                    start_date = ?, 
                    end_date = ?
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([
                trim($_POST['title']),
                trim($_POST['description'] ?? ''),
                $_POST['status'],
                $_POST['priority'] ?? 'medium',
                trim($_POST['company'] ?? ''),
                trim($_POST['employee'] ?? ''),
                !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                $ticketId,
                $userId
            ]);

            $_SESSION['success'] = 'Ticket updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update ticket: ' . $e->getMessage();
        }

        header('Location: /tickets');
        exit;
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $ticketId = $_POST['ticket_id'] ?? $_GET['id'] ?? null;

        if (!$ticketId) {
            $_SESSION['error'] = 'Invalid ticket ID';
            header('Location: /tickets');
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM tickets 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$ticketId, $userId]);

            $_SESSION['success'] = 'Ticket deleted successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to delete ticket: ' . $e->getMessage();
        }

        header('Location: /tickets');
        exit;
    }
}