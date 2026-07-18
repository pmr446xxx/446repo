<?php
declare(strict_types=1);

if (!isset($_SESSION['operator'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';
include 'includes/lang.php';

$operator = $_SESSION['operator'];
$spotId = (int)($_GET['id'] ?? 0);

if (!$spotId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM spots WHERE id = ?");
$stmt->execute([$spotId]);
$spot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$spot) {
    header('Location: index.php');
    exit;
}

// Sprawdzenie uprawnień
$isAdmin = $operator === 'admin';
$isOwner = $operator === $spot['operator'];

if (!$isAdmin && !$isOwner) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correspondent = trim($_POST['correspondent'] ?? '');
    $channel = (int)($_POST['channel'] ?? 0);
    $location_from = trim($_POST['location_from'] ?? '');
    $location_to = trim($_POST['location_to'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if (empty($correspondent) || empty($location_from) || empty($location_to) || $channel < 1) {
        $error = 'Wszystkie pola są wymagane i poprawne';
    } else {
        $stmt = $pdo->prepare("
            UPDATE spots 
            SET correspondent = ?, channel = ?, location_from = ?, location_to = ?, comment = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$correspondent, $channel, $location_from, $location_to, $comment, $spotId])) {
            $message = 'Spot został zaktualizowany';
            $spot['correspondent'] = $correspondent;
            $spot['channel'] = $channel;
            $spot['location_from'] = $location_from;
            $spot['location_to'] = $location_to;
            $spot['comment'] = $comment;
        } else {
            $error = 'Błąd aktualizacji spotu';
        }
    }
}

// Header i navbar już załadowane w index.php
?>

<style>
.edit-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
}

.edit-container {
    max-width: 700px;
    margin: 0 auto;
}

.edit-header {
    margin-bottom: 30px;
}

.edit-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
}

.edit-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-success {
    background-color: rgba(16, 185, 129, 0.1);
    border: 1px solid #10b981;
    color: #10b981;
}

.alert-error {
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
}

.edit-panel {
    background-color: transparent;
    border: 2px solid #ff3b3b;
    border-radius: 8px;
    padding: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #e5e7eb;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    background-color: #1a1f3a;
    border: 1px solid #ff3b3b;
    color: #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #ff3b3b;
    box-shadow: 0 0 10px rgba(255, 59, 59, 0.3);
    background-color: #1a1f3a;
}

.form-input:disabled {
    background-color: #0f1419;
    border-color: #6b7280;
    color: #9ca3af;
    cursor: not-allowed;
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    background-color: #1a1f3a;
    border: 1px solid #ff3b3b;
    color: #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff3b3b;
    box-shadow: 0 0 10px rgba(255, 59, 59, 0.3);
    background-color: #1a1f3a;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    padding: 12px;
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background-color: #059669;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.btn-cancel {
    flex: 1;
    padding: 12px;
    background-color: #6b7280;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel:hover {
    background-color: #4b5563;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.readonly-note {
    font-size: 0.85rem;
    color: #9ca3af;
    margin-top: 5px;
    font-style: italic;
}

@media (max-width: 768px) {
    .edit-wrapper {
        padding: 15px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .btn-group {
        flex-direction: column;
    }
}
</style>

<div class="edit-wrapper">
    <div class="edit-container">
        <div class="edit-header">
            <div class="edit-title">
                <i class="fa-solid fa-pen-to-square"></i> Edytuj spot #<?= (int)$spot['id'] ?>
            </div>
            <div class="edit-subtitle">Operator: <?= htmlspecialchars($spot['operator']) ?></div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="edit-panel">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="correspondent">
                        <i class="fa-solid fa-user"></i> Korespondent
                    </label>
                    <input type="text" id="correspondent" name="correspondent" class="form-input" value="<?= htmlspecialchars($spot['correspondent'] ?? '') ?>" placeholder="Imię i nazwisko" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="channel">
                            <i class="fa-solid fa-tower-broadcast"></i> Kanał
                        </label>
                        <input type="number" id="channel" name="channel" class="form-input" value="<?= (int)($spot['channel'] ?? 0) ?>" placeholder="1-16" min="1" max="16" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="distance_km">
                            <i class="fa-solid fa-road"></i> Dystans (km)
                        </label>
                        <input type="number" id="distance_km" name="distance_km" class="form-input" value="<?= (int)($spot['distance_km'] ?? 0) ?>" disabled>
                        <div class="readonly-note">ℹ️ Pole tylko do odczytu (z Live spoty)</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="location_from">
                            <i class="fa-solid fa-map-pin"></i> Miasto FROM
                        </label>
                        <input type="text" id="location_from" name="location_from" class="form-input" value="<?= htmlspecialchars($spot['location_from'] ?? '') ?>" placeholder="Warszawa" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="location_to">
                            <i class="fa-solid fa-map-pin"></i> Miasto TO
                        </label>
                        <input type="text" id="location_to" name="location_to" class="form-input" value="<?= htmlspecialchars($spot['location_to'] ?? '') ?>" placeholder="Gdańsk" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="comment">
                        <i class="fa-solid fa-comment"></i> Komentarz (opcjonalnie)
                    </label>
                    <textarea id="comment" name="comment" class="form-textarea" placeholder="Dodatkowe informacje..."><?= htmlspecialchars($spot['comment'] ?? '') ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz zmiany
                    </button>
                    <a href="index.php" class="btn-cancel">
                        <i class="fa-solid fa-times"></i> Anuluj
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>