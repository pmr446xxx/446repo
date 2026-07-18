<?php
// NAJPIERW ustaw język Z URL
if (!empty($_GET['lang'])) {
    $_SESSION['lang'] = strtoupper($_GET['lang']);
}

include 'includes/db.php';
include 'includes/lang.php';

if (isset($_SESSION['operator'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$current_lang = $_SESSION['lang'];
$token = $_GET['token'] ?? '';
$user = null;

if (empty($token)) {
    $error = 'Token resetowania nie znaleziony';
} else {
    // Sprawdź token
    $stmt = $pdo->prepare("SELECT id, username, email FROM operators WHERE password_reset_token = ? AND password_reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $error = 'Link resetowania wygasł lub jest nieprawidłowy';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    
    if (empty($password) || empty($password_confirm)) {
        $error = 'Uzupełnij wszystkie pola';
    } elseif (strlen($password) < 8) {
        $error = 'Hasło musi mieć co najmniej 8 znaków';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'Hasło musi zawierać przynajmniej jedną DUŻĄ literę';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = 'Hasło musi zawierać przynajmniej jedną małą literę';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'Hasło musi zawierać przynajmniej jedną cyfrę';
    } elseif ($password !== $password_confirm) {
        $error = 'Hasła nie są takie same';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Zaktualizuj hasło
        $stmt = $pdo->prepare("UPDATE operators SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        
        $success = 'Hasło zostało pomyślnie zmienione! Możesz się teraz zalogować.';
        $user = null; // Ukryj formularz
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.reset-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.reset-container {
    max-width: 450px;
    width: 100%;
    background-color: transparent;
    border: 2px solid #10b981;
    border-radius: 8px;
    padding: 40px;
}

.reset-header {
    text-align: center;
    margin-bottom: 30px;
}

.reset-logo {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.reset-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
}

.reset-subtitle {
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

.password-requirements {
    background-color: rgba(16, 185, 129, 0.05);
    border: 1px solid #10b981;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    color: #9ca3af;
}

.requirement {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.requirement:last-child {
    margin-bottom: 0;
}

.requirement-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    font-weight: bold;
}

.requirement-icon.incomplete {
    background-color: rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.requirement-icon.complete {
    background-color: rgba(16, 185, 129, 0.3);
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
    border: 1px solid #10b981;
    color: #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
    background-color: #1a1f3a;
}

.btn-reset {
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
    margin-top: 10px;
}

.btn-reset:hover {
    background-color: #059669;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.reset-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid rgba(16, 185, 129, 0.2);
}

.reset-footer-text {
    color: #9ca3af;
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.btn-login {
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

.btn-login:hover {
    background-color: #dc2626;
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

@media (max-width: 768px) {
    .reset-container {
        padding: 25px;
    }

    .reset-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="reset-wrapper">
    <div class="reset-container">
        <div class="reset-header">
            <div class="reset-logo">🔐</div>
            <div class="reset-title">Ustawianie nowego hasła</div>
            <div class="reset-subtitle">Ustawienie nowego hasła do Twojego konta</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                <br><br>
                <a href="forgot-password.php" style="color: #ef4444; text-decoration: underline;">Spróbuj ponownie</a>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                <br><br>
                <a href="login.php" class="btn-login" style="display: inline-block; margin-top: 10px;">
                    Zaloguj się
                </a>
            </div>
        <?php elseif ($user): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fa-solid fa-lock"></i> Nowe hasło
                    </label>
                    <input type="password" id="password" name="password" class="form-input" required onkeyup="checkPassword()" onfocus="clearField(this)">
                    
                    <div class="password-requirements">
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-length">✗</span>
                            <span>Minimum 8 znaków</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-upper">✗</span>
                            <span>Przynajmniej jedna DUŻA litera (A-Z)</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-lower">✗</span>
                            <span>Przynajmniej jedna mała litera (a-z)</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-digit">✗</span>
                            <span>Przynajmniej jedna cyfra (0-9)</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirm">
                        <i class="fa-solid fa-lock-open"></i> Powtórz hasło
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" required onfocus="clearField(this)">
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fa-solid fa-check"></i> Ustaw nowe hasło
                </button>
            </form>
        <?php endif; ?>

        <div class="reset-footer">
            <div class="reset-footer-text">Wróć do logowania</div>
            <a href="login.php" class="btn-login">
                <i class="fa-solid fa-sign-in-alt"></i> Zaloguj się
            </a>
        </div>
    </div>
</div>

<script>
function clearField(field) {
    setTimeout(() => {
        if (field.value.length === 0 || /^•+$/.test(field.value)) {
            field.value = '';
        }
    }, 0);
}

function checkPassword() {
    const password = document.getElementById('password').value;
    
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasDigit = /[0-9]/.test(password);
    
    updateRequirement('length', hasLength);
    updateRequirement('upper', hasUpper);
    updateRequirement('lower', hasLower);
    updateRequirement('digit', hasDigit);
}

function updateRequirement(type, isComplete) {
    const icon = document.getElementById('icon-' + type);
    if (isComplete) {
        icon.classList.remove('incomplete');
        icon.classList.add('complete');
        icon.textContent = '✓';
    } else {
        icon.classList.remove('complete');
        icon.classList.add('incomplete');
        icon.textContent = '✗';
    }
}

window.addEventListener('load', function() {
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
});

setTimeout(() => {
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
}, 100);
</script>

<?php include 'includes/footer.php'; ?>