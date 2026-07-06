<?php
session_start();

if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';
require_once 'includes/lang.php';

$operator = htmlspecialchars($_SESSION['operator']);
$errors = [];
$success = false;

// Pobierz dane operatora
$stmt = $pdo->prepare("SELECT * FROM operators WHERE operator = ?");
$stmt->execute([$operator]);
$operatorData = $stmt->fetch(PDO::FETCH_ASSOC);

// POST - Edycja profilu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $call_sign = trim($_POST['call_sign'] ?? '');
    $qth = trim($_POST['qth'] ?? '');
    $equipment = trim($_POST['equipment'] ?? '');
    
    if (empty($call_sign)) {
        $errors[] = $lang === 'en' ? 'Call sign is required' : 'Znak jest wymagany';
    }
    if (empty($qth)) {
        $errors[] = $lang === 'en' ? 'QTH is required' : 'QTH jest wymagane';
    }
    if (empty($equipment)) {
        $errors[] = $lang === 'en' ? 'Equipment is required' : 'Sprzęt jest wymagany';
    }
    
    if (count($errors) == 0) {
        $stmt = $pdo->prepare("
            UPDATE operators 
            SET call_sign = ?, qth = ?, equipment = ? 
            WHERE operator = ?
        ");
        $stmt->execute([$call_sign, $qth, $equipment, $operator]);
        
        $success = true;
        $operatorData['call_sign'] = $call_sign;
        $operatorData['qth'] = $qth;
        $operatorData['equipment'] = $equipment;
    }
}

// Liczba spotów
$stmtToday = $pdo->prepare("SELECT COUNT(*) as count FROM spots WHERE operator = ? AND DATE(created_at) = CURDATE()");
$stmtToday->execute([$operator]);
$spotsToday = $stmtToday->fetch(PDO::FETCH_ASSOC)['count'];

$stmtWeek = $pdo->prepare("SELECT COUNT(*) as count FROM spots WHERE operator = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmtWeek->execute([$operator]);
$spotsWeek = $stmtWeek->fetch(PDO::FETCH_ASSOC)['count'];

$stmtMonth = $pdo->prepare("SELECT COUNT(*) as count FROM spots WHERE operator = ? AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')");
$stmtMonth->execute([$operator]);
$spotsMonth = $stmtMonth->fetch(PDO::FETCH_ASSOC)['count'];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container-fluid mt-4">
    <div class="row g-3">
        <!-- PROFIL -->
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-id-card"></i>
                    <span><?= t('my_profile') ?></span>
                </div>

                <div class="panel-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-check-circle"></i>
                            <?= $lang === 'en' ? 'Profile updated successfully!' : 'Profil zaktualizowany pomyślnie!' ?>
                        </div>
                    <?php endif; ?>

                    <?php if (count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $e): ?>
                                <div><i class="fa-solid fa-exclamation-circle"></i> <?= $e ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fa-solid fa-user"></i>
                                    <?= $lang === 'en' ? 'Operator' : 'Operator' ?>
                                </label>
                                <input type="text" class="form-control" value="<?= $operator ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fa-solid fa-microphone"></i>
                                    <?= $lang === 'en' ? 'Call Sign' : 'Znak' ?>
                                </label>
                                <input type="text" name="call_sign" class="form-control" 
                                       value="<?= htmlspecialchars($operatorData['call_sign'] ?? '') ?>" required>
                                <small class="text-secondary">np. 161RC123</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= $lang === 'en' ? 'QTH' : 'QTH' ?>
                                </label>
                                <input type="text" name="qth" class="form-control" 
                                       value="<?= htmlspecialchars($operatorData['qth'] ?? '') ?>" required>
                                <small class="text-secondary">np. Kraków, Polska</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fa-solid fa-microchip"></i>
                                    <?= $lang === 'en' ? 'Equipment' : 'Sprzęt' ?>
                                </label>
                                <input type="text" name="equipment" class="form-control" 
                                       value="<?= htmlspecialchars($operatorData['equipment'] ?? '') ?>" required>
                                <small class="text-secondary">np. Baofeng UV-5R + GP340</small>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mb-3">
                                <i class="fa-solid fa-save"></i>
                                <?= $lang === 'en' ? 'Save Profile' : 'Zapisz profil' ?>
                            </button>
                        </div>

                        <div style="width: 200px; text-align: center; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 50px; color: white; font-weight: bold;">
                                <?= strtoupper(substr($operator, 0, 1)) ?>
                            </div>
                            <h5><?= $operator ?></h5>
                            <small class="text-secondary"><?= $operatorData['email'] ?></small>
                            <div style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                                <?= $lang === 'en' ? 'Member since' : 'Członek od' ?>: 
                                <?= date('d.m.Y', strtotime($operatorData['created_at'])) ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- STATYSTYKI -->
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-chart-bar"></i>
                    <span><?= $lang === 'en' ? 'My Statistics' : 'Moje statystyki' ?></span>
                </div>

                <div class="panel-body">
                    <div style="display: grid; gap: 15px;">
                        <div style="padding: 15px; background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e; border-radius: 8px;">
                            <div style="font-size: 12px; color: #9ca3af; margin-bottom: 5px;">
                                <?= $lang === 'en' ? 'Today' : 'Dzisiaj' ?>
                            </div>
                            <div style="font-size: 28px; font-weight: bold; color: #22c55e;">
                                <?= $spotsToday ?>
                            </div>
                            <div style="font-size: 12px; color: #9ca3af;">
                                <?= $lang === 'en' ? 'spots' : 'spotów' ?>
                            </div>
                        </div>

                        <div style="padding: 15px; background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; border-radius: 8px;">
                            <div style="font-size: 12px; color: #9ca3af; margin-bottom: 5px;">
                                <?= $lang === 'en' ? 'This Week' : 'Ten tydzień' ?>
                            </div>
                            <div style="font-size: 28px; font-weight: bold; color: #3b82f6;">
                                <?= $spotsWeek ?>
                            </div>
                            <div style="font-size: 12px; color: #9ca3af;">
                                <?= $lang === 'en' ? 'spots' : 'spotów' ?>
                            </div>
                        </div>

                        <div style="padding: 15px; background: rgba(251, 191, 36, 0.1); border-left: 4px solid #fbbf24; border-radius: 8px;">
                            <div style="font-size: 12px; color: #9ca3af; margin-bottom: 5px;">
                                <?= $lang === 'en' ? 'This Month' : 'Ten miesiąc' ?>
                            </div>
                            <div style="font-size: 28px; font-weight: bold; color: #fbbf24;">
                                <?= $spotsMonth ?>
                            </div>
                            <div style="font-size: 12px; color: #9ca3af;">
                                <?= $lang === 'en' ? 'spots' : 'spotów' ?>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; border-color: #333;">

                    <a href="my_spots.php" class="btn btn-primary w-100 mb-2">
                        <i class="fa-solid fa-list"></i>
                        <?= $lang === 'en' ? 'View All Spots' : 'Wyświetl wszystkie spoty' ?>
                    </a>

                    <a href="index.php" class="btn btn-secondary w-100">
                        <i class="fa-solid fa-arrow-left"></i>
                        <?= $lang === 'en' ? 'Back to Spots' : 'Wróć do spotów' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
