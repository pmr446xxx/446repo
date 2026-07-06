<?php
header('Content-Type: application/json');

include '../includes/db.php';

try {
    $today = date('Y-m-d');
    $month = date('Y-m');

    // Spoty dzisiaj
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM spots WHERE DATE(created_at) = ?");
    $stmt->execute([$today]);
    $today_spots = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Spoty w tym miesiącu
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM spots WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $month_spots = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Operatorzy online
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT operator) as count 
        FROM spots 
        WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $online_operators = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Top channel
    $stmt = $pdo->query("
        SELECT channel, COUNT(*) as count
        FROM spots
        WHERE DATE(created_at) = CURDATE()
        GROUP BY channel
        ORDER BY count DESC
        LIMIT 1
    ");
    $top_ch = $stmt->fetch(PDO::FETCH_ASSOC);
    $top_channel = $top_ch ? 'CH ' . (int)$top_ch['channel'] : '-';

    // Ostatni spot - czas w formacie "2h 15m temu"
    $stmt = $pdo->query("SELECT created_at FROM spots ORDER BY id DESC LIMIT 1");
    $lastSpot = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $lastSpotAgo = 'brak';
    if ($lastSpot) {
        $now = new DateTime();
        $created = new DateTime($lastSpot['created_at']);
        $diff = $now->diff($created);
        
        if ($diff->days > 0) {
            $lastSpotAgo = $diff->days . "d " . $diff->h . "h temu";
        } elseif ($diff->h > 0) {
            $lastSpotAgo = $diff->h . "h " . $diff->i . "m temu";
        } else {
            $lastSpotAgo = $diff->i . "m temu";
        }
    }

    echo json_encode([
        'success' => true,
        'spots_today' => (int)$today_spots,
        'spots_month' => (int)$month_spots,
        'operators_today' => (int)$online_operators,
        'top_channel' => $top_channel,
        'last_spot_ago' => $lastSpotAgo
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
