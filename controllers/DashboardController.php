<?php
class DashboardController {
    private $pdo;
    private $twig;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        // Initialize Twig
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false, // Set to '/path/to/cache' in production
            'debug' => true
        ]);
    }

    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Get user data
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        // Get all tickets for this user
        $stmt = $this->pdo->prepare("
            SELECT * FROM tickets 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $tickets = $stmt->fetchAll();

        // Calculate statistics
        $totalTickets = count($tickets);
        $openTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
        $inProgressTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress'));
        $closedTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'closed'));

        // Get recent tickets (last 4)
        $recentTickets = array_slice($tickets, 0, 4);

        // Calculate employee stats
        $employeeCounts = [];
        foreach ($tickets as $ticket) {
            $emp = $ticket['employee'] ?? 'Unknown';
            if (!isset($employeeCounts[$emp])) {
                $employeeCounts[$emp] = 0;
            }
            $employeeCounts[$emp]++;
        }
        
        arsort($employeeCounts);
        $topEmployees = [];
        $count = 0;
        foreach ($employeeCounts as $name => $ticketCount) {
            if ($count >= 3) break;
            $topEmployees[] = [
                'name' => $name,
                'count' => $ticketCount,
                'percentage' => $totalTickets > 0 ? round(($ticketCount / $totalTickets) * 100) : 0
            ];
            $count++;
        }

        // Calculate client stats
        $clientCounts = [];
        foreach ($tickets as $ticket) {
            $company = $ticket['company'] ?? 'Unknown';
            if (!isset($clientCounts[$company])) {
                $clientCounts[$company] = 0;
            }
            $clientCounts[$company]++;
        }
        
        arsort($clientCounts);
        $topClients = [];
        $count = 0;
        foreach ($clientCounts as $name => $ticketCount) {
            if ($count >= 3) break;
            $icon = ($name === 'McDonalds' || $name === 'Burger King') ? '🍔' : '🏢';
            $topClients[] = [
                'name' => $name,
                'count' => $ticketCount,
                'icon' => $icon,
                'color' => 'bg-orange-100'
            ];
            $count++;
        }

        // Check for success message
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        // Render template
        echo $this->twig->render('dashboard.twig', [
            'user' => $user,
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'closedTickets' => $closedTickets,
            'recentTickets' => $recentTickets,
            'topEmployees' => $topEmployees,
            'topClients' => $topClients,
            'successMessage' => $successMessage
        ]);
    }
}