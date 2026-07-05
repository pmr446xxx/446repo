<?php
session_start();

require_once 'includes/db.php';

if (!isset($_SESSION['operator'])) {
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $operator = $_SESSION['operator'];

    $correspondent = trim($_POST['correspondent'] ?? '');
    $channel = (int)($_POST['channel'] ?? 1);
    $call_text = trim($_POST['call_text'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $location_from = trim($_POST['location_from'] ?? '');
    $location_to = trim($_POST['location_to'] ?? '');

    if ($correspondent == '') {
        $errors[] = 'Podaj znak korespondenta.';
    }

    if ($call_text == '') {
        $errors[] = 'Podaj wywołanie.';
    }

    if ($location_from == '') {
        $errors[] = 'Podaj swoje miasto.';
    }

    if ($location_to == '') {
        $errors[] = 'Podaj miasto korespondenta.';
    }

    /* tymczasowo odległość ustawiamy na 0 km
       w następnym etapie będzie liczona automatycznie */

    $distance_km = 0;

    if (count($errors) == 0) {

        $stmt = $pdo->prepare("
            INSERT INTO spots
            (
                operator,
                correspondent,
                channel,
                call_text,
                comment,
                location_from,
                location_to,
                distance_km
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $operator,
            $correspondent,
            $channel,
            $call_text,
            $comment,
            $location_from,
            $location_to,
            $distance_km
        ]);

        header("Location: index.php");
        exit;
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mt-4">

<div class="panel">

<div class="panel-title">
📡 Dodaj nowy spot
</div>

<div class="panel-body">

<?php
if (!empty($errors)) {
    echo '<div class="alert alert-danger">';
    foreach ($errors as $e) {
        echo $e . "<br>";
    }
    echo '</div>';
}
?>

<form method="post">
<div class="mb-3">
    <label class="form-label">👤 Korespondent</label>
    <input
        type="text"
        name="correspondent"
        class="form-control"
        placeholder="np. PMR002"
        required>
</div>

<div class="mb-3">
    <label class="form-label">📻 Kanał</label>

    <select name="channel" class="form-select">

<?php
for($i=1;$i<=16;$i++){
    echo "<option value='$i'>CH $i</option>";
}
?>

    </select>
</div>

<div class="mb-3">
    <label class="form-label">📡 Wywołanie</label>

    <input
        type="text"
        name="call_text"
        class="form-control"
        placeholder="np. CQ DX"
        required>
</div>

<div class="mb-3">
    <label class="form-label">💬 Komentarz</label>

    <textarea
        name="comment"
        rows="4"
        class="form-control"
        placeholder="np. Łączność bardzo dobra..."></textarea>
</div>

<div class="row">

<div class="col-md-6">

<div class="mb-3">
<label class="form-label">📍 Moje miasto</label>

<input
type="text"
name="location_from"
class="form-control"
placeholder="np. Elbląg"
required>

</div>

</div>

<div class="col-md-6">

<div class="mb-3">
<label class="form-label">📍 Miasto korespondenta</label>

<input
type="text"
name="location_to"
class="form-control"
placeholder="np. Gdańsk"
required>

</div>

</div>

</div>

<button
type="submit"
class="btn btn-warning w-100">

📡 Dodaj spot

</button></form>

</div>

</div>

</div>

<?php include 'includes/footer.php'; ?>