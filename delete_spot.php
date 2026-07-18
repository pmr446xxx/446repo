<?php
declare(strict_types=1);

if (!isset($_SESSION['operator'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';
include 'includes/lang.php';

$spotId = (int)($_GET['id'] ?? 0);
$currentOperator = $_SESSION['operator'];

if (!$spotId) {
    header('Location: my_spots.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM spots WHERE id = ?");
$stmt->execute([$spotId]);
$spot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$spot) {
    header('Location: my_spots.php');
    exit;
}

$isAdmin = $currentOperator === 'admin';
$isOwner = $currentOperator === $spot['operator'];

if (!$isAdmin && !$isOwner) {
    header('Location: my_spots.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM spots WHERE id = ?");
if ($stmt->execute([$spotId])) {
    header('Location: my_spots.php?deleted=1');
} else {
    header('Location: my_spots.php?error=1');
}
exit;