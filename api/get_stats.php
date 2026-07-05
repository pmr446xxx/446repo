<?php
header('Content-Type: application/json');

include '../includes/db.php';

try {
    // Liczba operatorów online (ostatnia aktywność w ciągu ostatnich 30 minut)
    $stmt = $pdo->query("SELECT COUNT(DISTINCT operator) as count FROM spots WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    $online = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Liczba wszystkich spot'ów dzisiaj
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM spots WHERE DATE(created_at) = CURDATE()");
    $today_spots = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Liczba spot'ów w tym miesiącu
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM spots WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
    $month_spots = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Liczba aktywnych kanałów
    $stmt = $pdo->query("SELECT COUNT(DISTINCT channel) as count FROM spots WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $active_channels = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Liczba unikalnych lokacji
    $stmt = $pdo->query("SELECT COUNT(DISTINCT location_from) as count FROM spots WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $active_locations = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // TOP operatorzy dzisiaj
    $stmt = $pdo->query("
        SELECT operator, COUNT(*) as count 
        FROM spots 
        WHERE DATE(created_at) = CURDATE() 
        GROUP BY operator 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $top_operators = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'online_operators' => $online,
        'today_spots' => $today_spots,
        'month_spots' => $month_spots,
        'active_channels' => $active_channels,
        'active_locations' => $active_locations,
        'top_operators' => $top_operators
    ]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
