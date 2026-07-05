<?php
header('Content-Type: application/json');

include '../includes/db.php';

try {
    $stmt = $pdo->query("
        SELECT *
        FROM spots
        ORDER BY id DESC
        LIMIT 50
    ");
    
    $spots = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spots[] = [
            'id' => $row['id'],
            'operator' => $row['operator'],
            'correspondent' => $row['correspondent'],
            'channel' => $row['channel'],
            'call_text' => $row['call_text'],
            'comment' => $row['comment'],
            'location_from' => $row['location_from'],
            'location_to' => $row['location_to'],
            'distance_km' => $row['distance_km'],
            'time' => date('H:i', strtotime($row['created_at']))
        ];
    }
    
    echo json_encode(['success' => true, 'spots' => $spots]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
