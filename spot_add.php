<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/lang.php';

if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit;
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return round($R * $c);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correspondent = $_POST['correspondent'] ?? '';
    $channel = (int)($_POST['channel'] ?? 0);
    $location_from = $_POST['location_from'] ?? '';
    $location_to = $_POST['location_to'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $call_text = $_POST['call_text'] ?? '';

    // Walidacja kanału
    if ($channel < 1 || $channel > 16) {
        $channel = 1;
    }

    // Pobierz współrzędne miast
    $stmt1 = $pdo->prepare("
        SELECT lat, lng FROM cities 
        WHERE LOWER(city_ascii) = LOWER(?) OR LOWER(city) = LOWER(?)
        LIMIT 1
    ");
    $stmt1->execute([$location_from, $location_from]);
    $from = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("
        SELECT lat, lng FROM cities 
        WHERE LOWER(city_ascii) = LOWER(?) OR LOWER(city) = LOWER(?)
        LIMIT 1
    ");
    $stmt2->execute([$location_to, $location_to]);
    $to = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($from && $to) {
        $distance_km = calculateDistance(
            (float)$from['lat'], (float)$from['lng'],
            (float)$to['lat'], (float)$to['lng']
        );
    } else {
        $distance_km = 0;
    }

    // Wstaw spot
    $stmt = $pdo->prepare("
        INSERT INTO spots (operator, correspondent, channel, location_from, location_to, distance_km, comment, call_text)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['operator'],
        $correspondent,
        $channel,
        $location_from,
        $location_to,
        $distance_km,
        $comment,
        $call_text
    ]);

    header("Location: index.php");
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-plus"></i> <?= t('add_spot') ?>
                </div>
                <div class="panel-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label"><?= t('correspondent_label') ?></label>
                            <input type="text" name="correspondent" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= t('channel_label') ?></label>
                            <select name="channel" class="form-control" required>
                                <option value=""><?= t('choose_channel') ?></option>
                                <option value="1">CH 1</option>
                                <option value="2">CH 2</option>
                                <option value="3">CH 3</option>
                                <option value="4">CH 4</option>
                                <option value="5">CH 5</option>
                                <option value="6">CH 6</option>
                                <option value="7">CH 7</option>
                                <option value="8">CH 8</option>
                                <option value="9">CH 9</option>
                                <option value="10">CH 10</option>
                                <option value="11">CH 11</option>
                                <option value="12">CH 12</option>
                                <option value="13">CH 13</option>
                                <option value="14">CH 14</option>
                                <option value="15">CH 15</option>
                                <option value="16">CH 16</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= t('city_from_label') ?></label>
                            <input type="text" name="location_from" class="form-control" placeholder="np. Warszawa" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= t('city_to_label') ?></label>
                            <input type="text" name="location_to" class="form-control" placeholder="np. Kraków" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= t('comment_label') ?></label>
                            <textarea name="comment" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= t('call_text_label') ?></label>
                            <input type="text" name="call_text" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-plus"></i> <?= t('add_spot_button') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>