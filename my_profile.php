<?php
declare(strict_types=1);

if (!isset($_SESSION['operator'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';
include 'includes/lang.php';

$operator = $_SESSION['operator'];

$stmt = $pdo->prepare("SELECT * FROM operators WHERE operator = ?");
$stmt->execute([$operator]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $call_sign = trim($_POST['call_sign'] ?? '');
    $qth = trim($_POST['qth'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($call_sign) || empty($qth) || empty($email)) {
        $error = t('all_fields_required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t('invalid_email');
    } else {
        $stmt = $pdo->prepare("UPDATE operators SET call_sign = ?, qth = ?, email = ? WHERE operator = ?");
        if ($stmt->execute([$call_sign, $qth, $email, $operator])) {
            $message = t('profile_updated');
            $user['call_sign'] = $call_sign;
            $user['qth'] = $qth;
            $user['email'] = $email;
        } else {
            $error = t('profile_update_error');
        }
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) as spot_count FROM spots WHERE operator = ?");
$stmt->execute([$operator]);
$spotCount = $stmt->fetch(PDO::FETCH_ASSOC)['spot_count'] ?? 0;

// Header i navbar już załadowane w index.php
?>

<style>
.profile-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
}

.profile-container {
    max-width: 600px;
    margin: 0 auto;
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 900;
    color: white;
    margin: 0 auto 20px;
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

.profile-username {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.profile-joindate {
    font-size: 0.9rem;
    color: #9ca3af;
}

.profile-panel {
    background-color: transparent;
    border: 2px solid #ff3b3b;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
}

.profile-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #ff3b3b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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

.btn-save {
    width: 100%;
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

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
}

.stat-box {
    background-color: rgba(59, 130, 246, 0.1);
    border: 1px solid #3b82f6;
    border-radius: 4px;
    padding: 15px;
    text-align: center;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 900;
    color: #3b82f6;
}

.stat-label {
    font-size: 0.9rem;
    color: #9ca3af;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .profile-wrapper {
        padding: 15px;
    }

    .profile-panel {
        padding: 15px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="profile-wrapper">
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?= strtoupper(substr($operator, 0, 1)) ?>
            </div>
            <div class="profile-username"><?= htmlspecialchars($operator) ?></div>
            <div class="profile-joindate">
                <i class="fa-solid fa-calendar"></i> <?= t('member_since') ?>: <?= date('d.m.Y', strtotime($user['created_at'])) ?>
            </div>
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

        <div class="profile-panel">
            <div class="profile-title">
                <i class="fa-solid fa-pen-to-square"></i> <?= t('edit_profile') ?>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="call_sign">
                        <i class="fa-solid fa-signature"></i> <?= t('call_sign') ?>
                    </label>
                    <input type="text" id="call_sign" name="call_sign" class="form-input" value="<?= htmlspecialchars($user['call_sign'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="qth">
                        <i class="fa-solid fa-map-pin"></i> <?= t('qth') ?>
                    </label>
                    <input type="text" id="qth" name="qth" class="form-input" value="<?= htmlspecialchars($user['qth'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fa-solid fa-envelope"></i> <?= t('email') ?>
                    </label>
                    <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> <?= t('save_profile') ?>
                </button>
            </form>
        </div>

        <div class="profile-panel">
            <div class="profile-title">
                <i class="fa-solid fa-chart-bar"></i> <?= t('statistics') ?>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number"><?= $spotCount ?></div>
                    <div class="stat-label"><?= t('spots') ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?= date('Y', strtotime($user['created_at'])) ?></div>
                    <div class="stat-label"><?= t('registration_year') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
