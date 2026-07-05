<?php

require_once 'includes/db.php';

$stmt = $pdo->query("
    SELECT *
    FROM spots
    ORDER BY id DESC
    LIMIT 10
");

$spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($spots as $s) {

    echo "
    <div class='live-row'>
        <div class='live-op'>🟢 ".($s['operator'] ?? 'UNKNOWN')."</div>
        <div class='live-title'>📡 ".($s['call'] ?? '')."</div>
        <div class='live-msg'>".($s['message'] ?? '')."</div>
        <div class='live-loc'>📍 ".($s['location'] ?? '')."</div>
    </div>
    ";

}
?>