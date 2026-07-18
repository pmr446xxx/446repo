<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'includes/lang.php';
require_once 'includes/geolocation.php';

if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correspondent = trim($_POST['correspondent'] ?? '');
    $channel = (int)($_POST['channel'] ?? 0);
    $location_from = trim($_POST['location_from'] ?? '');
    $location_to = trim($_POST['location_to'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $call_text = trim($_POST['call_text'] ?? '');

    // Walidacja
    if (empty($correspondent) || empty($location_from) || empty($location_to) || $channel < 1 || $channel > 16) {
        $error = 'Wypełnij wszystkie pola!';
    } else {
        // Oblicz odległość
        $distance_km = GeoLocation::calculateDistance($pdo, $location_from, $location_to);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO spots (operator, correspondent, channel, location_from, location_to, distance_km, comment, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([
                $_SESSION['operator'],
                $correspondent,
                $channel,
                $location_from,
                $location_to,
                $distance_km,
                $comment
            ])) {
                $success = 'Spot dodany pomyślnie! ✅';
                // Czyszczenie formularza
                $_POST = [];
            } else {
                $error = 'Błąd przy dodawaniu spotu!';
            }
        } catch (Throwable $e) {
            $error = 'Błąd bazy danych: ' . $e->getMessage();
        }
    }
}

// Nie include header/navbar tutaj - są już w index.php
?>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html, body { background: #000 !important; }

.spot-add-wrapper {
    width: 100%;
    background: #000 !important;
    padding: 30px 20px;
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
}

.spot-add-container {
    max-width: 600px;
    width: 100%;
    background: transparent;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 30px;
    margin-top: 20px;
}

.spot-add-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid rgba(251, 191, 36, 0.3);
}

.spot-add-header i {
    color: #22c55e;
    font-size: 1.8rem;
}

.spot-add-title {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 900;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid #22c55e;
    color: #22c55e;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
}

.alert i {
    font-size: 1.2rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #fbbf24;
    font-weight: 700;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 15px;
    background: #1a1f3a;
    border: 1px solid #fbbf24;
    color: #fff;
    border-radius: 6px;
    font-size: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #22c55e;
    background: #1a1f3a;
    box-shadow: 0 0 12px rgba(34, 197, 94, 0.3);
}

.form-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.form-select option {
    background: #1a1f3a;
    color: #fff;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 900;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
}

.btn-submit:hover {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-submit i {
    font-size: 1.1rem;
}

.btn-cancel {
    width: 100%;
    padding: 12px;
    background: transparent;
    color: #fbbf24;
    border: 1px solid #fbbf24;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-cancel:hover {
    background: rgba(251, 191, 36, 0.1);
}

.info-box {
    background: rgba(251, 191, 36, 0.05);
    border: 1px solid rgba(251, 191, 36, 0.3);
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
    color: #fbbf24;
    font-size: 0.9rem;
    line-height: 1.5;
}

.info-box i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .spot-add-wrapper {
        padding: 15px;
    }

    .spot-add-container {
        padding: 20px;
    }

    .spot-add-title {
        font-size: 1.2rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="spot-add-wrapper">
    <div class="spot-add-container">
        <div class="spot-add-header">
            <i class="fa-solid fa-plus"></i>
            <span class="spot-add-title">Dodaj Spot</span>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fa-solid fa-info-circle"></i>
            Wypełnij wszystkie pola aby dodać nowy spot. Odległość będzie obliczona automatycznie.
        </div>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Korespondent</label>
                <input 
                    type="text" 
                    name="correspondent" 
                    class="form-input" 
                    placeholder="np. SS5, FGB, PMR044"
                    value="<?= htmlspecialchars($_POST['correspondent'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Kanał</label>
                <select name="channel" class="form-select" required>
                    <option value="">-- Wybierz kanał --</option>
                    <?php for ($i = 1; $i <= 16; $i++): ?>
                        <option value="<?= $i ?>" <?= (isset($_POST['channel']) && $_POST['channel'] == $i) ? 'selected' : '' ?>>
                            CH <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Miasto FROM</label>
                    <input 
                        type="text" 
                        name="location_from" 
                        class="form-input" 
                        placeholder="np. Warszawa"
                        value="<?= htmlspecialchars($_POST['location_from'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Miasto TO</label>
                    <input 
                        type="text" 
                        name="location_to" 
                        class="form-input" 
                        placeholder="np. Kraków"
                        value="<?= htmlspecialchars($_POST['location_to'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Komentarz</label>
                <textarea 
                    name="comment" 
                    class="form-textarea" 
                    placeholder="Dodaj komentarz (opcjonalnie)"
                ><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-plus"></i> Dodaj Spot
            </button>

            <a href="index.php" class="btn-cancel">
                <i class="fa-solid fa-arrow-left"></i> Wróć
            </a>
        </form>
    </div>
</div>
