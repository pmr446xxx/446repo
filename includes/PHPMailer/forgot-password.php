<?php
// NAJPIERW ustaw język Z URL
if (!empty($_GET['lang'])) {
    $_SESSION['lang'] = strtoupper($_GET['lang']);
}

include 'includes/db.php';
include 'includes/lang.php';
include 'includes/mail-config.php';

if (isset($_SESSION['operator'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = t('fill_all_fields');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t('invalid_email');
    } else {
        // Sprawdź czy email istnieje
        $stmt = $pdo->prepare("SELECT id, username FROM operators WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $error = 'Email nie istnieje w systemie';
        } else {
            // Generuj token resetowania
            $resetToken = bin2hex(random_bytes(32));
            $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Zapisz token w bazie
            $stmt = $pdo->prepare("UPDATE operators SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?");
            $stmt->execute([$resetToken, $resetExpires, $user['id']]);
            
            // Wyślij email
            $resetLink = "http://localhost/reset-password.php?token=$resetToken";
            if (sendPasswordResetEmail($email, $user['username'], $resetToken, $resetLink)) {
                $success = 'Email z linkiem do resetowania hasła został wysłany!';
            } else {
                $error = 'Błąd przy wysyłaniu emaila. Spróbuj ponownie.';
            }
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.forgot-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.forgot-container {
    max-width: 450px;
    width: 100%;
    background-color: transparent;
    border: 2px solid #fbbf24;
    border-radius: 8px;
    padding: 40px;
}

.forgot-header {
    text-align: center;
    margin-bottom: 30px;
}

.forgot-logo {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.forgot-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
}

.forgot-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-error {
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
}

.alert-success {
    background-color: rgba(16, 185, 129, 0.1);
    border: 1px solid #10b981;
    color: #10b981;
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
    border: 1px solid #fbbf24;
    color: #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #fbbf24;
    box-shadow: 0 0 10px rgba(251, 191, 36, 0.3);
    background-color: #1a1f3a;
}

.btn-submit {
    width: 100%;
    padding: 12px;
    background-color: #fbbf24;
    color: #000;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-submit:hover {
    background-color: #f59e0b;
    box-shadow: 0 8px 16px rgba(251, 191, 36, 0.3);
}

.forgot-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid rgba(251, 191, 36, 0.2);
}

.forgot-footer-text {
    color: #9ca3af;
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.btn-back {
    display: inline-block;
    padding: 8px 16px;
    background-color: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background-color: #dc2626;
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

@media (max-width: 768px) {
    .forgot-container {
        padding: 25px;
    }

    .forgot-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="forgot-wrapper">
    <div class="forgot-container">
        <div class="forgot-header">
            <div class="forgot-logo">🔑</div>
            <div class="forgot-title">Resetowanie hasła</div>
            <div class="forgot-subtitle">Wprowadź swój email, aby otrzymać link resetowania</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                <br><br>
                <p style="color: #10b981; margin-top: 15px; font-size: 0.9rem;">Sprawdź swój email i kliknij link, aby zresetować hasło.</p>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fa-solid fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required autofocus>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Wyślij link resetowania
                </button>
            </form>
        <?php endif; ?>

        <div class="forgot-footer">
            <div class="forgot-footer-text">Pamiętasz hasło?</div>
            <a href="login.php?lang=<?= htmlspecialchars($current_lang) ?>" class="btn-back">
                <i class="fa-solid fa-sign-in-alt"></i> Zaloguj się
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>